<?php

    namespace App\Blueprint\Collection\Identity;

    class Authenticate extends Base {

        /*
        This class is part of the Identity collection.
        It is used for managing user credentials, sessions, and registration tasks.
        */

        /**
         * Validates credentials and records the sign-in session event.
         * * @param string $usernameOrEmail
         * @param string $password
         * @return array|false Returns the basic user payload on success, false on failure.
         */
        public function login($usernameOrEmail = null, $password = null) {
            if (empty($usernameOrEmail) || empty($password)) {
                return false;
            }

            // Check if input is an email format or username string
            $searchField = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $user = $this->findIdentityByField($searchField, $usernameOrEmail);

            if (!$user) {
                return false; // Identity not found
            }

            if ((int)$user['is_active'] !== 1) {
                return false; // Account suspended
            }

            // Verify securely hashed password
            if (password_verify($password, $user['password_hash'])) {
                // Log the login timestamp in the database via inherited Base tracker
                $this->recordLogin((int)$user['id']);

                // Build a safe identity session array
                unset($user['password_hash']);
                return $user;
            }

            return false;
        }

        /**
         * Registers a brand new account record within the identity table.
         * * @param string $username
         * @param string $email
         * @param string $password
         * @param string $role Defaults to 'user'
         * @return bool
         */
        public function register($username = null, $email = null, $password = null, $role = 'user') {
            if (empty($username) || empty($email) || empty($password)) {
                return false;
            }

            // Verify availability before writing to avoid triggering unique constraints violations
            if ($this->findIdentityByField('username', $username) || $this->findIdentityByField('email', $email)) {
                return false; 
            }

            $now = time();
            $userData = [
                'username'      => trim($username),
                'email'         => trim($email),
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'role'          => $role,
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now
            ];

            try {
                return $this->insertRow($this->dbTable, $userData);
            } catch (\Exception $e) {
                error_log("Account Registration Failure: " . $e->getMessage());
                return false;
            }
        }

        /**
         * Cleanly terminates active session tokens or system tracking states.
         * * @return bool
         */
        public function logout() {
            if (session_status() === PHP_SESSION_ACTIVE) {
                // Unset identity trackers and destroy current session frame
                $_SESSION = [];
                session_destroy();
                return true;
            }
            return false;
        }
    }