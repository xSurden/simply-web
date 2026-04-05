<?php

    namespace App\Modules\Security;

    class CSRF {

        public function load() {
            $this->generate();
            $token = $_SESSION['csrf_token'];
            echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
        }

        public function validate(bool $rotate = false) {
             if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
                throw new \Exception("Unable to validate CSRF token: Missing?");
            }

            $isValid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);

            if (!$isValid) {
                throw new \Exception("Unable to validate CSRF token: Invalid Token");
            }

            if ($rotate) {
                $this->rotate();
            }

            return true;
        }

        private function generate() {
            if (empty($_SESSION["csrf_token"])) {
                $this->rotate();
            }
        }

        private function rotate() {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }



    }