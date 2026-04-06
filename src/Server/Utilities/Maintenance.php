<?php

    namespace App\Server\Utilities;

    class Maintenance {

        private $file_path = ABSPATH . "/server/storage/maintenance.php";

        public function status() {
            return file_exists($this->file_path);
        }

        public function on() {
            $dir = dirname($this->file_path);
            
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            return file_put_contents($this->file_path, "<?php return ['enabled' => true, 'since' => " . time() . "];") !== false;
        }

        public function off() {
            if (file_exists($this->file_path)) {
                return unlink($this->file_path);
            }
            
            return true;
        }
    }