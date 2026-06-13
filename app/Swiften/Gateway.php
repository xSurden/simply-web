<?php

    namespace App\Swiften;

    class Gateway {

        private $Package;

        public function __construct() {
            $this->Package = new \App\Frame\Gateway();
        }

        public function runSchema($file = null) {
            $this->Package->runSchema($file);
        }

        public function runCommand($command = null, $expand = false) {
            
            $result = $this->Package->runCommand($command);

            if ($result === null || $result === false) {
                echo "Failed to execute or command returned no output.\n";
                return;
            }

            // If asked to expand, prepare the result and pass it downstream
            if (strtolower($expand) === "true") {
                // Convert the raw multi-line string into an array of individual lines
                $resultArray = is_array($result) ? $result : explode("\n", $result);

                if (method_exists($this->Package, 'expandResult')) {
                    // 🚀 FIXED: Echo the expanded data block to the terminal screen
                    echo $this->Package->expandResult($resultArray) ?? "Failed to expand object.\n";
                } else {
                    // Fallback output formatting if expandResult doesn't exist
                    echo "--- Expanded Result Output ---\n";
                    print_r($resultArray);
                }
                return;
            }
            
            // 🚀 FIXED: Echo the raw multi-line string block to the terminal screen 
            // when NOT asked to expand.
            echo "\nReturned data is an array.\n";
        }

        public function toggle() {
            echo "\nSwiften's Gateway Management CLI Module\n";
            echo "========================================\n";
            echo "Usage:\n";
            echo "  php swiften gateway runCommand <command>\n";
            echo "  php swiften gateway runSchema <file_path>\n";
        }
    }