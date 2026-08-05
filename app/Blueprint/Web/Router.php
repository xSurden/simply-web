<?
    namespace App\Blueprint\Web;

    class Router {

        protected static $routes = [];
        protected static $lastRouteUri = null; // Track the most recently added route for chaining

        public static function get($uri, $viewPath) {
            $uri = '/' . trim($uri, '/');
            
            // Store route with default properties
            self::$routes[$uri] = [
                'view' => $viewPath,
                'auth' => false // Default to false
            ];

            // Keep track of this URI so ->auth() knows which route to modify
            self::$lastRouteUri = $uri;

            // Return a static-friendly instance or self reference for chaining
            // Since get is static, returning an object or using a helper works best.
            // E.g., we can return a class instance, or just `new self()` if methods are adjusted.
            return new static(); 
        }

        public function auth() {
            if (self::$lastRouteUri && isset(self::$routes[self::$lastRouteUri])) {
                self::$routes[self::$lastRouteUri]['auth'] = true;
            }
            return $this; // Allows further chaining if needed
        }

        public function capture($Dependencies = []) {
            $webRoutesFile = ABSPATH . "/routes/Web.php";
            if (file_exists($webRoutesFile)) {
                include_once $webRoutesFile;
            }

            $Uri = self::getRoute();

            if (array_key_exists($Uri, self::$routes)) {
                $routeData = self::$routes[$Uri];
                
                // Check if route requires authentication
                if ($routeData['auth']) {
                    // Implement your check here. Assuming session or auth helper:
                    $isLoggedIn = isset($_SESSION['user']) /* || Auth::check() */; 
                    
                    if (!$isLoggedIn) {
                        header("Location: /login");
                        exit;
                    }
                }

                // Render the mapped view path
                $this->view($routeData['view'], $Dependencies);
                return;
            }

            // 3. Fallback to 404
            if (isset($Dependencies['Templater'])) {
                $data = ["route" => self::getRoute()];
                $Dependencies['Templater']->load("server/404", $data);
            } else {
                http_response_code(404);
                die("404 - Page not found");
            }
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

        public static function getRoute() {
            $Route = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
            $Route = rtrim($Route, "/");
            
            return empty($Route) ? "/" : $Route;
        }
    }