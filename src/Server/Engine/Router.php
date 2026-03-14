<?php

    namespace SW\Source\Server\Engine;

    class Router {
        public function Route($path) {
            if ($path == "/") {
                $path = "index";
            }

            if (!file_exists(ABSPATH . "/resources/routes/" . $path . ".view.php")) {
                http_response_code(404);
                include ABSPATH . "/resources/templates/errors/404.view.php";
                return;
            }
            include ABSPATH . "/resources/routes/" . $path . ".view.php";
        }
    }

?>