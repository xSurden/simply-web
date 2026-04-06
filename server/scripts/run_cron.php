<?php

    define("ABSPATH", dirname(__DIR__)); 

    require_once ABSPATH . "/vendor/autoload.php";

    try {
        $CronPkg = new \App\Server\Utilities\Cron();
        $CronPkg->run();
        
        echo "[" . date('Y-m-d H:i:s') . "] Cron executed successfully.\n";
    } catch (\Exception $e) {
        error_log("Cron Failure: " . $e->getMessage());
        echo "Cron Failure: " . $e->getMessage() . "\n";
        exit(1);
    }