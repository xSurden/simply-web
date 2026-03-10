<?php

    namespace SW\Source\Modules;

    class DomainValidation {
        public static function Validate() {
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