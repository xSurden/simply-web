<?php

    $Pkg = new \SW\Source\Server\Utilities\Migrations();
    $Text = new \SW\Source\Server\Utilities\Text();

    // Init migration
    $result = $Pkg::Init();
    if (!$result) {
        $Text::DisplayText("Unable to initilise the database");
        exit;
    }

    $Text::DisplayText("Initilised the database successfully.");
    exit;

?>