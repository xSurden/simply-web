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
                "Env" => new \App\Server\Controller\Environment(),
                "Templater" => new \App\Modules\Templating\Templater(),
                "Views" => new \App\Server\Handle\Views()
            ];
        }

        public function fetch() {

            return $this->Dependencies ?? [];

        }

    }