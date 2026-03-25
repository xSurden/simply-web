<?php

    /*
    |   SimplyWeb | A lightweight - near native PHP framework
    |   Built by Surden (aka Piyarach Muenchana) as a side hobby
    |   This usually should not be modified unless you know what you are doing
    */

    
    /*
    |   This is the CSP protection script
    |   Currently you can load in Tailwind CSS, google fonts and your local files
    |   Other sources will be blocked unless specified below.
    */
    header("Content-Security-Policy: default-src 'self'; " .
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://www.gstatic.com; " .
    "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://www.google.com https://www.gstatic.com; " .
    "frame-src https://www.google.com; " . 
    "font-src 'self' https://fonts.gstatic.com; " .
    "img-src 'self' data: https://www.gstatic.com; " .
    "object-src 'none';");

    
    /*
    |   Initialising secure cookie sessions.
    |   We prefer that you do keep this!
    */
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_only_cookies', 1); 
    session_start([
        'cookie_samesite' => 'Lax'
    ]);

    /*
    |   Suppressing errors so our system can take care of it.
    |   Generally - do not remove this unless you require more in-depth
    |   overview of the errors you may encounter
    */
    error_reporting(E_ALL);
    ini_set('display_errors', 0);

    
    /*
    |   Defining the root base and loading autoloader (Composer)
    |   100% do not remove this!
    */
    define("ABSPATH", __DIR__ . "/..");
    require ABSPATH . "/vendor/autoload.php";


    /*
    |   Loading the classes we need for the index page
    |   These will handle routing, startup of each page and allowing template engine
    |   Will also catch errors and stuff I suppose.
    */
    use \SW\Source\Server\Engine\Router;
    use \SW\Source\Server\Web;
    use \SW\Source\Server\Engine\TemplateEngine;

    try {
        $WebServer = new Web();
        $Router = new Router();

        $route = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $route = rtrim($route, '/');
        if ($route === '') {
            $route = '/';
        }

        // Init web server Web.php file
        $WebServer::Start();

        /*
        |   This is to add dependencies to be used globally throughout the entire
        |   application without initialising it always
        */
        $dependencies = [
            "TemplateEngine" => new TemplateEngine()
        ];

        // Init the route method and route the request(s)
        $Router->Route($route, $dependencies);

    } catch (\Throwable $e) {
        $data = [
            "type" => "System Error",
            "code" => $e->getCode() ?: 500,
            "message" => "Our server encountered a critical issue: " . $e->getMessage()
        ];

        if (str_contains($e->getMessage(), 'cron')) {
            $data['type'] = "Error - Cron Failed";
            $data['message'] = "Our server failed to register certain cron tasks - which has broken our system.";
        }

        TemplateEngine::Render("server/message", $data);
        exit();
    }