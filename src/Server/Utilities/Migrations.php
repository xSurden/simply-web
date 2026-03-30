<?php

    namespace SW\Source\Server\Utilities;

    class Migrations {

        private static $Pointer; 
        
        private static $default_tables = [
            "server_configs" => [
                'id'           => 'INT AUTO_INCREMENT PRIMARY KEY',
                'config_key'   => 'VARCHAR(64) NOT NULL UNIQUE',
                'config_value' => 'TEXT',
                'description'  => 'VARCHAR(255)',
                'updated_at'   => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]
        ];

        private static function getPointer() {
            if (self::$Pointer === null) {
                self::$Pointer = new \SW\Source\Server\Database\Pointer();
            }
            return self::$Pointer;
        }

        private static function MigrateTable($tableName, $schema) {
            self::getPointer()->CreateTable($tableName, $schema);
        }

        public static function Init() {
            $pointer = self::getPointer();
            $FetchedTables = $pointer->FetchTables();

            foreach (self::$default_tables as $tableName => $schema) {
                if (!in_array($tableName, $FetchedTables)) {
                    self::MigrateTable($tableName, $schema);
                }
            }
        }
    }