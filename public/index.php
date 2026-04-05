<?php

    /*
    SimplyWeb | A lightweight - near native PHP framework
    Built by Surden (aka Piyarach Muenchana) as a side hobby
    This usually should not be modified unless you know what you are doing
    */

    // Suppressing the error
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Loading required definitions & load composer
    define("ABSPATH", __DIR__ . "/..");
    define("SW_START", microtime(true));
    require ABSPATH . "/vendor/autoload.php";

    
    // Check if maintenance is enabled..
    if (file_exists(ABSPATH . "/server/maintenance.php")) {
        die("Application in maintenance mode");
    }


    /*
    Start of the application
    */
    $WebServer = new \App\Server\Web();
    $WebServer->Start();

?>