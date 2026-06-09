<?php

    /*
    This class is used for storing local data, such as ReCaptcha's tokens and keys.
    
    I will be using SQLite, as a flat file format for speed, reliability (in case connection drops)
    */

    namespace App\Blueprint\Datastore;

    class LocalStorage {

        private $db;
        private $localServerVariablesFile = ABSPATH . "/server/data/app/schema.sql";
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

                // Ensure the directory exists before attempting to create/open the SQLite file
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
         * * @param string|null $valueName
         * @return string|null
         */
        public function getValue($valueName = null) {

            if ($valueName === null) {
                return null;
            }

            try {
                $stmt = $this->db->prepare("SELECT valueData FROM server_variables WHERE valueName = :valueName LIMIT 1");
                $stmt->execute([':valueName' => $valueName]);
                
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                // Return the data if found, otherwise null
                return $result ? $result['valueData'] : null;

            } catch (\PDOException $e) {
                throw new \Exception("Error fetching variable '{$valueName}': " . $e->getMessage());
            }
        }

        /**
         * Store or update a variable's value. 
         * Uses an UPSERT (INSERT ... ON CONFLICT) to update cleanly if the key already exists.
         * * @param string $valueName
         * @param string|null $valueData
         * @return bool
         */
        public function setValue($valueName, $valueData = null) {
            if (empty($valueName)) {
                return false;
            }

            $now = time();

            try {
                // SQLite 3.24.0+ supports ON CONFLICT syntax natively
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
    }