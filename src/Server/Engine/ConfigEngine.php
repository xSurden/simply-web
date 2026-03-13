<?php

    namespace SW\Source\Server\Engine;

    class ConfigEngine {

        public static function GetValue($key) {
            $config = require ABSPATH . "/settings.php";
            return $config[$key] ?? null;
        }

    }

?>