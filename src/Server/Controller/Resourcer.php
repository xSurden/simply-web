<?php

    namespace App\Server\Controller;

    use Exception;

    class Resourcer {

        /**
         * Loads a resource and injects optional data.
         * * @param string|null $path The path relative to /resources/
         * @param array $data Associative array of variables to pass to the resource
         */
        public function get($path = null, array $data = []) {
            if ($path === null) {
                throw new Exception("Unable to load the resource as the path was null");
            }

            $builtPath = ABSPATH . "/resources/" . $path;

            if (!file_exists($builtPath)) {
                throw new Exception("Resource path: /resources/" . $path . " does not exist");
            }

            if (is_dir($builtPath)) {
                throw new Exception("Loading folders is unsupported: /resources/" . $path);
            }

            // Extract the data array into the local symbol table
            // EXTR_SKIP ensures we don't overwrite existing variables like $builtPath
            if (!empty($data)) {
                extract($data, EXTR_SKIP);
            }

            include $builtPath;
            return;
        }
    }