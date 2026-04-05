<?php

    namespace App\Server;

    class Web {

        private $Route;
        private $Env;
        private $Dependencies;
        private $Maintenance;
        private $Templater;

        public function __construct() {
            // Ensure environment file exists
            if (!\App\Server\Controller\Environment::load()) {
                die("Unable to find the environment file!");
            }

            $this->Env = new \App\Server\Controller\Environment();
            $this->Maintenance = new \App\Server\Utilities\Maintenance();
            $this->Route = new \App\Server\Handle\Route();
            $this->Dependencies = new \App\Server\Dependencies();
            $this->Templater = new \App\Modules\Templating\Templater();
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
                $this->Templater->load("server/maintenance", $this->Dependencies->fetch());
                die;
            }

            /*
            This is the CSP protection script
            Currently you can load in Tailwind CSS, google fonts and your local files
            Other sources will be blocked unless specified below.
            */
            header("Content-Security-Policy: default-src 'self'; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://www.gstatic.com https://cdn.tailwindcss.com; " .
            "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://www.google.com https://www.gstatic.com; " .
            "frame-src https://www.google.com; " . 
            "font-src 'self' https://fonts.gstatic.com; " .
            "img-src 'self' data: https://www.gstatic.com; " .
            "object-src 'none';");

            
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


            // Fetch route try -> catch
            try {
                $this->Route->capture($this->Dependencies->fetch());
            } catch (\Throwable $e) {
                // Log the error to web server
                error_log($e->getMessage());

                if (!class_exists('\App\Modules\Templating\Templater')) {
                    die("Fatal Error: Templater class missing. Original error: " . $e->getMessage());
                }
                $data = [
                    "server_error_message" => $e->getMessage(),
                    "server_error_trace" => $e->getTraceAsString()
                ];
                $this->Templater->load("server/server_error", $data);
            }
                        
        }

    }

?>