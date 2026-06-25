<?php

    namespace App\Blueprint\Datastore;

    class LocalStorage extends SQLitePointer {

        private $tableName = "server_variables";

        public function __construct() {
            $schema = "
                CREATE TABLE IF NOT EXISTS {$this->tableName} (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    valueName TEXT NOT NULL UNIQUE,
                    valueData TEXT NULL,
                    created_at INTEGER NOT NULL,
                    updated_at INTEGER NOT NULL
                )
            ";
            // Send table schema directly to the parent SQLite initializer
            parent::__construct([$schema]);
        }

        public function getValue($valueName = null) {
            if ($valueName === null) return null;

            $row = $this->getRow($this->tableName, 'valueName', $valueName);
            return $row ? $row['valueData'] : null;
        }

        public function setValue($valueName, $valueData = null) {
            if (empty($valueName)) return false;

            $now = time();
            
            // Use our inherited engine commands to verify state, keeping logic clean
            if ($this->hasValue($valueName)) {
                return $this->updateRow($this->tableName, 'valueName', $valueName, [
                    'valueData' => $valueData,
                    'updated_at' => $now
                ]);
            }

            return $this->insertRow($this->tableName, [
                'valueName' => $valueName,
                'valueData' => $valueData,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        public function updateValue($valueName, $valueData = null) {
            if (empty($valueName)) return false;

            return $this->updateRow($this->tableName, 'valueName', $valueName, [
                'valueData' => $valueData,
                'updated_at' => time()
            ]);
        }

        public function dropValue($valueName) {
            if (empty($valueName)) return false;
            return $this->deleteRow($this->tableName, 'valueName', $valueName);
        }

        public function hasValue($valueName) {
            if (empty($valueName)) return false;
            return $this->getRow($this->tableName, 'valueName', $valueName) !== null;
        }

        public function getAllValues() {
            $rows = $this->getAllRows($this->tableName, 'valueName');
            
            $output = [];
            foreach ($rows as $row) {
                $output[$row['valueName']] = $row['valueData'];
            }
            return $output;
        }
    }