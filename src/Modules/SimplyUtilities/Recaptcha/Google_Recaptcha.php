<?php

    namespace SW\Source\Modules\SimplyUtilities\Recaptcha;

    class Google_Recaptcha {

        /*
        |   Recaptcha module for SimplyWeb framework
        |   Currently includes: Google's reCaptcha
        */

        // Variables
        private $Pointer;

        public function __construct()
        {
            $this->Pointer = new \SW\Source\Server\Database\Pointer();

            // Set key and secret to "not_available" if does not exist in server configs table
            $result = $this->Pointer->FetchField("server_configs", "config_key", "simplyutilities_google_recaptcha_site_key");
            if ($result === null) {
                $data = [
                    "config_key" => "simplyutilities_google_recaptcha_site_key",
                    "config_value" => "not_available",
                    "description" => "Google Site Key"
                ];

                $this->Pointer->Insert("server_configs", $data);
            }

            // For secret

            $result = $this->Pointer->FetchField("server_configs", "config_key", "simplyutilities_google_recaptcha_secret_key");
            if ($result === null) {
                $data = [
                    "config_key" => "simplyutilities_google_recaptcha_secret_key",
                    "config_value" => "not_available",
                    "description" => "Google Site Secret"
                ];

                $this->Pointer->Insert("server_configs", $data);
            }
        }

        /*
        |   Google's reCaptcha section
        |   Load method: Outputs the JS script and the div element
        */
        public function Loadv2()
        {
            $result = $this->Pointer->FetchField("server_configs", "config_key", "simplyutilities_google_recaptcha_site_key");
            $siteKey = $result['config_value'] ?? null;

            if (!$siteKey) {
                return "";
            }

            $html = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
            $html .= '<div class="g-recaptcha" data-sitekey="' . htmlspecialchars($siteKey) . '"></div>';

            return $html;
        }

        /*
        |   Verify method: Checks the response with Google's servers
        */
        public function Verify($response)
        {
            $result = $this->Pointer->FetchField("server_configs", "config_key", "simplyutilities_google_recaptcha_secret_key");
            $secretKey = $result['config_value'] ?? null;

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

            $context  = stream_context_create($options);
            $verify = file_get_contents($url, false, $context);
            $captchaSuccess = json_decode($verify);

            return $captchaSuccess->success;
        }
    }