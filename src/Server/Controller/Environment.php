<?php

    namespace App\Server\Controller;
    
    use Dotenv\Dotenv;

    class Environment {

        private static $ENV_PATH = ABSPATH;

        /*
        Server class: Used for fetching values from .env file.
        */

        public static function load() {
            // Check if the .env file exists
            if (file_exists(self::$ENV_PATH)) {
                $dotenv = Dotenv::createImmutable(self::$ENV_PATH);
                $dotenv->load();
                return true;
            }

            return false;
        }

        public function get($key) {
            return $_ENV[$key] ?? null;
        }

        public function getMicroTime(): float {
            $start = defined('SW_START') ? SW_START : microtime(true);
            $end = microtime(true);
            
            return round(($end - $start) * 1000, 2);
        }

        public function checkEnvironment() {
            // Check if hostname is from the app_url
            if ($_SERVER["HTTP_HOST"] !== $this->get("APP_URL")) {
                die("This hostname is not authorised to access this web application");
            }
        }

    }