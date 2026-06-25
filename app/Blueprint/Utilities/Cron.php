<?php

    namespace App\Blueprint\Utilities;

    use App\Blueprint\Datastore\SQLitePointer;

    class Cron extends SQLitePointer {

        private $tableName = "cron_tasks";

        public function __construct() {
            // Defines the permanent task registry directly in your SQLite file
            $schema = "
                CREATE TABLE IF NOT EXISTS {$this->tableName} (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    class_name TEXT NOT NULL,
                    method_name TEXT NOT NULL,
                    is_active INTEGER DEFAULT 1, -- 1 = Enabled, 0 = Disabled
                    last_status TEXT DEFAULT 'never_run',
                    updated_at INTEGER NOT NULL,
                    UNIQUE(class_name, method_name) -- Prevents duplicate entries
                )
            ";
            parent::__construct([$schema]);
        }

        /**
         * Executes all active cron tasks registered in the SQLite database.
         */
        public function run() {
            try {
                // 1. Fetch only active tasks from your SQLite database
                $stmt = $this->db->prepare("SELECT * FROM {$this->tableName} WHERE is_active = 1");
                $stmt->execute();
                $activeTasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                throw new \Exception("Database error pulling cron tasks: " . $e->getMessage());
            }

            if (empty($activeTasks)) {
                echo "No active cron tasks found in the database registry.\n";
                return false;
            }

            // 2. Loop through and execute your dynamic class methods
            foreach ($activeTasks as $task) {
                $className = $task['class_name'];
                $method    = $task['method_name'];
                $taskId    = $task['id'];

                if (class_exists($className)) {
                    $object = new $className();

                    if (method_exists($object, $method)) {
                        
                        // Run the actual job payload
                        $success = (bool) $object->$method();
                        $statusText = $success ? "success" : "failed";

                        echo $success 
                            ? "Successfully ran task: {$className}->{$method}()\n" 
                            : "Failed to run task: {$className}->{$method}()\n";

                        // 3. Update the execution history instantly in SQLite
                        $this->updateRow($this->tableName, 'id', $taskId, [
                            'last_status' => $statusText,
                            'updated_at'  => time()
                        ]);

                    } else {
                        echo "Error: Method '{$method}' not found in class '{$className}'\n";
                        $this->updateTaskError($taskId, "method_missing");
                    }
                } else {
                    echo "Error: Class '{$className}' not found.\n";
                    $this->updateTaskError($taskId, "class_missing");
                }
            }
            return true;
        }

        /**
         * Helper to manually add/register a new Cron job into your SQLite engine.
         */
        public function registerTask(string $className, string $methodName) {
            return $this->insertRow($this->tableName, [
                'class_name'  => $className,
                'method_name' => $methodName,
                'is_active'   => 1,
                'last_status' => 'registered',
                'updated_at'  => time()
            ]);
        }

        /**
         * Internal tracking updates for broken or missing code targets
         */
        private function updateTaskError($id, $errorType) {
            $this->updateRow($this->tableName, 'id', $id, [
                'last_status' => $errorType,
                'updated_at'  => time()
            ]);
        }
    }