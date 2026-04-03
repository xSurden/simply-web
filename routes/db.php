<?php

    echo "Loaded Page Before DB Query: " . $Environment->getMicroTime() . "ms<br><br>";

    // Init ElegantSql class
    $ElegantSql = new \App\Modules\Database\ElegantHandle(); 


    $db_fetched = $ElegantSql->fetchTables();
    if(!empty($db_fetched)) {
        var_dump($db_fetched);
    } else {
        echo "Database does not contain a single table";
    }

    echo "<br><br>Completed this page in: " . $Environment->getMicroTime() . "ms";

?>