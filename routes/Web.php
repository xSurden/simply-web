<?php

    // Get the Router class
    use App\Blueprint\Web\Router;

    // Defined Routes below
    /*
    Example: Router::get("/example", "app/demo/example");
    */
    Router::get('/', 'home');

    /*
    Testing routes for any references in case you are stuck
    and are unsure of how to use or what to do with each built-in feature
    */

    // Route for our built-in authentication system (Identity)
    Router::get("/login", "identity/login");
    Router::get("/register", "identity/register");

    // (DEV) Testing logged in route
    Router::get("/dashboard", "identity/dashboard")->auth(); // Optional (verified) field