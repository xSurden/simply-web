<?php 

    namespace App\Modules\Database;

    use App\Server\Controller\Environment;

    class DB {

        private $env;
        private $host;
        private $port;
        private $user;
        private $password;
        private $db_name;

        public function __construct()
        {
            $this->env = new Environment();

            $this->host = $this->env->get("DB_HOST");
            $this->host = $this->env->get("DB_PORT");
            $this->host = $this->env->get("DB_NAME");
            $this->host = $this->env->get("DB_USER");
            $this->host = $this->env->get("DB_PASSWORD");
        }

        public function createConnection() {
            
            try {
                $conn = new \PDO("mysql:host=$this->host;dbname=$this->db_name;port=$this->port", $this->user, $this->password);
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                return $conn;
            } catch (\PDOException $e) {
                die("Server Error: " . $e);
            }

        }

    }