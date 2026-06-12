<?php

    $Gateway = new \App\Frame\Server\Gateway();

    echo $Gateway->runCommand("ipconfig");