<?php

    session_start();

    define("ABSPATH", __DIR__ . "/..");
    require ABSPATH . "/vendor/autoload.php";

    use SW\Source\Engine\Router;
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