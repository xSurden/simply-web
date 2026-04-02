<?php

    namespace App\Server;

    class Web {

        private $Route;
        private $Env;

        public function __construct() {
            // Ensure environment file exists
            if (!\App\Server\Controller\Environment::load()) {
                die("Unable to find the environment file!");
            }

            $this->Env = new \App\Server\Controller\Environment();

            // Load Route Handle
            $this->Route = new \App\Server\Handle\Route();
        }

        
        
        public function Start() {

            /*
            Main method that will receive the requests, and process them
            That's all - nothing else needs to be said here
            */

            // Environment Check
            $this->Env->checkEnvironment();

            /*
            This is the CSP protection script
            Currently you can load in Tailwind CSS, google fonts and your local files
            Other sources will be blocked unless specified below.
            */
            header("Content-Security-Policy: default-src 'self'; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://www.gstatic.com; " .
            "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://www.google.com https://www.gstatic.com; " .
            "frame-src https://www.google.com; " . 
            "font-src 'self' https://fonts.gstatic.com; " .
            "img-src 'self' data: https://www.gstatic.com; " .
            "object-src 'none';");

            
            /*
            Initialising secure cookie sessions.
            We prefer that you do keep this!
            */
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.use_only_cookies', 1); 
            session_start([
                'cookie_samesite' => 'Lax'
            ]);


            // Fetch route try -> catch
            try {
                $Pkg = new \App\Server\Dependencies();
                $this->Route->capture($Pkg->fetch());
            } catch (\Throwable $e) {
                die("Server Error: " . $e);
            }
            
        }

    }

?>