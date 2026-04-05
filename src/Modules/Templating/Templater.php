<?php

    namespace App\Modules\Templating;

    class Templater {

        public function load($template_name, $additional_data = []) {
            if (!isset($template_name)) {
                throw new \Exception("Unable to load template - template name not specified");
                return;
            }

            if (!empty($additional_data)) {
                extract($additional_data);

                // Ensure dependencies exist -- may limit performance
                $Dependencies = new \App\Server\Dependencies();
                extract($Dependencies->fetch());
                ob_start();
            }

            $template_path = ABSPATH . "/server/data/modules/templating/templates/" . $template_name . ".php";

            if (file_exists($template_path)) {
                include $template_path;
                return;
            }

            // if template is not found
            throw new \Exception("Unable to load a template specified: " . $template_name);
            return;
        }

    }