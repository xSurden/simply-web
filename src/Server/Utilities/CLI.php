<?php

    namespace App\Server\Utilities;

    class CLI {

        private $Maintenance;
        private $Cron;

        public function __construct() {
            $this->Maintenance = new \App\Server\Utilities\Maintenance();
            $this->Cron = new \App\Server\Utilities\Cron();
        }

        public function run($class_name = null, $method = null) {

            try {
                if (!$class_name || !$method) {
                    return false;
                }

                // Ensure we match the property name exactly (Maintenance)
                $prop = ucfirst(strtolower($class_name));

                // Check if the property exists on this class instance
                if (property_exists($this, $prop) && $this->$prop !== null) {
                    $service = $this->$prop;

                    if (method_exists($service, $method)) {
                        return $service->$method();
                    }
                }

                throw new \Exception("[CLI] Error: Command '$prop:$method' not found." . PHP_EOL);

            } catch (\Exception $e) {
                die("CLI Failure: " . $e->getMessage() . PHP_EOL);
            }
            
        }
    }