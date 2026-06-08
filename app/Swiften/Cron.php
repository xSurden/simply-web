<?php

    namespace App\Swiften;

    class Cron {

        private $Package;

        public function __construct() {
            $this->Package = new \App\Server\Utilities\Cron();
        }

        public function run() {
            echo "\nRunning cron functions...\n\n";
            $this->Package->run();
        }

        // Return error if method does not exist
        public function toggle() {
            echo "Swiften's Cron Function";
        }

    }