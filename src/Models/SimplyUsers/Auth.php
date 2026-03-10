<?php

    namespace SW\Source\Model\SimplyUsers;

    class Auth {

        /*
            Defining the variables for the class
        */
        private $table_users = "users";
        private $table_pwd_resets = "password_resets";

        /*
            Functions to manage users, including creating users, fetching user data, etc.
        */

        public function Auth(){
            $result = $this->GetSession();
        }

        public function Login(){}

        public function Logout(){}

        public function Register(){}


        private function GetSession(){
            if (!isset($_COOKIE['session_token'])) {
                return null;
            }

            $token = $_COOKIE['session_token'];

            $stmt = $this->pdo->prepare("
                SELECT u.*
                FROM users u
                JOIN sessions s ON u.id = s.user_id
                WHERE s.token = :token
                AND s.expires_at > NOW()
                LIMIT 1
            ");

            $stmt->execute([':token' => $token]);
            return $stmt->fetch();
        }
    }

