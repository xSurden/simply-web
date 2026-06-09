<?php

    namespace App\Blueprint\Utilities;

    use App\Blueprint\Datastore\SQLitePointer;

    class Queue extends SQLitePointer {

        private $tableName = "queue_cron";

        public function __construct() {
            $schema = "
                CREATE TABLE IF NOT EXISTS {$this->tableName} (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    task TEXT NOT NULL,
                    payload TEXT NULL,
                    status TEXT DEFAULT 'pending',
                    attempts INTEGER DEFAULT 0,
                    created_at INTEGER NOT NULL,
                    updated_at INTEGER NOT NULL
                )
            ";
            parent::__construct([$schema]);
        }

        public function getQueue($limit = 10) {
            try {
                // Complex conditional queries can safely access the inherited protected $this->db reference
                $sql = "SELECT * FROM {$this->tableName} 
                        WHERE status = 'pending' OR (status = 'failed' AND attempts < 3)
                        ORDER BY id ASC 
                        LIMIT :limit";

                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
                $stmt->execute();

                return $stmt->fetchAll(\PDO::FETCH_ASSOC);

            } catch (\PDOException $e) {
                error_log("Queue Fetch Error: " . $e->getMessage());
                return [];
            }
        }

        public function add($task, $payload = null) {
            $data = [
                'task'       => $task,
                'payload'    => is_array($payload) || is_object($payload) ? json_encode($payload) : $payload,
                'created_at' => time(),
                'updated_at' => time()
            ];

            return $this->insertRow($this->tableName, $data);
        }

        public function updateStatus($id, $status, $incrementAttempts = false) {
            try {
                // Custom modifier logic handled directly via native PDO reference
                $attemptSql = $incrementAttempts ? ", attempts = attempts + 1" : "";
                
                $sql = "UPDATE {$this->tableName} 
                        SET status = :status, updated_at = :updated_at {$attemptSql} 
                        WHERE id = :id";

                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':status', $status, \PDO::PARAM_STR);
                $stmt->bindValue(':updated_at', time(), \PDO::PARAM_INT);
                $stmt->bindValue(':id', (int)$id, \PDO::PARAM_INT);

                return $stmt->execute();

            } catch (\PDOException $e) {
                error_log("Queue Update Error: " . $e->getMessage());
                return false;
            }
        }
    }