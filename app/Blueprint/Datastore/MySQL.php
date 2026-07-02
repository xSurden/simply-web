<?php 

    namespace App\Blueprint\Datastore;

    use App\Blueprint\Environment;

    class MySQL {

        private $env;
        private $host;
        private $port;
        private $user;
        private $password;
        private $DB_name;

        private static ?\PDO $conn = null;

        public function __construct()
        {
            $this->env = new Environment();

            $this->host = $this->env->get("DB_HOST");
            $this->port = $this->env->get("DB_PORT");
            $this->DB_name = $this->env->get("DB_NAME");
            $this->user = $this->env->get("DB_USER");
            $this->password = $this->env->get("DB_PASSWORD");
        }

        public function createConnection() {

            if (self::$conn !== null) {
                return self::$conn;
            }

            $dsn = "mysql:host={$this->host};dbname={$this->DB_name};port={$this->port};charset=utf8mb4";

            self::$conn = new \PDO($dsn, $this->user, $this->password);
            self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return self::$conn;
        }

    }