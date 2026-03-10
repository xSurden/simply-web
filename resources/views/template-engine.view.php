<?php

    use SW\Source\Engine\TemplateEngine;

    $data = [
        "name" => "Simply-Web",
        "description" => "A simple web application framework built with PHP.",
        "version" => "Beta 0.1",
        "branch" => "development"
    ];
    TemplateEngine::Render("template1", $data);