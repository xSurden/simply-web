<?php

    namespace SW\Source\Engine;

    class ConfigEngine {

        public static function GetValue($key) {
            $config = require ABSPATH . "/settings.php";
            return $config[$key] ?? null;
        }

    }

?>