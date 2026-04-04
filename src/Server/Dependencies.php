<?php

    namespace App\Server;

    class Dependencies {

        private $Dependencies;

        public function __construct()
        {
            /*
            Add new dependencies into here.
            */

            $this->Dependencies = [
                "Environment" => new \App\Server\Controller\Environment()
            ];
        }

        public function fetch() {

            return $this->Dependencies ?? [];

        }

    }