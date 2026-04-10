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

        public function select($table, $where = [], $limit = null, $order = null, $offset = null) {
            $table = $this->sanitize($table);
            $sql = "SELECT * FROM `$table`";
            $values = [];

            if (!empty($where)) {
                $conditions = [];
                foreach ($where as $col => $val) {
                    $col = $this->sanitize($col);
                    $conditions[] = "`$col` = ?";
                    $values[] = $val;
                }
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            if ($order) {
                $sql .= " ORDER BY " . $order; 
            }

            if ($limit !== null) {
                $sql .= " LIMIT " . (int)$limit;
            }

            if ($offset !== null) {
                $sql .= " OFFSET " . (int)$offset;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($values);
            
            return ($limit === 1) ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function fetchTable($table, $limit = 100, $order = "id DESC") {
            return $this->select($table, [], $limit, $order);
        }

        public function fetchField($table, $column, $key, $limit = 1) {
            return $this->select($table, [$column => $key], $limit);
        }

        public function count($table, $where = []) {
            $table = $this->sanitize($table);
            $sql = "SELECT COUNT(*) as total FROM `$table`";
            $values = [];

            if (!empty($where)) {
                $conditions = [];
                foreach ($where as $col => $val) {
                    $col = $this->sanitize($col);
                    $conditions[] = "`$col` = ?";
                    $values[] = $val;
                }
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($values);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$res['total'];
        }

        public function insert($table, $data = []) {
            if (empty($data)) return false;
            $table = $this->sanitize($table);
            
            $fields = array_map([$this, 'sanitize'], array_keys($data));
            $placeholders = array_fill(0, count($data), '?');

            $sql = "INSERT INTO `$table` (`" . implode('`, `', $fields) . "`) VALUES (" . implode(', ', $placeholders) . ")";
            return $this->conn->prepare($sql)->execute(array_values($data));
        }

        public function update($table, $data = [], $where = []) {
            if (empty($data) || empty($where)) return false;
            $table = $this->sanitize($table);
            
            $set = [];
            $values = [];
            foreach ($data as $f => $v) {
                $set[] = "`" . $this->sanitize($f) . "` = ?";
                $values[] = $v;
            }

            $conditions = [];
            foreach ($where as $f => $v) {
                $conditions[] = "`" . $this->sanitize($f) . "` = ?";
                $values[] = $v;
            }

            $sql = "UPDATE `$table` SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $conditions);
            return $this->conn->prepare($sql)->execute($values);
        }

        public function delete($table, $where = []) {
            if (empty($where)) return false;
            $table = $this->sanitize($table);
            
            $conditions = [];
            $values = [];
            foreach ($where as $f => $v) {
                $conditions[] = "`" . $this->sanitize($f) . "` = ?";
                $values[] = $v;
            }

            $sql = "DELETE FROM `$table` WHERE " . implode(' AND ', $conditions);
            return $this->conn->prepare($sql)->execute($values);
        }

        
        public function createTable($name, $dataArray) {
            $name = $this->sanitize($name);
            $columns = [];
            foreach ($dataArray as $column => $definition) {
                $columns[] = "`" . $this->sanitize($column) . "` $definition";
            } 
            $sql = "CREATE TABLE IF NOT EXISTS `$name` (" . implode(', ', $columns) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            return $this->conn->exec($sql) !== false;
        }

        public function dropTable($table) {
            $table = $this->sanitize($table);
            return $this->conn->exec("DROP TABLE IF EXISTS `$table`") !== false;
        }
    }