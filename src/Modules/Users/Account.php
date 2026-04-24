<?php


    namespace App\Modules\Users;

    use App\Modules\Database\ElegantHandle;

    class Account extends ElegantHandle {


        /*
        Variables that will be used here
        */

        private $schema_folder = "/server/data/modules/users";


        /*
        Ensure that all of the required table exists
        */
        public function __construct() {

            // Check if folder for server data exists
            if (!is_dir($this->schema_folder)) {
                try {
                    mkdir($this->schema_folder);
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }

            // Check default schemas - if not available, download from repo.
            $Helper = new \App\Modules\Users\Helper();
            // Fetch the php file and then load all helpers
            $schema_file = require $this->schema_folder . "/schema.php";
            foreach ($schema_file as $schema) {
                $Helper->getSchema($schema);
            }

            // Perform migration if table does not exist
        }



        /*
        Public methods such as: login, register, getSession, logout
        */


        // Get session
        public function getSession() {
        }

    }