<?php

    namespace App\Blueprint;

    class Web {

        private $Router;
        private $Env;
        private $Dependencies;
        private $Maintenance;
        private $Template;

        public function __construct() {
            // Ensure environment file exists
            if (!\App\Blueprint\Environment::load()) {
                die("Unable to find the environment file!");
            }

            $this->Env = new \App\Blueprint\Environment();
            $this->Router = new \App\Blueprint\Web\Router();
            $this->Maintenance = new \App\Blueprint\Utilities\Maintenance();
            $this->Dependencies = new \App\Blueprint\Web\Dependencies();
            $this->Template = new \App\Blueprint\Web\Template();
        }

        
        
        public function Start() {

            /*
            Main method that will receive the requests, and process them
            That's all - nothing else needs to be said here
            */

            // Environment Check
            $this->Env->checkEnvironment();

            // Check if maintenance is enabled
            if ($this->Maintenance->status()) {
                $this->Template->load("server/maintenance", $this->Dependencies->fetch());
                die;
            }

            /*
            Reads environment status to decide whether to skip, enforce, or fail.
            */

            if ($this->Env->get("APP_ENV") === null || trim($this->Env->get("APP_ENV")) === '') {
                throw new \Exception("Configuration Error: 'APP_ENV' is not specified in the system configuration.");
            }

            if (strtolower($this->Env->get("APP_ENV")) === "production") {
                include ABSPATH . "/config/CSP.php";
            } elseif (strtolower($this->Env->get("APP_ENV")) !== "development") {
                throw new \Exception("Configuration Error: Invalid application environment value specified: '{$this->Env->get("APP_ENV")}'. Expected 'development' or 'production'.");
            }

            
            /*
            Initialising secure cookie sessions.
            We prefer that you do keep this!
            */
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;

            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', $isSecure ? 1 : 0); // Only 1 if HTTPS is active
            ini_set('session.use_only_cookies', 1); 

            session_start([
                'cookie_samesite' => 'Lax'
            ]);


            // Fetch Router try -> catch
            try {
                $this->Router->capture($this->Dependencies->fetch());
            } catch (\Throwable $e) {
                // Log the error to web server
                error_log($e->getMessage());

                if (!class_exists('\App\Blueprint\Web\Template')) {
                    die("Fatal Error: Templater class missing. Original error: " . $e->getMessage());
                }
                $data = [
                    "server_error_message" => $e->getMessage(),
                    "server_error_trace" => $e->getTraceAsString()
                ];
                $this->Template->load("server/server_error", $data);
            }
                        
        }

    }

?>