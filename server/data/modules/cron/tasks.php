<?php

    /*
    This array will be used to run cron tasks for the specified 

    Example:
    "App\Modules\Email\Marketing\Core" => "runCron"

    First section will be the class and the 2nd section is the method within the class.
    */

    return [
        "App\Addons\TestAddon\Cron" => "run"
    ];

?>