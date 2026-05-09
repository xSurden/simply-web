<?php

    namespace App\Server\Handle;

    class Router {

        public function view($path = null, $data = []) {
            if ($path === null) {
                throw new \Exception("Unable to load the view as it is not set");
            }

            $pathWithExt = str_ends_with($path, ".php") ? $path : $path . ".php";
            $filePath = ABSPATH . "/resources/views/" . $pathWithExt;

            if (!file_exists($filePath)) {
                throw new \Exception("View path not found: " . $filePath);
            }

            $deps = new \App\Server\Dependencies();
            $viewVariables = array_merge($deps->fetch(), $data);
            
            extract($viewVariables);

            include $filePath;
        }
    }