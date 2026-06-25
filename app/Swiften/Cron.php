<?php

    namespace App\Swiften;

    class Cron {

        private $Package;

        public function __construct() {
            $this->Package = new \App\Blueprint\Utilities\Cron();
        }

        /**
         * Executes all active cron tasks
         * php swiften cron run
         */
        public function run() {
            echo "\nRunning cron functions...\n\n";
            $this->Package->run();
        }

        /**
         * Fetches all registered cron tasks and displays them in a clean terminal table
         * php swiften cron gettasks
         */
        public function getTasks() {
            echo "\nFetching cron tasks...\n";
            
            // SQLitePointer's getAllRows handles data collection natively
            $data = $this->Package->getAllRows("cron_tasks");
            
            if (!empty($data)) {
                echo sprintf("\n%-5s | %-40s | %-20s | %-8s | %s\n", "ID", "CLASS NAME", "METHOD NAME", "ACTIVE", "LAST STATUS");
                echo str_repeat("-", 95) . "\n";
                
                foreach ($data as $item) {
                    echo sprintf(
                        "%-5d | %-40s | %-20s | %-8s | %s\n",
                        $item['id'],
                        $item['class_name'],
                        $item['method_name'],
                        $item['is_active'] ? 'YES' : 'NO',
                        $item['last_status']
                    );
                }
            } else {
                echo "No tasks pending.";
            }

            echo "\n";
        }

        /**
         * Registers a new cron task configuration to the database store
         * php swiften cron registertask "App\Blueprint\Web\Dependencies" "fetch"
         */
        public function registerTask($name = null, $method = null) {
            if ($name === null || $method === null) {
                echo "\nError: Missing registration arguments.\n";
                echo "Usage: php swiften cron registertask [ClassName] [MethodName]\n";
                echo "Example: php swiften cron registertask \"App\\Swiften\\LocalStorage\" \"list\"\n\n";
                return;
            }

            try {
                $success = $this->Package->registerTask($name, $method);
                
                if ($success) {
                    echo "\nSuccessfully registered cron task: {$name}->{$method}()\n\n";
                } else {
                    echo "\nFailed to register cron task. The record might already exist.\n\n";
                }
            } catch (\Exception $e) {
                echo "\nError saving task to registry: " . $e->getMessage() . "\n\n";
            }
        }

        /**
         * Fallback info display panel
         * php swiften cron
         */
        public function toggle() {
            echo "\nSwiften's Cron Management CLI Module\n";
            echo "========================================\n";
            echo "Usage:\n";
            echo "  php swiften cron run\n";
            echo "  php swiften cron gettasks\n";
            echo "  php swiften cron registertask \"[ClassName]\" \"[MethodName]\"\n\n";
        }
    }