<?php

    namespace App\Modules\Database;

    use PDO;

    class ElegantHandle {

        private $conn;

        public function __construct()
        {
            $DB = new \App\Modules\Database\DB();
            $this->conn = $DB->createConnection();
        }

        private function sanitize($string) {
            return preg_replace("/[^a-zA-Z0-9_]/", "", $string);
        }

        public function createTable($name, $dataArray) {
            if (!$name || empty($dataArray)) {
                return false;
            }

            $name = $this->sanitize($name);
            $columns = [];

            foreach ($dataArray as $column => $definition) {
                $column = $this->sanitize($column);
                $columns[] = "`$column` $definition";
            } 

            $sql = "CREATE TABLE IF NOT EXISTS `$name` (" . implode(', ', $columns) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            return $this->conn->exec($sql) !== false;
        }

        public function fetchTables() {
            $stmt = $this->conn->query("SHOW TABLES");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        public function fetchTable($table) {
            $table = $this->sanitize($table);
            if (!$table) return [];

            $stmt = $this->conn->query("SELECT * FROM `$table`");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function fetchField($table, $column, $key) {
            $table = $this->sanitize($table);
            $column = $this->sanitize($column);

            if (!$table || !$column) return null;

            $stmt = $this->conn->prepare("SELECT * FROM `$table` WHERE `$column` = ? LIMIT 1");
            $stmt->execute([$key]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        public function insert($table = null, $data = []) {
            if (!$table || empty($data)) return false;

            $table = $this->sanitize($table);
            $fields = array_map([$this, 'sanitize'], array_keys($data));
            $placeholders = array_fill(0, count($data), '?');

            $sql = "INSERT INTO `$table` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $placeholders) . ")";
            return $this->conn->prepare($sql)->execute(array_values($data));
        }

        public function update($table = null, $data = [], $where = []) {
            if (!$table || empty($data) || empty($where)) return false;

            $table = $this->sanitize($table);
            $values = [];
            $set = [];
            $conditions = [];

            foreach ($data as $f => $v) {
                $f = $this->sanitize($f);
                $set[] = "`$f` = ?";
                $values[] = $v;
            }

            foreach ($where as $f => $v) {
                $f = $this->sanitize($f);
                $conditions[] = "`$f` = ?";
                $values[] = $v;
            }

            $sql = "UPDATE `$table` SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $conditions);
            return $this->conn->prepare($sql)->execute($values);
        }

        public function delete($table = null, $where = []) {
            if (!$table || empty($where)) return false;

            $table = $this->sanitize($table);
            $conditions = [];
            $values = [];

            foreach ($where as $f => $v) {
                $f = $this->sanitize($f);
                $conditions[] = "`$f` = ?";
                $values[] = $v;
            }

            $sql = "DELETE FROM `$table` WHERE " . implode(' AND ', $conditions);
            return $this->conn->prepare($sql)->execute($values);
        }

        public function dropTable($table = null) {
            $table = $this->sanitize($table);
            if (!$table) return false;

            return $this->conn->exec("DROP TABLE IF EXISTS `$table`") !== false;
        }
    }