<?php

    namespace App\Blueprint\Web;

    class Dependencies {

        private $Dependencies;

        public function __construct()
        {
            /*
            Add new dependencies into here.
            */

            $this->Dependencies = [
                "Environment" => new \App\Blueprint\Environment(),
                "Templater" => new \App\Blueprint\Web\Template(),
                "Router" => new \App\Blueprint\Web\Router(),
                "Views" => new \App\Blueprint\Web\Views(),
                "Resourcer" => new \App\Blueprint\Web\Resourcer(),
                "Exception" => new \App\Blueprint\Web\Exception()
            ];
        }

        public function fetch() {

            return $this->Dependencies ?? [];

        }

    }