<?php

    $Package = new \App\Blueprint\Datastore\LocalStorage();

    echo "V2 Key: " . $Package->getValue("app.security.recaptcha.google.v2.site_key") . "<br>";
    echo "V2 Secret: " . $Package->getValue("app.security.recaptcha.google.v2.secret_key") . "<br>";

    echo "V3 Key: " . $Package->getValue("app.security.recaptcha.google.v3.site_key") . "<br>";
    echo "V3 Secret: " . $Package->getValue("app.security.recaptcha.google.v3.secret_key") . "<br>";
    echo "V3 Threshold: " . $Package->getValue("app.security.recaptcha.google.v3.threshold") . "<br>";