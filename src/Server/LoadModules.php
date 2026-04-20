<?php

    namespace App\Server;

    class LoadModules {

        public function load() {

            /*
            This class will be used to load the required checks and modules
            Can also be ran by cron if need be or cli. 
            */

            // Check if SQL for crons exists

            // Load required modules
            $Cache = new \App\Server\Utilities\Cache();


            // If all is fine, return true;
            return true;


        }

    }