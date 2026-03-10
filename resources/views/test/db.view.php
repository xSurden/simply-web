<?php

    // Use the database class and create connection
    $DBInstance = new \SW\Source\Model\Database();

    $result = $DBInstance->FetchField("test_data", "id", 1);

    if ($result) {
        echo "DB is working and test value fetched: " . $result['test_field'];
    } else {
        echo "Failed to fetch data from the provided database.";
    }

?>