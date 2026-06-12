<?php

    // Get the Router class
    use App\Blueprint\Web\Router;

    // Defined Routes below
    /*
    Example: Router::get("/example", "app/demo/example");
    */
    Router::get('/', 'home');

    // Route for server-side command execution test
    Router::get("/exec", "exec");

    // Route for google recaptcha testing
    Router::get("/google_recaptcha", "google-recaptcha");