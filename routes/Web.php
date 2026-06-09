<?php

    // Get the Router class
    use App\Blueprint\Web\Router;

    // Defined Routes below
    /*
    Example: Router::get("/example", "app/demo/example");
    */
    Router::get('/', 'home');
    Router::get("/google_recaptcha", "google-recaptcha");