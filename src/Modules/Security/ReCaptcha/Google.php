<?php

    namespace App\Modules\Security\ReCaptcha;

    class Google {

        private $ConfigRepo = ABSPATH . "/server/data/modules/google_recaptcha/config.php";
        private $Credentials_v2;
        private $Credentials_v3;
        private $Threshold;

        public function __construct() {
            if (!file_exists($this->ConfigRepo)) {
                throw new \Exception("Configuration file for Google ReCaptcha Server Module was not found.");
            }

            $ConfigData = require $this->ConfigRepo;

            if (!is_array($ConfigData)) {
                throw new \Exception("Invalid configuration format.");
            }

            $this->Credentials_v2 = $ConfigData["site_v2"] ?? [];
            $this->Credentials_v3 = $ConfigData["site_v3"] ?? [];
            $this->Threshold = $ConfigData["site_v3"]["threshold"] ?? 0.5;
        }

        public function loadv2() {
            $siteKey = $this->Credentials_v2["site_key"] ?? null;
            if (!$siteKey) return "";

            $html = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
            $html .= '<div class="g-recaptcha" data-sitekey="' . htmlspecialchars($siteKey) . '"></div>';

            return $html;
        }

        public function loadv3(string $action) {
            $siteKey = $this->Credentials_v3["site_key"] ?? null;
            if (!$siteKey) return "";

            $html = '<script src="https://www.google.com/recaptcha/api.js?render=' . htmlspecialchars($siteKey) . '"></script>';
            $html .= '<script>
                grecaptcha.ready(function() {
                    grecaptcha.execute("' . htmlspecialchars($siteKey) . '", {action: "' . $action . '"}).then(function(token) {
                        var recaptchaResponse = document.getElementById("g-recaptcha-response-v3");
                        if(recaptchaResponse) { recaptchaResponse.value = token; }
                    });
                });
            </script>';
            $html .= '<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-v3">';

            return $html;
        }

        public function verify_v2($response) {
            return $this->performVerify($this->Credentials_v2["secret_key"] ?? null, $response);
        }

        public function verify_v3($response) {
            $secretKey = $this->Credentials_v3["secret_key"] ?? null;
            $result = $this->performVerify($secretKey, $response);

            if ($result && isset($result->success) && $result->success && isset($result->score) && $result->score >= $this->Threshold) {
                return true;
            }

            return false;
        }

        private function performVerify($secretKey, $response) {
            if (!$secretKey || empty($response)) {
                return false;
            }

            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = [
                'secret'   => $secretKey,
                'response' => $response,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            ];

            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                ]
            ];

            $context = stream_context_create($options);
            $verify = @file_get_contents($url, false, $context);
            
            return json_decode($verify);
        }
    }