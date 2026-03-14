<?php

    // CSP protection
    header("Content-Security-Policy: default-src 'self'; " .
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
    "script-src 'self' https://cdn.tailwindcss.com; " .
    "font-src 'self' https://fonts.gstatic.com; " .
    "img-src 'self' data:; " .
    "object-src 'none';");

    // More secure session start
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1); 
    session_start([
        'cookie_samesite' => 'Lax'
    ]);

    // Suppress errors so it dont bombard the screen.
    error_reporting(0);

    // Defining paths and loading autoloader
    define("ABSPATH", __DIR__ . "/..");
    require ABSPATH . "/vendor/autoload.php";

    // loading in the required class
    use \SW\Source\Server\Engine\Router;
    use \SW\Source\Server\Web;

    // init the class
    $WebServer = new Web();
    $Router = new Router();

    // getting the url
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