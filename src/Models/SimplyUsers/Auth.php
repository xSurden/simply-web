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


        private function GetSession(){}
    }

