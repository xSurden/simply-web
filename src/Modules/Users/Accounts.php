<?php

    namespace App\Modules\Users;

    class Accounts {

        private $Migrations;

        private $local_tbn = "users";
        private $local_tba = [
            "id"            => "UUID PRIMARY KEY DEFAULT gen_random_uuid()",
            "email"         => "VARCHAR(255) UNIQUE NOT NULL",
            "password"      => "TEXT NOT NULL",
            "display_name"  => "VARCHAR(100) NULL",
            "role"          => "VARCHAR(20) DEFAULT 'user'",
            "is_verified"   => "BOOLEAN DEFAULT FALSE",
            "last_login"    => "TIMESTAMPTZ NULL",
            "created_at"    => "TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP",
            "updated_at"    => "TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP"
        ];

        private $sessions_tbn = "user_sessions";
        private $sessions_tba = [
            "id"            => "UUID PRIMARY KEY DEFAULT gen_random_uuid()",
            "user_id"       => "UUID NOT NULL",
            "token"         => "VARCHAR(255) UNIQUE NOT NULL",
            "ip_address"    => "VARCHAR(45) NULL",
            "user_agent"    => "TEXT NULL",
            "expires_at"    => "TIMESTAMPTZ NOT NULL",
            "created_at"    => "TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP"
        ];

        private $reset_tbn = "password_resets";
        private $reset_tba = [
            "id"            => "SERIAL PRIMARY KEY",
            "user_id"       => "UUID NOT NULL",
            "token_hash"    => "TEXT NOT NULL",
            "expires_at"    => "TIMESTAMPTZ NOT NULL",
            "is_used"       => "BOOLEAN DEFAULT FALSE",
            "created_at"    => "TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP"
        ];

        public function __construct()
        {
            $this->Migrations = new \App\Modules\Database\Migrations();
            $this->Migrations->migrate($this->local_tbn, $this->local_tba);
            $this->Migrations->migrate($this->sessions_tbn, $this->sessions_tba);
            $this->Migrations->migrate($this->reset_tbn, $this->reset_tba);
        }
    }