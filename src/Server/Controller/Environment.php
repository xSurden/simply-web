<?php

    namespace App\Server\Controller;

    class Environment {

        /*
        Server class: Used for fetching values from .env file.
        */

        public static function isAvailable() {
            // Check if the .env file exists
            if (file_exists(ABSPATH . "/.env")) {
                return true;
            }

            return false;
        }

    }