<?php

    namespace SW\Source\Server;

    class Web {

        private static $TemplateEngine;

        public function __construct() 
        {
            self::Init();
        }

        private static function Init(){
            if (self::$TemplateEngine === null) {
                self::$TemplateEngine = new \SW\Source\Engine\TemplateEngine();
            }
        }

        public static function Start() {
            /*
                Logic that will be executed when a web request is made to the app. 
                This can be used for middleware, routing, and other custom logics you have made. 
            */

            // Init and check for files
            self::Init();
            self::FileIntegrity();

            // Check if the domain the user is visiting from is valid
            if (!self::Validate()) {
                // If the domain is not valid, we will display "Unauthorised domain" and stop processing the request. 
                self::$TemplateEngine->Render("server/invalid-domain");
                exit();
            }
        }

        /*
            This section is for server commands such as refresh page etc
        */
        public static function Refresh() {
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }

        private static function FileIntegrity() {
            /*
                Check if the application core files are intact
                
                Checks for settings file. 
            */

            if (!file_exists(ABSPATH . "/settings.php")) {
                $data = [
                    "type" => "Error",
                    "code" => 404,
                    "message" => "Unable to load settings file. Please ensure settings.php exists"
                ];
                self::$TemplateEngine->Render("server/message", $data);
            }
        }

        private static function Validate() {
            /*
                Logic to validate the domain. This can include checking if the domain is in a valid format, if it is allowed by the app's configuration, etc. 
                You can also add custom logic here to handle specific cases or requirements for your app. 
            */

            $domain = $_SERVER['HTTP_HOST'];
            $trusted_domains = \SW\Source\Engine\ConfigEngine::GetValue("trusted_domains");

            if (!in_array($domain, $trusted_domains)) {
                return false;
            }

            // if the domain is valid, return true
            return true;
        }
    }