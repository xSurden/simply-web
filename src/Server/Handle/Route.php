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
            $Route = ABSPATH . "/routes" . $Uri . ".php";
            if (file_exists($Route)) {
                if (!empty($Dependencies)) {
                    extract($Dependencies);
                    ob_start();
                }
                include $Route;
                return;
            }

            echo "404, Not able to find route view: " . $Uri;

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