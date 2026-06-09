<?php

    namespace App\Blueprint\Utilities;

    class Queue {

        private $queueFile = ABSPATH . "/server/data/app/schema.sql";
        private $db;

        /*
        Check if the queue.sql file exists within the server and initialize the schema
        */
        public function __construct() {

            $dir = dirname($this->queueFile);

            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            try {
                $this->db = new \PDO("sqlite:" . $this->queueFile);
                $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                // Create a reliable job queue table schema
                $sql = "
                    CREATE TABLE IF NOT EXISTS queue_cron (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        task TEXT NOT NULL,
                        payload TEXT NULL,
                        status TEXT DEFAULT 'pending',
                        attempts INTEGER DEFAULT 0,
                        created_at INTEGER NOT NULL,
                        updated_at INTEGER NOT NULL
                    )
                ";

                $this->db->exec($sql);

            } catch (\PDOException $e) {
                throw new \Exception("Queue Initialization Error: " . $e->getMessage() . "\n");
            }
        }

        /**
         * Fetches jobs from the SQLite database that need processing.
         * * @param int $limit Maximum number of tasks to pull at once.
         * @return array Array of associative arrays representing the tasks.
         */
        public function getQueue($limit = 10) {
            try {
                // Pull items that are either 'pending' or have failed but can be retried (less than 3 attempts)
                $sql = "SELECT * FROM queue_cron 
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

        /**
         * Pushes a new background task onto the queue.
         * * @param string $task Name of the worker/action (e.g., 'send_email')
         * @param mixed $payload Context metadata or data arrays needed for the job
         * @return bool
         */
        public function add($task, $payload = null) {
            try {
                $sql = "INSERT INTO queue_cron (task, payload, created_at, updated_at) 
                        VALUES (:task, :payload, :created_at, :updated_at)";

                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(':task', $task, \PDO::PARAM_STR);
                $stmt->bindValue(':payload', is_array($payload) || is_object($payload) ? json_encode($payload) : $payload, \PDO::PARAM_STR);
                $stmt->bindValue(':created_at', time(), \PDO::PARAM_INT);
                $stmt->bindValue(':updated_at', time(), \PDO::PARAM_INT);

                return $stmt->execute();

            } catch (\PDOException $e) {
                error_log("Queue Push Error: " . $e->getMessage());
                return false;
            }
        }

        /**
         * Updates a job's processing cycle milestones.
         * * @param int $id The queue primary ID
         * @param string $status 'processing', 'completed', or 'failed'
         * @param bool $incrementAttempts Progress counter flag
         * @return bool
         */
        public function updateStatus($id, $status, $incrementAttempts = false) {
            try {
                $attemptSql = $incrementAttempts ? ", attempts = attempts + 1" : "";
                
                $sql = "UPDATE queue_cron 
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