<?php

    namespace App\Server\Utilities;

    class CLI {

        /**
         * Dynamically run a utility class method
         * @param string $class_name The name of the utility (e.g., 'Cron' or 'Maintenance')
         * @param string $method     The method to fire (e.g., 'run' or 'on')
         */

        public function run($class_name = null, $method = null) {
            try {
                if (!$class_name || !$method) {
                    return false;
                }

                if (strpos($class_name, 'App\\') === 0) {
                    $fullClass = $class_name;
                } else {
                    $formattedClassName = ucfirst(strtolower($class_name));
                    $fullClass = __NAMESPACE__ . "\\" . $formattedClassName;
                }

                if (class_exists($fullClass)) {
                    $service = new $fullClass();

                    if (method_exists($service, $method)) {
                        return $service->$method();
                    }
                }

                throw new \Exception("Command '$class_name:$method' not found (Checked: $fullClass)");

            } catch (\Exception $e) {
                die("CLI Failure: " . $e->getMessage() . PHP_EOL);
            }
        }
    }