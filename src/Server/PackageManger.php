<?php

    namespace SW\Source\Server;

    class PackageManger {

        private $repoBase;

        public function __construct()
        {
            $this->repoBase = \SW\Source\Server\Engine\ConfigEngine::GetValue("package_repository");

            if ($this->repoBase) {
                $this->repoBase = rtrim($this->repoBase, '/') . '/';
            } else {
                throw new \Exception("Package repository URL is not configured. Please set 'package_repository' in your settings.");
            }
        }

        public function InstallPackage($target) {
            try {
                echo "Searching for package: {$target}...\n";

                $props = $this->Fetch($target);
                
                if (!$props) {
                    return false;
                }

                $installPath = ABSPATH . ($props['install_to'] ?? '/resources/packages');
                
                if (!is_dir($installPath)) {
                    if (!mkdir($installPath, 0777, true)) {
                        throw new \Exception("Could not create directory: $installPath");
                    }
                }

                echo "Installing to: {$installPath}...\n";

                $packageUrl = $this->repoBase . $target . "/data.zip";
                $tempZip = ABSPATH . "/resources/temp/{$target}.zip";

                if ($this->DownloadFile($packageUrl, $tempZip)) {
                    return $this->Unpack($tempZip, $installPath);
                }

                return false;

            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
                return false;
            }
        }

        public function Fetch($target) {
            $url = $this->repoBase . $target . "/properties.json";
            
            $host = parse_url($this->repoBase, PHP_URL_HOST);
            if (!$this->isHostUp($host)) {
                echo "Error: Repository host '{$host}' is unreachable. Check your internet or DNS.\n";
                return null;
            }

            $response = @file_get_contents($url);

            if ($response === false) {
                echo "Error: Could not find 'properties.json' for package '{$target}'.\n";
                return null;
            }

            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "Error: 'properties.json' is corrupted or invalid JSON.\n";
                return null;
            }

            return $data;
        }

        private function isHostUp($host) {
            $ip = gethostbyname($host);
            return ($ip !== $host); 
        }

        private function DownloadFile($url, $dest) {
            if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0777, true);
            
            echo "Downloading data...\n";
            $fp = @fopen($url, 'r');
            if (!$fp) {
                echo "Error: Could not download the package data (data.zip).\n";
                return false;
            }
            
            return file_put_contents($dest, $fp);
        }

        private function Unpack($zipPath, $dest) {
            $zip = new \ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($dest);
                $zip->close();
                unlink($zipPath); 
                echo "Package installed successfully!\n";
                return true;
            }
            echo "Error: Failed to unpack package.\n";
            return false;
        }

        public function DeletePackage($target) {
        }

        public function UpdatePackage($target) {
            $this->DeletePackage($target);
            $this->InstallPackage($target);
        }
    }