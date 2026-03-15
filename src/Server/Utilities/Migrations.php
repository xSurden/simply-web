<?php

    namespace SW\Source\Server\Utilities;

    class Migrations {

        /*
            Migrations will be used to fix missing database tables.
        */

        public static function Server_Configs_Check() {
            $Pointer = new \SW\Source\Modules\SimplySql\Pointer();

            $tableName = 'server_configs';
            $tableSchema = [
                'id'           => 'INT AUTO_INCREMENT PRIMARY KEY',
                'config_key'   => 'VARCHAR(64) NOT NULL UNIQUE',
                'config_value' => 'TEXT',
                'description'  => 'VARCHAR(255)',
                'updated_at'   => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ];

            $Pointer->CreateTable($tableName, $tableSchema);
        }

    }