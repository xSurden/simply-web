<?php

    namespace App\Blueprint\Collection\Identity;

    class User extends Base {

        /*
        This class is part of the Identity collection.
        It is used for user-related data manipulation.

        This is not the authentication script; if you require login etc, use \App\Blueprint\Collection\Identity\Authenticate
        */

        /**
         * Fetch complete details for a specific user ID, stripped of the password hash.
         * * @param int|null $userId
         * @return array|null
         */
        public function getUserDetails($userId = null) {
            if ($userId === null) {
                return null;
            }

            try {
                $user = $this->getRow($this->dbTable, 'id', (int)$userId);
                
                if ($user) {
                    // Safeguard: Never expose password hashes down the application pipeline
                    unset($user['password_hash']);
                    return $user;
                }
            } catch (\Exception $e) {
                error_log("Error retrieving user details: " . $e->getMessage());
            }

            return null;
        }

        /**
         * Update account meta values or structural info for a user.
         * * @param int $userId
         * @param array $data Array of column => value pairings to update
         * @return bool
         */
        public function updateProfile($userId, array $data) {
            if (empty($userId) || empty($data)) {
                return false;
            }

            // Prevent unauthorized column injection or direct password updates through profile modifications
            unset($data['id'], $data['password_hash']);

            $data['updated_at'] = time();

            try {
                return $this->updateRow($this->dbTable, 'id', (int)$userId, $data);
            } catch (\Exception $e) {
                error_log("Error updating user profile: " . $e->getMessage());
                return false;
            }
        }

        /**
         * Deactivate or drop a user safely from the storage engine.
         * * @param int $userId
         * @return bool
         */
        public function deleteUser($userId) {
            if (empty($userId)) {
                return false;
            }

            try {
                return $this->deleteRow($this->dbTable, 'id', (int)$userId);
            } catch (\Exception $e) {
                error_log("Error deleting user: " . $e->getMessage());
                return false;
            }
        }
    }