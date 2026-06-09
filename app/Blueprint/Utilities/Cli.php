<?php

    namespace App\Blueprint\Utilities;

    class Cli {

        public function __construct() {
            $Dependencies = new \App\Blueprint\Web\Dependencies();
            $Dependencies->fetch();
        }

        /**
         * Handles the execution of a CLI command.
         * * @param string|null $command The Swiften module name (e.g., 'Maintenance', 'LocalStorage')
         * @param string|null $method  The action to perform (e.g., 'on', 'getvalue')
         * @param mixed       ...$args Any trailing arguments passed via terminal (e.g. key names)
         */
        public function command($command = null, $method = null, ...$args) {
            if (!$command) {
                die("\nError: Command (module name) is not defined.\nUsage: php swiften [ModuleName] [MethodName] [Arguments...]\nExample: php swiften Maintenance on\n\n");
            }

            // Format the class name (e.g., localstorage -> LocalStorage)
            $swiftenModule = ucfirst(strtolower($command));
            $filePath = ABSPATH . "/app/Swiften/" . $swiftenModule . ".php";

            // Check if the requested module file exists
            if (!file_exists($filePath)) {
                die("\nError: Swiften module '{$swiftenModule}' not found at {$filePath}\n\n");
            }

            // Build the fully qualified class name using its namespace
            $className = "\\App\\Swiften\\" . $swiftenModule;

            // Ensure the class exists (handled by Composer autoload, but good to check)
            if (!class_exists($className)) {
                // If composer hasn't picked it up yet, fallback to manual require
                require_once $filePath;
            }

            if (!class_exists($className)) {
                die("\nError: Class '{$className}' could not be resolved.\n\n");
            }

            // Instantiate the Swiften module dynamically
            $instance = new $className();

            // Determine the action method: Use user input if provided, otherwise default to 'toggle'
            // Keeping it lowercase/preserving case depending on how you name methods. 
            // Using regular input case or lowercase depending on preference.
            $action = $method ? $method : 'toggle';

            // Check if the method exists on the target class
            if (!method_exists($instance, $action)) {
                die("\nError: '{$swiftenModule}' does not have a '{$action}' method.\n\n");
            }

            // Execute the action, unpacking any extra trailing parameters into it
            $instance->$action(...$args);
            
            echo "\n"; // Just a clean line break for the terminal output
        }
    }