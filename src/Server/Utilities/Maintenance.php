<?php

    namespace App\Server\Utilities;

    class Maintenance {

        private $file_path = ABSPATH . "/server/storage/maintenance.php";

        // Internal helper so we don't return multiple times during toggle
        public function status() {
            return file_exists($this->file_path);
        }

        public function on() {
            $dir = dirname($this->file_path);
            
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $result = file_put_contents($this->file_path, "<?php return ['enabled' => true, 'since' => " . time() . "];") !== false;
            
            if ($result) {
                return "Maintenance is now ON";
            }
            return $result;
        }

        public function off() {
            if (file_exists($this->file_path)) {
                $result = unlink($this->file_path);
                if ($result) {
                    return "Maintenance is now OFF";
                }
                return $result;
            }
            
            return "Maintenance was already OFF";
            return true;
        }
    }