<?php

    namespace App\Blueprint\Datastore;

    class SQLitePointer {

        protected $db;
        private $localServerVariablesFile = ABSPATH . "/server/data/app/schema.sqlite";

        /**
         * Constructor sets up database connection and executes any class-specific initialization schemas.
         * @param array $initialSchemas Array of raw CREATE TABLE SQL strings
         */
        public function __construct(array $initialSchemas = []) {
            try {
                $dir = dirname($this->localServerVariablesFile);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $this->db = new \PDO("sqlite:" . $this->localServerVariablesFile);
                $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                // Execute tables definitions specific to the child class on instantiation
                foreach ($initialSchemas as $schemaSql) {
                    if (!empty(trim($schemaSql))) {
                        $this->db->exec($schemaSql);
                    }
                }

            } catch (\PDOException $e) {
                throw new \Exception("Database Pointer Error: " . $e->getMessage() . "\n");
            }
        }

        /**
         * Add a record / Row insert
         */
        public function insertRow($table, array $data) {
            if (empty($table) || empty($data)) return false;

            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));

            try {
                $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
                $stmt = $this->db->prepare($sql);
                
                $params = [];
                foreach ($data as $key => $value) {
                    $params[":{$key}"] = $value;
                }

                return $stmt->execute($params);
            } catch (\PDOException $e) {
                throw new \Exception("Pointer Insert Error on table '{$table}': " . $e->getMessage());
            }
        }

        /**
         * Update an explicit record matching a key condition
         */
        public function updateRow($table, $keyColumn, $keyValue, array $data) {
            if (empty($table) || empty($data)) return false;

            $fields = [];
            $params = [":key_val" => $keyValue];

            foreach ($data as $column => $value) {
                $fields[] = "{$column} = :{$column}";
                $params[":{$column}"] = $value;
            }

            $fieldString = implode(', ', $fields);

            try {
                $sql = "UPDATE {$table} SET {$fieldString} WHERE {$keyColumn} = :key_val";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute($params);
            } catch (\PDOException $e) {
                throw new \Exception("Pointer Update Error on table '{$table}': " . $e->getMessage());
            }
        }

        /**
         * Fetch a single row by condition
         */
        public function getRow($table, $keyColumn, $keyValue) {
            try {
                $sql = "SELECT * FROM {$table} WHERE {$keyColumn} = :key_val LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':key_val' => $keyValue]);
                return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
            } catch (\PDOException $e) {
                throw new \Exception("Pointer Read Error on table '{$table}': " . $e->getMessage());
            }
        }

        /**
         * Bulk fetch helper for simple structured data retrieval
         */
        public function getAllRows($table, $orderByColumn = null) {
            try {
                $orderSql = $orderByColumn ? " ORDER BY {$orderByColumn} ASC" : "";
                $stmt = $this->db->query("SELECT * FROM {$table}{$orderSql}");
                return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            } catch (\PDOException $e) {
                throw new \Exception("Pointer Bulk Read Error on table '{$table}': " . $e->getMessage());
            }
        }

        /**
         * Delete a row by condition
         */
        public function deleteRow($table, $keyColumn, $keyValue) {
            try {
                $sql = "DELETE FROM {$table} WHERE {$keyColumn} = :key_val";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':key_val' => $keyValue]);
                return $stmt->rowCount() > 0;
            } catch (\PDOException $e) {
                throw new \Exception("Pointer Delete Error on table '{$table}': " . $e->getMessage());
            }
        }
    }