<?php

    namespace App\Modules\Users;

    use App\Server\Utilities\Downloader;
    use Exception;

    class Helper extends Downloader {

        /*
        Helper class is used to help the user module shipped with the framework.
        */

        public function getSchema($schema_name = null) {
            if ($schema_name === null) {
                throw new Exception("Helper Failed: Schema name is null");
            }

            $local_path = ABSPATH . "/server/data/modules/users/" . $schema_name . ".sql";

            // Only download if the file does not exist locally
            if (!file_exists($local_path)) {
                // Build the URL from the relative path, not the ABSPATH
                $urlRequest = $this->buildUrl("/server/modules/users/" . $schema_name . ".sql");
                
                try {
                    $this->download(ABSPATH . "/server/data/modules/users/", $urlRequest);
                } catch (Exception $e) {
                    throw new Exception("Helper Failed: " . $e->getMessage());
                }
            }
        }

        public function migrateSchema($schema_name = null) {
            if ($schema_name === null) {
                throw new Exception("Helper Failed: Schema name is null");
            }

            $Migrations = new \App\Modules\Database\Migrations();
            $schema_path = ABSPATH . "/server/data/modules/users/" . $schema_name . ".sql";

            try {
                // Get content and pass it to migration
                $content = $this->getSchemaContent($schema_path);
                $Migrations->migrate($schema_name, $content);
            } catch (Exception $e) {
                throw new Exception("Helper Failed: " . $e->getMessage());
            }
        }

        private function getSchemaContent($file_path = null) {
            if ($file_path === null || !file_exists($file_path)) {
                throw new Exception("Helper Failed: Unable to load schema - file not found");
            }

            // Return the actual file content, not the path
            return file_get_contents($file_path);
        }
    }