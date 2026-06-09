<?php

    namespace App\Blueprint\Web;

    class Router {

        public function view($path = null, $data = []) {
            if ($path === null) {
                throw new \Exception("Unable to load the view as it is not set");
            }

            $pathWithExt = str_ends_with($path, ".php") ? $path : $path . ".php";
            $filePath = ABSPATH . "/resources/views/" . $pathWithExt;

            if (!file_exists($filePath)) {
                throw new \Exception("View path not found: " . $filePath);
            }

            $deps = new \App\Blueprint\Web\Dependencies();
            $viewVariables = array_merge($deps->fetch(), $data);
            
            extract($viewVariables);

            include $filePath;
        }

        public function capture($Dependencies = []) {
            /*
            This method is responsible for how each request handles data,
            now supporting both direct files and directory-based routing.
            */

            $Uri = self::getRoute();
            
            if ($Uri === "/") {
                $Uri = "/index";
            }

            // Prepare dependencies for the included route files
            if (!empty($Dependencies)) {
                extract($Dependencies);
            }

            $basePath = ABSPATH . "/routes" . $Uri;
            $fileRoute = $basePath . ".php";
            $folderRoute = $basePath . "/index.php";

            if (file_exists($fileRoute)) {
                include $fileRoute;
                return;
            } 
            
            if (is_dir($basePath) && file_exists($folderRoute)) {
                include $folderRoute;
                return;
            }

            if (isset($Templater)) {
                $data = ["route" => self::getRoute()];
                $Templater->load("server/404", $data);
            } else {
                http_response_code(404);
                die("404 - Page not found");
            }
        }

        public static function getRoute() {
            $Route = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
            $Route = rtrim($Route, "/");
            
            return empty($Route) ? "/" : $Route;
        }
    }