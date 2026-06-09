<?php

    namespace App\Blueprint\Web;

    class Router {

        // Store the registered routes
        protected static $routes = [];

        /**
         * Register a GET route explicitly mapping a URI to a view file
         */
        public static function get($uri, $viewPath) {
            // Clean up the URI to ensure uniform matching (e.g., /dashboard or dashboard)
            $uri = '/' . trim($uri, '/');
            self::$routes[$uri] = $viewPath;
        }

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
            This method now loads the explicit route definitions first,
            then checks if the current URI matches any of them.
            */

            // 1. Load the explicit routes file
            $webRoutesFile = ABSPATH . "/routes/Web.php";
            if (file_exists($webRoutesFile)) {
                include_once $webRoutesFile;
            }

            $Uri = self::getRoute();

            // 2. Check if the requested URI is explicitly defined
            if (array_key_exists($Uri, self::$routes)) {
                $viewPath = self::$routes[$Uri];
                
                // Render the mapped view path
                $this->view($viewPath, $Dependencies);
                return;
            }

            // 3. Fallback to 404 if no explicit route matches
            if (isset($Dependencies['Templater'])) {
                $data = ["route" => self::getRoute()];
                $Dependencies['Templater']->load("server/404", $data);
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