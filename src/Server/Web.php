<?php

    namespace SW\Source\Server;

    class Web {
        public static function Start() {
            /*
                Logic that will be executed when a web request is made to the app. 
                This can be used for middleware, routing, and other custom logics you have made. 
            */

            // Check if the domain the user is visiting from is valid
            if (!\SW\Source\Modules\DomainValidation::Validate()) {
                // If the domain is not valid, we will display "Unauthorised domain" and stop processing the request. 
                die("Invalid domain.");
            }
        }
    }