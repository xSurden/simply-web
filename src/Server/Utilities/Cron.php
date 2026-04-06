<?php

    namespace App\Server\Utilities;

    class Cron {

        private $CronTasks_File = ABSPATH . "/server/data/modules/cron/tasks.php";

        public function run() {

            // Fetch the required cron run classes from /server/data/modules/cron/tasks.php
            $Tasks = include $this->CronTasks_File ?? [];

            if (!is_array($Tasks)) {
                throw new \Exception("Tasks list is not an array\n");
            }

            if (!empty($Tasks) && is_array($Tasks)) {
                foreach ($Tasks as $className => $method) {
                    
                    if (class_exists($className)) {
                        
                        $object = new $className();

                        if (method_exists($object, $method)) {
                            
                            if ($object->$method()) {
                                echo "Successfully ran task: " . $className . "->" . $method . "()\n";
                            } else {
                                echo "Failed to run task: " . $className . "->" . $method . "()\n";
                            }
                            
                        } else {
                            echo "Error: Method '$method' not found in class '$className'\n";
                        }
                    } else {
                        echo "Error: Class '$className' not found.\n";
                    }
                }
                return true;
            }

            throw new \Exception("Cron tasks list is empty");

        }

    }