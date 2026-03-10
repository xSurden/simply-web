<?php

    session_start();

    define("ABSPATH", __DIR__ . "/..");
    require ABSPATH . "/vendor/autoload.php";

    use SW\Source\Model\Router;
    $Router = new Router();

    $route = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $route = rtrim($route, '/');
    if ($route === '') {
        $route = '/';
    }

    $Router->Route($route);

?>