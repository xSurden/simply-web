<?php

    namespace SW\Source\Server\Engine;

    class TemplateEngine {
        public static function Render($template, $data = []) {
            // Render the template with the provided data
            if (!isset($template)) {
                throw new \Exception("Template not found.");
            }

            $template = ABSPATH . "/resources/templates/" . $template . ".template.php";
            if (!file_exists($template)) {
                throw new \Exception("Template file does not exist.");
            }

            // Extract data to variables
            extract($data);
            ob_start();
            include $template;
        }
    }

?>