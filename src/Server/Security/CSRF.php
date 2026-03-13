<?php

    namespace SW\Source\Server\Security;

    class CSRF {

        public static function Generate(): void {
            if (empty($_SESSION['csrf_token'])) {
                self::Rotate();
            }
        }

        public static function Rotate(): void {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        public static function Insert(): string {
            self::Generate(); 
            $token = $_SESSION['csrf_token'];
            return '<input type="hidden" name="csrf_token" value="' . $token . '">';
        }

        public static function Validate(): bool {
            if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
                return false;
            }

            return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
        }
    }

?>