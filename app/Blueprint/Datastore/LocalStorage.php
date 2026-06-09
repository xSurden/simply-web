<?php

    /*
    This class is used for storing local data, such as ReCaptcha's tokens and keys.
    
    I will be using SQLite, as a flat file format for speed, reliability (in case connection drops)
    */

    namespace App\Blueprint\Datastore;

    class LocalStorage {

        private $db;
        private $localServerVariablesFile = ABSPATH . "/server/data/app/schema.sqlite";
        private $localSchema = "
            CREATE TABLE IF NOT EXISTS server_variables (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                valueName TEXT NOT NULL UNIQUE,
                valueData TEXT NULL,
                created_at INTEGER NOT NULL,
                updated_at INTEGER NOT NULL
            )
        ";


        public function __construct() {

            try {
                $dir = dirname($this->localServerVariablesFile);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $this->db = new \PDO("sqlite:" . $this->localServerVariablesFile);
                $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $this->db->exec($this->localSchema);

            } catch (\PDOException $e) {
                throw new \Exception("Queue Initialization Error: " . $e->getMessage() . "\n");
            }
        }

        /**
         * Fetch a variable's value by its name.
         */
        public function getValue($valueName = null) {
            if ($valueName === null) {
                return null;
            }

            try {
                $stmt = $this->db->prepare("SELECT valueData FROM server_variables WHERE valueName = :valueName LIMIT 1");
                $stmt->execute([':valueName' => $valueName]);
                
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                return $result ? $result['valueData'] : null;

            } catch (\PDOException $e) {
                throw new \Exception("Error fetching variable '{$valueName}': " . $e->getMessage());
            }
        }

        /**
         * Store or update a variable's value. 
         */
        public function setValue($valueName, $valueData = null) {
            if (empty($valueName)) {
                return false;
            }

            $now = time();

            try {
                $sql = "
                    INSERT INTO server_variables (valueName, valueData, created_at, updated_at)
                    VALUES (:valueName, :valueData, :created_at, :updated_at)
                    ON CONFLICT(valueName) DO UPDATE SET
                        valueData = excluded.valueData,
                        updated_at = excluded.updated_at
                ";

                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    ':valueName'  => $valueName,
                    ':valueData'  => $valueData,
                    ':created_at' => $now,
                    ':updated_at' => $now
                ]);

            } catch (\PDOException $e) {
                throw new \Exception("Error setting variable '{$valueName}': " . $e->getMessage());
            }
        }

        /**
         * Explicitly update an existing variable's value.
         */
        public function updateValue($valueName, $valueData = null) {
            if (empty($valueName)) {
                return false;
            }

            $now = time();

            try {
                $sql = "UPDATE server_variables SET valueData = :valueData, updated_at = :updated_at WHERE valueName = :valueName";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':valueName'  => $valueName,
                    ':valueData'  => $valueData,
                    ':updated_at' => $now
                ]);

                return $stmt->rowCount() > 0;

            } catch (\PDOException $e) {
                throw new \Exception("Error updating variable '{$valueName}': " . $e->getMessage());
            }
        }

        /**
         * Delete a variable entirely from the database store.
         * * @param string $valueName
         * @return bool True if deleted, False if it didn't exist
         */
        public function dropValue($valueName) {
            if (empty($valueName)) {
                return false;
            }

            try {
                $stmt = $this->db->prepare("DELETE FROM server_variables WHERE valueName = :valueName");
                $stmt->execute([':valueName' => $valueName]);

                return $stmt->rowCount() > 0;
            } catch (\PDOException $e) {
                throw new \Exception("Error deleting variable '{$valueName}': " . $e->getMessage());
            }
        }

        /**
         * Verify if a key exists in the storage regardless of whether its value is NULL.
         * * @param string $valueName
         * @return bool
         */
        public function hasValue($valueName) {
            if (empty($valueName)) {
                return false;
            }

            try {
                $stmt = $this->db->prepare("SELECT 1 FROM server_variables WHERE valueName = :valueName LIMIT 1");
                $stmt->execute([':valueName' => $valueName]);
                return (bool) $stmt->fetchColumn();
            } catch (\PDOException $e) {
                return false;
            }
        }

        /**
         * Grab everything inside the data table. Highly useful for building control panels or tables.
         * * @return array Key-value pair array of all data
         */
        public function getAllValues() {
            try {
                $stmt = $this->db->query("SELECT valueName, valueData FROM server_variables ORDER BY valueName ASC");
                return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
            } catch (\PDOException $e) {
                return [];
            }
        }
    }