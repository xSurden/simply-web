<?php


    /*
    The default routing page for the index page.
    Utilising the folder/file method without complicated setup.
    */

    // Loading example classes you can use
    $Resourcer = new \App\Server\Controller\Resourcer();

    $Resourcer->get("simple-text.php");