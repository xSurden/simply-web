<?php

    namespace SW\Source\Server\Utilities;

    class Migrations {

        private $Pointer;
        private static $table_server_configs = "server_configs";

        public function __construct()
        {
            if ($this->Pointer === null) {
                $this->Pointer = new \SW\Source\Modules\SimplySql\Pointer();
            }
        }

        public static function Init() {

            $existingTables = self::$Pointer->FetchTables();
            
            if (!in_array(self::$table_server_configs, $existingTables)) {
                self::DefaultMigrations();
            }

            $result = self::$Pointer->FetchField(self::$table_server_configs, "config_key", "package_repository_url");
            
            if ($result === null) {
                $data = [
                    "config_key"   => "package_repository_url",
                    "config_value" => "https://repo.surden.me/packages/",
                    "description"  => "The repo url - default is standard and shipped with framework"
                ];
                self::$Pointer->Insert(self::$table_server_configs, $data);
            }
        }

        private static function DefaultMigrations() {

            $tableSchema = [
                'id'           => 'INT AUTO_INCREMENT PRIMARY KEY',
                'config_key'   => 'VARCHAR(64) NOT NULL UNIQUE',
                'config_value' => 'TEXT',
                'description'  => 'VARCHAR(255)',
                'updated_at'   => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ];

            self::$Pointer->CreateTable(self::$table_server_configs, $tableSchema);
        }
    }