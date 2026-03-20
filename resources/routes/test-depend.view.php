<?php

    $data = [
        "type" => "View OK",
        "code" => 200,
        "message" => "Web view using template-engine is working"
    ];
    $TemplateEngine::Render("server/message", $data);

?>