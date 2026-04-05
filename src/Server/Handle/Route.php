<?php

    namespace App\Server\Handle;

    class Route {

        public function capture($Dependencies = []) {

            /*
            This method will be responsible how each request
            handles data
            */

            // Attepmt to find the routes file
            $Uri = self::getRoute();
            if ($Uri === "/") {
                $Uri = "/index";
            }

            if (!empty($Dependencies)) {
                extract($Dependencies);
                ob_start();
            }


            $Route = ABSPATH . "/routes" . $Uri . ".php";
            if (file_exists($Route)) {
                include $Route;
                return;
            }

            // Load 404 via templater
            $data = ["route" => self::getRoute()];
            $Templater->load("server/404", $data);

        }


        public static function getRoute() {

            $Route = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
            $Route = rtrim($Route, "/");
            if ($Route === "") {
                $Route = "/";
            }

            return $Route ?? "/";

        }

    }

?>