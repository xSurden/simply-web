<?php

    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1); 
    session_start([
        'cookie_samesite' => 'Lax'
    ]);
    error_reporting(0);

    define("ABSPATH", __DIR__ . "/..");
    require ABSPATH . "/vendor/autoload.php";

    use SW\Source\Server\Engine\Router;
    use SW\Source\Server\Web;
    $WebServer = new Web();
    $Router = new Router();

    $route = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $route = rtrim($route, '/');
    if ($route === '') {
        $route = '/';
    }

    // Start the web server module
    $WebServer->Start();

    // Render the route
    $Router->Route($route);

?>