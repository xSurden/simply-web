<?php

    namespace App\Swiften;

    class Queue {

        private $Package;

        public function __construct() {

            $this->Package = new \App\Blueprint\Utilities\Queue();

        }

        // Return error if method does not exist
        public function toggle() {
            echo "Swiften's Queue Function";
        }

        public function getQueue() {
            echo "Items in queue: " . count($this->Package->getQueue(5));
        }

    }