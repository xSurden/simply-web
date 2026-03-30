<?php

    namespace SW\Source\Modules\SimplyAuth;

    class Authentication {

        /*
            Defining the variables for the class
        */
        private $Pointer;

        private $table_users = "users";
        private $table_pwd_resets = "password_resets";

        public function __construct()
        {
            $this->Pointer = new \SW\Source\Server\Database\Pointer();
        }

        /*
            Functions to manage users, including creating users, fetching user data, etc.
        */

        public static function Restrict(array $roles_allowed = []) {
            if (!empty($roles_allowed)) {
                // Continue if the roles are not empty.
            }
        }

        public function Auth(){
            /*
            |   Will be used to authenticate users before accessing certain views
            */
        }

        public function Login(){}

        public function Logout(){}

        public function Register(){}
    }

