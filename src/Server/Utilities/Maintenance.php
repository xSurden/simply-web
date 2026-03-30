<?php

    namespace SW\Source\Server\Utilities;

    use Exception;

    class Maintenance {

        private static $Pointer;
        private $server_configs_field = "server_maintenance_status";
        private static $maintenance_file = ABSPATH . '/server/maintenance';

        public function __construct() {
            if (self::$Pointer === null) {
                self::$Pointer = new \SW\Source\Server\Database\Pointer();
            }

            $existingConfigs = self::$Pointer->FetchAllFromTable("server_configs");
            $keys = array_column($existingConfigs ?? [], 'config_key');

            if (!in_array($this->server_configs_field, $keys)) {
                try {
                    $dat = [
                        "config_key" => $this->server_configs_field,
                        "config_value" => "false",
                        "description" => "Maintenance mode status"
                    ];
                    self::$Pointer->Insert("server_configs", $dat);
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), '1062') === false) {
                        throw new Exception("Unable to set maintenance mode: " . $e->getMessage());
                    }
                }
            }
        }

        public function Toggle(string $action = "disable") {
            if ($action === "enable") {
                if (!file_exists(dirname(self::$maintenance_file))) {
                    mkdir(dirname(self::$maintenance_file), 0755, true);
                }
                file_put_contents(self::$maintenance_file, 'Active since: ' . date('Y-m-d H:i:s'));
                $dbValue = "true";
            } else {
                if (file_exists(self::$maintenance_file)) {
                    unlink(self::$maintenance_file);
                }
                $dbValue = "false";
            }

            $data = ["config_value" => $dbValue];
            $where = ["config_key" => $this->server_configs_field];

            return self::$Pointer->Update("server_configs", $data, $where);
        }

        public static function Status() {
            if (file_exists(self::$maintenance_file)) {
                return true;
            }
            return false;
        }
    }