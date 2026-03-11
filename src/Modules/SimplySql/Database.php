<?php 

    namespace SW\Source\Modules\SimplySql;

    class Database {
        /*
            A database Modules - used to manipulate the provided database easily.
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
    }