<?php

    namespace App\Swiften;

    class Maintenance {

        private $Package;

        public function __construct() {
            $this->Package = new \App\Server\Utilities\Maintenance();
        }

        public function on() {
            echo $this->Package->on();
        }

        public function off() {
            echo $this->Package->off();
        }

    }