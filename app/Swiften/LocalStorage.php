<?php

    namespace App\Swiften;

    class LocalStorage {

        private $Package;

        public function __construct() {
            $this->Package = new \App\Blueprint\Datastore\LocalStorage();
        }

        public function getValue($value = null) {
            if ($value === null) {
                echo "Error: Missing value name.\nUsage: php swiften localstorage getvalue [value_name]\n";
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
                echo "Error: Missing value name.\nUsage: php swiften localstorage setvalue [value_name] [value_data]\n";
                return;
            }

            // Run the UPSERT query via Datastore\LocalStorage
            $success = $this->Package->setValue($key, $value);

            if ($success) {
                echo "Successfully updated local store: '{$key}' => " . ($value ?? 'NULL') . "\n";
            } else {
                echo "Failed to save variable to local storage.\n";
            }
        }
        
        public function toggle() {
            echo "\nSwiften's LocalStorage Cli module\nVersion 1.0\n\n";
            echo "Usage:\n";
            echo "  php swiften localstorage getvalue [key]\n";
            echo "  php swiften localstorage setvalue [key] [value]\n";
        }
    }