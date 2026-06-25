<?php

    namespace App\Blueprint\Security\ReCaptcha;

    use App\Blueprint\Datastore\LocalStorage;
    
    class Google extends LocalStorage {

        private $Credentials_v2;
        private $Credentials_v2_Secret;
        private $Credentials_v3;
        private $Credentials_v3_Secret;
        private $Threshold;

        public function __construct() {
            
            $this->Credentials_v2 = $this->getValue("app.security.recaptcha.google.v2.site_key") ?? null;
            $this->Credentials_v2_Secret = $this->getValue("app.security.recaptcha.google.v2.secret_key") ?? null;
            
            $this->Credentials_v3 = $this->getValue("app.security.recaptcha.google.v3.site_key") ?? null;
            $this->Credentials_v3_Secret = $this->getValue("app.security.recaptcha.google.v2.secret_key") ?? null;
            $this->Threshold = $this->getValue("app.security.recaptcha.google.v3.threshold") ?? 0.5;

        }

        public function loadV2() {
            if (!$this->Credentials_v2) return "";

            $html = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
            $html .= '<div class="g-recaptcha" data-sitekey="' . htmlspecialchars($this->Credentials_v2) . '"></div>';

            return $html;
        }

        public function loadV3(string $action) {
            if (!$this->Credentials_v3) return "";

            $html = '<script src="https://www.google.com/recaptcha/api.js?render=' . htmlspecialchars($this->Credentials_v3) . '"></script>';
            $html .= '<script>
                grecaptcha.ready(function() {
                    grecaptcha.execute("' . htmlspecialchars($this->Credentials_v3) . '", {action: "' . $action . '"}).then(function(token) {
                        var recaptchaResponse = document.getElementById("g-recaptcha-response-v3");
                        if(recaptchaResponse) { recaptchaResponse.value = token; }
                    });
                });
            </script>';
            $html .= '<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-v3">';

            return $html;
        }

        public function verifyV2($response) {
            return $this->performVerify($this->Credentials_v2_Secret, $response);
        }

        public function verifyV3($response) {
            $result = $this->performVerify($this->Credentials_v3_Secret, $response);

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