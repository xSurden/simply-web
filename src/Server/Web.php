<?php

    namespace SW\Source\Server;

    class Web {
        public static function Start() {
            /*
                Logic that will be executed when a web request is made to the app. 
                This can be used for middleware, routing, and other custom logics you have made. 
            */

            // Check if the domain the user is visiting from is valid
            if (!self::Validate()) {
                // If the domain is not valid, we will display "Unauthorised domain" and stop processing the request. 
                die("Invalid domain.");
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