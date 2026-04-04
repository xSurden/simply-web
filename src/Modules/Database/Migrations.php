<?php

    namespace App\Modules\Database;

    class Migrations {

        private $ElegantHandle;

        public function __construct()
        {
            if (!isset($ElegantHandle)) {
                $this->ElegantHandle = new \App\Modules\Database\ElegantHandle();
            }
        }

        public function migrate($table_name = null, $data_array = []) {

            if ($table_name === null || empty($data_array)) {
                return false;
            }

            if ($this->ElegantHandle->createTable($table_name, $data_array)) {
                return true;
            }

            return false;

        }

    }