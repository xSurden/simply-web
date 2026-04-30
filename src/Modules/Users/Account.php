<?php

    namespace App\Modules\Users;

    use App\Modules\Database\ElegantHandle;
    use Exception;

    class Account extends ElegantHandle {

        private $schema_folder = "/server/data/modules/users";
        private $table = "users";

        public function __construct() {
            
            // Initialize the database connection from ElegantHandle
            parent::__construct();

            // Ensure schema directory exists
            if (!is_dir($this->schema_folder)) {
                if (!mkdir($this->schema_folder, 0755, true)) {
                    throw new Exception("Failed to create schema directory.");
                }
            }

            $Helper = new \App\Modules\Users\Helper();
            $schema_file = $this->schema_folder . "/schema.php";
            
            if (file_exists($schema_file)) {
                $schemas = require $schema_file;
                foreach ($schemas as $schema) {
                    $Helper->getSchema($schema);
                    $Helper->migrateSchema($schema);
                }
            }
        }

        /**
         * Register a new user
         */
        public function registerUser($data = []) {
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                throw new Exception("Registration requires username, email, and password.");
            }

            // Prevent duplicate accounts
            if ($this->count($this->table, ['email' => $data['email']]) > 0) {
                throw new Exception("Email is already registered.");
            }

            $userData = [
                'username' => $data['username'],
                'email'    => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s')
            ];

            return $this->insert($this->table, $userData);
        }

        /**
         * Login user
         */
        public function login($email, $password) {
            $user = $this->select($this->table, ['email' => $email]);
            $user = is_array($user) && isset($user[0]) ? $user[0] : null;

            if (!$user || !password_verify($password, $user['password'])) {
                throw new Exception("Invalid credentials.");
            }

            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user_id'] = $user['id'];
            return true;
        }

        /**
         * Get current session user ID
         */
        public function getSession() {
            if (session_status() === PHP_SESSION_NONE) session_start();
            return $_SESSION['user_id'] ?? null;
        }

        /**
         * Terminate session
         */
        public function logout() {
            if (session_status() === PHP_SESSION_NONE) session_start();
            session_unset();
            session_destroy();
        }

        /**
         * Reset password for an existing user
         */
        public function resetPassword($userId, $newPassword) {
            return $this->update($this->table, 
                ['password' => password_hash($newPassword, PASSWORD_BCRYPT)], 
                ['id' => $userId]
            );
        }

        /**
         * Delete user account
         */
        public function deleteAccount($userId) {
            $this->logout();
            return $this->delete($this->table, ['id' => $userId]);
        }
    }