<?php


    namespace App\Server\Handle;

    use Exception;

    class Views {

        private $default_route = ABSPATH . "/resources/views/";

        public function load($view_name = null, $dependencies = []) {
            if ($view_name === null) {
                throw new Exception("Unable to load view: Not provided");
            }

            $path_built = $this->default_route . $view_name . ".php";

            if (file_exists($path_built)) {
                if (!empty($dependencies)) {
                    extract($dependencies);

                    // Ensure dependencies exist -- may limit performance
                    $Dependencies = new \App\Server\Dependencies();
                    extract($Dependencies->fetch());
                    ob_start();
                }
                include $path_built;
                return true;
            }

            throw new Exception("Unable to load view: view file not found");
        }

    }