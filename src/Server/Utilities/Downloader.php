<?php

    namespace App\Server\Utilities;

    class Downloader {

        private $Repo_URL;

        private $Networking;

        public function __construct() {
            $Env = new \App\Server\Controller\Environment();
            $this->Networking = new \App\Server\Utilities\Networking();


            $this->Repo_URL = $Env->get("REPO_URL");
            if (str_ends_with($this->Repo_URL, "/")) {
                $this->Repo_URL = substr($this->Repo_URL, 0, -1);
            }

            $status = $this->Networking->getCode($this->Repo_URL);
            $allowedCodes = [0, 200, 403, null, ""];
        
            if (!in_array($status, $allowedCodes, true)) {
                throw new \Exception("Unable to connect to the repository base: " . $this->Repo_URL . " (Code: " . $status . ")");
            }
        }

        public function buildUrl($slug = null) {
            if ($slug === null) {
                throw new \Exception("Slugs must not be null");
            }

            if (!str_starts_with($slug, "/")) {
                $slug = "/" . $slug;
            }

            return $this->Repo_URL . $slug;
        }

        public function download($path = null, $url = null) {
            if ($path === null || $url === null) {
                throw new \Exception("Download parameters cannot be null");
            }

            $code = $this->Networking->getCode($url);

            if ($code !== 200 && $code !== 403 && $code !== 0) {
                throw new \Exception("Remote file unreachable: " . $url . " (Code: " . $code . ")");
            }

            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            try {
                $opts = [
                    "http" => [
                        "method" => "GET",
                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) PHP/Downloader\r\n"
                    ],
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ]
                ];
                $context = stream_context_create($opts);
                $data = @file_get_contents($url, false, $context);
                
                if ($data === false) {
                    throw new \Exception("Failed to read remote data from " . $url);
                }

                if (file_put_contents($path, $data) === false) {
                    throw new \Exception("Failed to write to local path: " . $path);
                }

                if (!file_exists($path)) {
                    throw new \Exception("Verification File not found at " . $path);
                }

                return true;

            } catch (\Throwable $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }