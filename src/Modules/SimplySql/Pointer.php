<?php

    namespace SW\Source\Modules\SimplySql;

    class Pointer {

        /*
            Get all tables in the provided
            database;
        */
        public function FetchTables() {
            $conn = \SW\Source\Modules\SimplySql\Database::GetConnection();
            $sql = "Show tables;";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_COLUMN) ?? [];
        }

        /*
            Functions to use the database
            including fetching data, inserting and other stuff.
        */
        public function FetchField($table, $column, $value) {
            if (!$table || !$column || !$value) {
                return null;
            }

            // sanitise the names
            $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
            $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);

            $conn = \SW\Source\Modules\SimplySql\Database::GetConnection();
            $sql = "SELECT * FROM `$table` WHERE `$column` = :value LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null;
        }

        /*
            Get all fields from a table
        */
        public function FetchAll($table) {
            if (!$table) {
                return [];
            }

            // sanitise the name
            $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

            $conn = \SW\Source\Modules\SimplySql\Database::GetConnection();
            $sql = "SELECT * FROM `$table`";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        /*
            Insert function
        */
        public function Insert($table = null, $data = []) {
            if (!$table || empty($data)) {
                return false;
            }

            // Sanitise the table name
            $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

            $fields = array_keys($data);
            $placeholders = array_map(function($field) {
                return ':' . preg_replace('/[^a-zA-Z0-9_]/', '', $field);
            }, $fields);

            $conn = \SW\Source\Modules\SimplySql\Database::GetConnection();
            $sql = "INSERT INTO `$table` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $conn->prepare($sql);

            foreach ($data as $field => $value) {
                $placeholder = ':' . preg_replace('/[^a-zA-Z0-9_]/', '', $field);
                $stmt->bindValue($placeholder, $value);
            }

            return $stmt->execute();
        }

        public function Update($table = null, $data = [], $where = []) {
            if (!$table || empty($data) || empty($where)) {
                return false;
            }

            // check names
            $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

            $setClauses = [];
            foreach ($data as $field => $value) {
                $sanitizedField = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
                $setClauses[] = "`$sanitizedField` = :set_$sanitizedField";
            }

            $whereClauses = [];
            foreach ($where as $field => $value) {
                $sanitizedField = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
                $whereClauses[] = "`$sanitizedField` = :where_$sanitizedField";
            }

            $conn = \SW\Source\Modules\SimplySql\Database::GetConnection();
            $sql = "UPDATE `$table` SET " . implode(', ', $setClauses) . " WHERE " . implode(' AND ', $whereClauses);
            $stmt = $conn->prepare($sql);

            foreach ($data as $field => $value) {
                $placeholder = ':set_' . preg_replace('/[^a-zA-Z0-9_]/', '', $field);
                $stmt->bindValue($placeholder, $value);
            }

            foreach ($where as $field => $value) {
                $placeholder = ':where_' . preg_replace('/[^a-zA-Z0-9_]/', '', $field);
                $stmt->bindValue($placeholder, $value);
            }

            return $stmt->execute();
        }

        public function Delete($table = null, $where = []) {
            if (!$table || empty($where)) {
                return false;
            }

            // Sanitise the table name
            $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

            $whereClauses = [];
            foreach ($where as $field => $value) {
                $sanitizedField = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
                $whereClauses[] = "`$sanitizedField` = :$sanitizedField";
            }

            $conn = \SW\Source\Modules\SimplySql\Database::GetConnection();
            $sql = "DELETE FROM `$table` WHERE " . implode(' AND ', $whereClauses);
            $stmt = $conn->prepare($sql);

            foreach ($where as $field => $value) {
                $placeholder = ':' . preg_replace('/[^a-zA-Z0-9_]/', '', $field);
                $stmt->bindValue($placeholder, $value);
            }

            return $stmt->execute();
        }

        public function DropTable($table = null) {
            if (!$table) {
                return false;
            }

            // Sanitise the table name
            $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

            $conn = \SW\Source\Modules\SimplySql\Database::GetConnection();
            $sql = "DROP TABLE IF EXISTS `$table`";
            $stmt = $conn->prepare($sql);
            return $stmt->execute();
        }
    }