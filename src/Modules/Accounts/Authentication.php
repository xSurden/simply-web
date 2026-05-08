<?php

    namespace App\Modules\Accounts;

    use App\Modules\Database\ElegantHandle;

    class Authentication extends ElegantHandle {

        private $tables = [
            "tbl_users",
            "tbl_sessions",
        ]; 

        public function __construct() {

            // Check if the user table exists
            $this->checkTables();
            
        }

        private function checkTables() {
            $val = true; // Passed by default
            foreach ($this->tables as $table) {
                if (!$this->fetchTable($table)) {
                    $val = false;
                }
            }

            return $val ?? false;
        }

    }

?>