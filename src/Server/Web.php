<?php

    namespace SW\Source\Server;

    class Web {

        private static $TemplateEngine;
        private static $Pointer;
        private static $Migrations;

        public function __construct() 
        {
            self::Init();
        }

        private static function Init(){
            if (self::$TemplateEngine === null) {
                self::$TemplateEngine = new \SW\Source\Server\Engine\TemplateEngine();
            }

            if (self::$Pointer === null) {
                self::$Pointer = new \SW\Source\Modules\SimplySql\Pointer();
            }

            if (self::$Migrations === null) {
                self::$Migrations = new \SW\Source\Server\Utilities\Migrations();
            }
        }

        public static function RegisterCronTasks(array $tasks = []) {
            if (!empty($tasks)) {
                foreach ($tasks as $item) {
                    $result = \SW\Source\Server\Utilities\Cron::Register($item["class"], $item["method"]);
                    if (!$result) {
                        $data = [
                            "type" => "Error - Cron Failed",
                            "code" => 00,
                            "message" => "Our server failed to register certain cron tasks - which has broken our system."
                        ];
                        \SW\Source\Server\Engine\TemplateEngine::Render("server/message", $data);
                        exit();
                    }
                }
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
            self::CheckDatabase();

            // Check if the domain the user is visiting from is valid
            if (!self::ValidateDomain()) {
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

        private static function ValidateDomain() {
            /*
                Logic to validate the domain. This can include checking if the domain is in a valid format, if it is allowed by the app's configuration, etc. 
                You can also add custom logic here to handle specific cases or requirements for your app. 
            */

            $domain = $_SERVER['HTTP_HOST'];
            $trusted_domains = \SW\Source\Server\Engine\ConfigEngine::GetValue("trusted_domains");

            if (!in_array($domain, $trusted_domains)) {
                return false;
            }

            // if the domain is valid, return true
            return true;
        }

        private static function CheckDatabase() {
            $existingTables = self::$Pointer->FetchTables();
            
            if (!in_array("server_configs", $existingTables)) {
                self::$Migrations::Server_Configs_Check();
            }

            // Check if repo variable exists
            $result = self::$Pointer->FetchField("server_configs", "config_key", "package_repository_url");
            if ($result === null) {
                $data = [
                    "config_key"   => "package_repository_url",
                    "config_value" => "https://repo.surden.me/packages/",
                    "description"  => "The repo url - default is standard and shipped with framework"
                ];
                self::$Pointer->Insert("server_configs", $data);
            }
        }
    }