<?php

    namespace SW\Source\Server\Utilities;

    class Text {
        public static function DisplayText(string $data, string $charset = "UTF-8"): string {
            return htmlspecialchars($data, ENT_QUOTES, $charset ?? 'UTF-8');
        }
    }

?>