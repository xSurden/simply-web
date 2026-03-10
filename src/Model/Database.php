<?php 

    namespace SW\Source\Model;

    class Database {
        /*
            A database model - used to manipulate the provided database easily.
        */

        // Class to create connection to the database
        public static function GetConnection() {
            $db_config = \SW\Source\Engine\ConfigEngine::GetValue("database");
            $host = $db_config["host"];
            $port = $db_config["port"];
            $username = $db_config["username"];
            $password = $db_config["password"];
            $dbname = $db_config["database"];

            try {
                $conn = new \PDO("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                return $conn;
            } catch (\PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
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

            // create connection and fetch the data
            $conn = self::GetConnection();
            $sql = "SELECT * FROM `$table` WHERE `$column` = :value LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->execute();

            // fetch the result and return
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ?: null;
        }
    }