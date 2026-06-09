<?php

    namespace App\Swiften;

    class LocalStorage {

        private $Package;

        public function __construct() {
            $this->Package = new \App\Blueprint\Datastore\LocalStorage();
        }

        public function getValue($value = null) {
            if ($value === null) {
                echo "\n" . "Error: Missing value name.\nUsage: php swiften localstorage getvalue [value_name]\n";
                return;
            }

            $result = $this->Package->getValue($value);

            if ($result !== null) {
                echo "\n" . $result . "\n";
            } else {
                echo "\n(null)\n";
            }
        }

        /**
         * Set or update a local storage variable from CLI
         */
        public function setValue($key = null, $value = null) {
            if ($key === null) {
                echo "\n" . "Error: Missing value name.\nUsage: php swiften localstorage setvalue [value_name] [value_data]\n";
                return;
            }

            // Run the UPSERT query via Datastore\LocalStorage
            $success = $this->Package->setValue($key, $value);

            if ($success) {
                echo "\n" . "Successfully updated local store: '{$key}' => " . ($value ?? 'NULL') . "\n";
            } else {
                echo "\n" . "Failed to save variable to local storage.\n";
            }
        }

        /**
         * Explicitly update an existing key from CLI
         */
        public function updateValue($key = null, $value = null) {
            if ($key === null) {
                echo "\n" . "Error: Missing value name.\nUsage: php swiften localstorage updatevalue [value_name] [value_data]\n";
                return;
            }

            $success = $this->Package->updateValue($key, $value);

            if ($success) {
                echo "\n" . "Successfully updated local store: '{$key}' => " . ($value ?? 'NULL') . "\n";
            } else {
                echo "\n" . "Failed: Variable '{$key}' does not exist. Use 'setvalue' to create it.\n";
            }
        }

        /**
         * Delete a key entirely from storage
         */
        public function dropValue($key = null) {
            if ($key === null) {
                echo "\n" . "Error: Missing value name to drop.\nUsage: php swiften localstorage dropvalue [value_name]\n";
                return;
            }

            $success = $this->Package->dropValue($key);

            if ($success) {
                echo "\n" . "Successfully dropped '{$key}' from store.\n";
            } else {
                echo "\n" . "Notice: '{$key}' did not exist in storage.\n";
            }
        }

        /**
         * Check if a key exists in storage
         */
        public function hasValue($key = null) {
            if ($key === null) {
                echo "\n" . "Error: Missing value name.\nUsage: php swiften localstorage hasvalue [value_name]\n";
                return;
            }

            $exists = $this->Package->hasValue($key);
            echo "\n" . $exists ? "Key '{$key}' exists.\n" : "Key '{$key}' does not exist.\n";
        }

        /**
         * Display all stored variables in a clean tabular layout
         */
        public function list() {
            $all = $this->Package->getAllValues();

            if (empty($all)) {
                echo "\nLocal storage database is currently empty.\n";
                return;
            }

            echo sprintf("\n%-30s | %s\n", "STORED KEY", "VALUE");
            echo str_repeat("-", 60) . "\n";
            
            foreach ($all as $key => $val) {
                $displayVal = ($val === null) ? "[NULL]" : $val;
                
                // Trim long configurations cleanly so they don't visually ruin your layout
                if (strlen($displayVal) > 40) {
                    $displayVal = substr($displayVal, 0, 37) . "...";
                }
                echo sprintf("%-30s | %s\n", $key, $displayVal);
            }
        }
        
        public function toggle() {
            echo "\nSwiften's LocalStorage Cli module\nVersion 1.2\n\n";
            echo "Usage:\n";
            echo "  php swiften localstorage list\n";
            echo "  php swiften localstorage hasvalue    [key]\n";
            echo "  php swiften localstorage getvalue    [key]\n";
            echo "  php swiften localstorage setvalue    [key] [value]\n";
            echo "  php swiften localstorage updatevalue  [key] [value]\n";
            echo "  php swiften localstorage dropvalue   [key]\n";
        }
    }