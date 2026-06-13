<?php

    namespace App\Frame;

    class Gateway {

        /*
        \Frame\Gateway is a script that is used to directly run system-side commands. 

        This feature is experimental and can be dangerous to use (as of writing)
        */

        public function __construct() {

            if (function_exists('exec')) {
                $output = [];
                $resultCode = null;
                
                // Run a harmless command (works on both Linux and Windows)
                @exec('whoami', $output, $resultCode);
                
                if ($resultCode === 0 && !empty($output)) {
                    return;
                } else {
                    throw new \Exception("The exec() function exists, but the server blocked the command execution (Exit code: $resultCode).");
                }
            } else {
                throw new \Exception("The exec() function is completely disabled or unavailable on this server.");
            }

        }

        public function runCommand($command = null) {
            if ($command === null) {
                return null;
            }

            $outputLines = [];
            $resultCode = null;

            exec($command, $outputLines, $resultCode);

            if (!empty($outputLines)) {
                return $outputLines;
            }

            return null;
        }

        public function expandResult($result = null) {
            return is_array($result) ? implode("\n", $result) : (string)$result;
        }

        public function runSchema($file = null) {

            if ($file === null) {
                echo "Command schema was not provided.\n";
                return;
            }

            $schemaContent = $this->getSchemaFileContent($file);

            if ($schemaContent === null) {
                echo "Failed to execute command schema: '{$file}' (Schema file template not found).\n";
                return;
            }

            // Normalize cross-platform line endings (\r\n to \n) before splitting
            $normalizedContent = str_replace("\r\n", "\n", $schemaContent);
            $commands = explode("\n", $normalizedContent);
            
            foreach ($commands as $command) {
                $trimmedCommand = trim($command);
                
                if (empty($trimmedCommand)) {
                    continue;
                }

                // Run command silently and capture output array
                $result = $this->runCommand($trimmedCommand);

                if ($result !== null && is_array($result)) {
                    // Rely cleanly on native command line-breaks without appending extra \n spacing gaps
                    echo implode("\n", $result);
                }
            }
        }

        public function getSchemaFileContent($path = null) {

            if ($path === null) {
                throw new \Exception("Schema path cannot be null for App\Frame\Gateway\CommandWrapper");
            }

            $path = ABSPATH . "/server/data/app/frame/interface/schema/" . $path . ".txt";

            if (file_exists($path)) {
                return file_get_contents($path) ?: null;
            }

            return null;
        }
    }