<?php

    namespace App\Modules\Email;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

    class Mailer {

        private $Environment;

        private $host;
        private $port;
        private $encrypted;
        private $authenticate;
        private $user;
        private $password;

        public function __construct()
        {

            // Check if the env is set - if not, initialise a new instance
            if (!isset($Environment)) {
                $this->Environment = new \App\Server\Controller\Environment();
            }
        }

        private function prepareSMTP() {

            $failed = false;
            if ($this->Environment->get("MAIL_HOST")) {
                $this->host = $this->Environment->get("MAIL_HOST");
            } else {
                $failed = true;
            }
            if ($this->Environment->get("MAIL_PORT")) {
                $this->port = $this->Environment->get("MAIL_PORT");
            } else {
                $failed = true;
            }
            if ($this->Environment->get("MAIL_ENCRYPTED")) {
                $this->encrypted = $this->Environment->get("MAIL_ENCRYPTED");
            } else {
                $failed = true;
            }
            if ($this->Environment->get("MAIL_USER")) {
                $this->user = $this->Environment->get("MAIL_USER");
            } else {
                $failed = true;
            }
            if ($this->Environment->get("MAIL_AUTHENTICATE")) {
                $this->authenticate = $this->Environment->get("MAIL_AUTHENTICATE");
            } else {
                $failed = true;
            }
            if ($this->Environment->get("MAIL_PASSWORD")) {
                $this->password = $this->Environment->get("MAIL_PASSWORD");
            } else {
                $failed = true;
            }

            return $failed;
        }

        public function send($recipient, $body=[]) {

            if ($this->Environment->get("MAIL_ENABLED") === "false" || !$this->Environment->get("MAIL_ENABLED")) {
                throw new \Exception("SMTP is disabled via the environment file");
                return;
            }

            if (!$this->prepareSMTP()) {
                die("Error: Unable to prepare SMTP service");
            }

            if (empty($recipient) || empty($body)) {
                throw new \Exception("Recipient and body are required");
            }

            $PHPMailer = new PHPMailer(true);

            $PHPMailer->isSMTP();
            $PHPMailer->Host       = $this->host;
            $PHPMailer->SMTPAuth   = $this->authenticate;
            $PHPMailer->Username   = $this->user;
            $PHPMailer->Password   = $this->password;
            $PHPMailer->SMTPSecure = $this->encrypted;
            $PHPMailer->Port       = $this->port;

            $PHPMailer->setFrom($this->user, $this->Environment->get("APP_NAME"));
            $PHPMailer->addAddress($recipient);

            $PHPMailer->isHTML(true);
            $PHPMailer->Subject = $body['subject'] ?? "Message from " . $this->Environment->get("APP_NAME");
            $PHPMailer->Body    = $this->Build($body);
            $PHPMailer->AltBody = strip_tags($PHPMailer->Body);

            return $PHPMailer->send();
        }


        public function build($body = []) {
            if (!empty($body)) {
                return $body["body"] ?? "Body failed to load or is not found.";
            }
        }

    }
