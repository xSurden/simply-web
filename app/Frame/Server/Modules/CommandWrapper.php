<?php

    namespace App\Frame\Server\Modules;

    class CommandWrapper {

        public function getSchemaFileContent($path = null) {

            if ($path === null) {
                throw new \Exception("Schema path cannot be null for App\Frame\Server\Gateway\CommandWrapper");
            }

            $path = ABSPATH . "/server/data/app/frame/interface/schema/" . $path;

            if (file_exists($path)) {
                return file_get_contents($path) ?: null;
            }

            return null;
        }
    }