<?php

    echo "Loaded Page Before DB Query: " . $Environment->getMicroTime() . "ms<br><br>";

    // Init ElegantSql class
    $ElegantSql = new \App\Modules\Database\ElegantHandle(); 

    // Created post button
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $array = [
            'id'           => 'INT AUTO_INCREMENT PRIMARY KEY',
            'config_key'   => 'VARCHAR(64) NOT NULL UNIQUE',
            'config_value' => 'TEXT',
            'description'  => 'VARCHAR(255)',
            'updated_at'   => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ];
        $ElegantSql->createTable("server_configurations", $array);
    }

    $db_fetched = $ElegantSql->fetchTables();
    if(!empty($db_fetched)) {
        var_dump($db_fetched);
    } else {
        echo "Database does not contain a single table";
    }

    echo "<br><br>Completed this page in: " . $Environment->getMicroTime() . "ms";

?>


<form action="" method="POST">
    <button type="submit">Create A New Table</button>
</form>