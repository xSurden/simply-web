<?php

    namespace App\Server\Utilities;

    class Cli {

        public function __construct() {
            $Dependencies = new \App\Server\Dependencies();
            $Dependencies->fetch();
        }

        /**
         * Handles the execution of a CLI command.
         * * @param string|null $command The Swiften module name (e.g., 'Maintenance')
         * @param string|null $method  The action to perform (e.g., 'on', 'off', 'status')
         */
        public function command($command = null, $method = null) {
            if (!$command) {
                die("\nError: Command (module name) is not defined.\nUsage: php swiften [ModuleName] [MethodName]\nExample: php swiften Maintenance on\n\n");
            }

            // Format the class name (e.g., maintenance -> Maintenance)
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
            $action = $method ? strtolower($method) : 'toggle';

            // Check if the method exists on the target class
            if (!method_exists($instance, $action)) {
                die("\nError: '{$swiftenModule}' does not have default/togglable method.\n\n");
            }

            // Execute the action
            $instance->$action();
            echo "\n"; // Just a clean line break for the terminal output
        }
    }