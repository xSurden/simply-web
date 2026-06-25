<?php

    namespace App\Blueprint\Collection\Identity;

    use App\Blueprint\Datastore\SQLitePointer;

    class Base extends SQLitePointer {

        /*
        This class is the default script that will be included in every Identity class.
        Mainly contains the paths, schemas, and shared utilities required for user authentication.
        */

        // Target database structures
        protected $dbTable = "identity_users";
        
        // Core User Identity Schema
        protected $dbSchema = "
            CREATE TABLE IF NOT EXISTS identity_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                is_active INTEGER DEFAULT 1,
                last_login INTEGER NULL,
                created_at INTEGER NOT NULL,
                updated_at INTEGER NOT NULL
            )
        ";

        /**
         * Initializes the database tracking layer and registers schemas down to the SQLitePointer.
         */
        public function __construct() {
            // Automatically cascade the layout schema up to the parent database engine initializer
            parent::__construct([$this->dbSchema]);
        }

        /**
         * Locate an active identity signature profile by its unique account field identifier.
         * * @param string $field The column lookup criterion ('id', 'username', or 'email')
         * @param mixed $value The exact variable constraint value matching the account profile
         * @return array|null Returns raw profile array map or null if non-existent
         */
        protected function findIdentityByField(string $field, $value): ?array {
            // Enforce safe internal column selections
            if (!in_array($field, ['id', 'username', 'email'])) {
                return null;
            }

            try {
                return $this->getRow($this->dbTable, $field, $value);
            } catch (\Exception $e) {
                error_log("Identity Search Error [{$field}]: " . $e->getMessage());
                return null;
            }
        }

        /**
         * Refresh the tracking epoch log timestamp whenever an account identity performs a system action.
         * * @param int $userId The primary sequence account tracking index key
         * @return bool Returns status confirmation
         */
        public function recordLogin(int $userId): bool {
            try {
                return $this->updateRow($this->dbTable, 'id', $userId, [
                    'last_login' => time(),
                    'updated_at' => time()
                ]);
            } catch (\Exception $e) {
                error_log("Failed to record identity access login: " . $e->getMessage());
                return false;
            }
        }
    }