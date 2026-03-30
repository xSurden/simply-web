<?php

    namespace SW\Source\Server;

    class PackageManger {

        private $repoBase;
        private $Pointer;

        public function __construct()
        {
            // Depreciated -> moved to database check
            // $this->repoBase = \SW\Source\Server\Engine\ConfigEngine::GetValue("package_repository");

            // Init the pointer class
            $this->Pointer = new \SW\Source\Server\Database\Pointer();
            $this->repoBase = $this->Pointer->FetchField("server_configs", "config_key", "package_repository_url");

            if ($this->repoBase) {
                $this->repoBase = rtrim($this->repoBase["config_value"], '/') . '/';
            } else {
                throw new \Exception("Package repository URL is not configured. Please set one within your database (server_configs table)");
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
            $props = $this->Fetch($target);
            $path = ABSPATH . ($props['install_to'] ?? "/resources/packages/{$target}");

            if (!is_dir($path)) {
                echo "Package '{$target}' not found at {$path}.\n";
                return false;
            }

            echo "Removing package: {$target}...\n";
            return $this->recursiveDelete($path);
        }

        private function recursiveDelete($dir) {
            if (!file_exists($dir)) return true;
            if (!is_dir($dir)) return unlink($dir);

            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') continue;
                if (!$this->recursiveDelete($dir . DIRECTORY_SEPARATOR . $item)) return false;
            }

            return rmdir($dir);
        }

        public function UpdatePackage($target) {
            $this->DeletePackage($target);
            $this->InstallPackage($target);
        }

        public function UpdateSystem() {
            $githubUser = "xSurden";
            $githubRepo = "simply-web"; 
            $branch = "main";
            
            $zipUrl = "https://github.com/{$githubUser}/{$githubRepo}/archive/refs/heads/{$branch}.zip";
            $tempDir = ABSPATH . "/resources/temp/update_stage";
            $zipFile = ABSPATH . "/resources/temp/system_update.zip";

            echo "--- Initiating GitHub Core Update ---\n";

            try {
                if (!$this->DownloadFile($zipUrl, $zipFile)) {
                    throw new \Exception("Failed to download system update from GitHub.");
                }

                $zip = new \ZipArchive;
                if ($zip->open($zipFile) === TRUE) {
                    $zip->extractTo($tempDir);
                    $zip->close();
                    unlink($zipFile);
                } else {
                    throw new \Exception("Failed to open update ZIP.");
                }

                $extractedFolder = $tempDir . "/{$githubRepo}-{$branch}";

                $map = [
                    '/sw.php'       => '/sw.php',
                    '/src/Server'   => '/src/Server',
                    '/server'       => '/server'
                ];

                foreach ($map as $repoPath => $destPath) {
                    $source = $extractedFolder . $repoPath;
                    $destination = ABSPATH . $destPath;

                    if (file_exists($source)) {
                        echo "Updating: {$destPath}...\n";
                        if (is_dir($source)) {
                            $this->copyRecursive($source, $destination);
                        } else {
                            copy($source, $destination);
                        }
                    }
                }

                $this->recursiveDelete($tempDir);
                
                echo "Successfully updated core system files from GitHub.\n";
                return true;

            } catch (\Exception $e) {
                echo "Update Error: " . $e->getMessage() . "\n";
                return false;
            }
        }

        private function copyRecursive($src, $dst) {
            if (!is_dir($dst)) mkdir($dst, 0777, true);
            $dir = opendir($src);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        $this->copyRecursive($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }
    }