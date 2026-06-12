<?php

    namespace App\Frame\Server;

    use App\Frame\Server\Modules\CommandWrapper;

    class Gateway extends CommandWrapper {

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
            return implode("\n", $result) ?? [];
        }

        public function runSchema($file = null) {

            if ($file === null) {
                echo "Command schema was not provided.";
                return;
            }

            if ($this->runCommand($this->getSchemaFileContent($file) ?? "echo Unable to load command schema.")) {
                echo "Ran command schema successfully: {$file}";    
            } else {
                echo "Failed to execute command schema: {$file}";
            }

        }
    }