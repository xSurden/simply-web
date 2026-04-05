<?php

    namespace App\Server\Utilities;

    class Maintenance {

        private $file_path = ABSPATH . "/server/storage/maintenance.php";

        public function status() {
            if (file_exists($this->file_path)) {
                return true;
            }

            return false;
        }

    }

