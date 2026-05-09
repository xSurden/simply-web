<?php

    namespace App\Server\Handle;

    class Exception {
        public function new($text = null) {
            if ($text === null) {
                $text = "Exception is not handled properly and no text was provided";
            }

            throw new \Exception($text);
        }
    }

?>