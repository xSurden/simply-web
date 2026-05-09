<?php

    namespace App\Server\Controller;

    use Exception;

    class Resourcer {

        public function get($path = null) {
            if ($path === null) {
                throw new \Exception("Unable to load the resource as the path was null");
            }

            $builtPath = ABSPATH . "/resources/" . $path;

            if (!file_exists($builtPath)) {
                throw new \Exception("Resource path: /resources/" . $path . " does not exist");
            }

            if (is_dir($builtPath)) {
                throw new \Exception("Loading folders is unsupported: /resources/" . $path);
            }

            include $builtPath;
            return;
        }

    }