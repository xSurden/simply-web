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
                "Environment" => new \App\Server\Controller\Environment(),
                "Templater" => new \App\Modules\Templating\Templater(),
                "Router" => new \App\Server\Handle\Router(),
                "Views" => new \App\Server\Handle\Views(),
                "Resourcer" => new \App\Server\Controller\Resourcer(),
                "Exception" => new \App\Server\Handle\Exception()
            ];
        }

        public function fetch() {

            return $this->Dependencies ?? [];

        }

    }