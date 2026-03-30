<?php

    namespace SW\Source\Server;

    class Web {

        private static $TemplateEngine;
        private static $Pointer;
        private static $Maintenance;
        private static $Migrations;

        public function __construct() {

            /*
            |   Default Web handler
            |   Does not need to be altered unless you know
            |   what you are doing
            */

            if(self::$TemplateEngine === null) {
                self::$TemplateEngine = new \SW\Source\Server\Engine\TemplateEngine();
            }
            if(self::$Pointer === null) {
                self::$Pointer = new \SW\Source\Server\Database\Pointer();
            }
            if(self::$Maintenance === null) {
                self::$Maintenance = new \SW\Source\Server\Utilities\Maintenance();
            }
            if(self::$Migrations === null) {
                self::$Migrations = new \SW\Source\Server\Utilities\Migrations();
            }

        }



        /*
        |   Public methods
        */



        public static function Start() {

            /*
            |   Start of the web request - will be handled here
            */

            // Check if maintenance is enabled
            if (self::$Maintenance::Status()) {

            }
        }



        /*
        |   Private methods
        */

    }