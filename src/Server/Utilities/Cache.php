<?php

    namespace App\Server\Utilities;

    class Cache {

        private $Path = ABSPATH . "/server/storage/cache/";

        public function __construct()
        {
            if (!is_dir($this->Path)) {
                try {
                    if (!@mkdir($this->Path, 0777, true)) {
                        throw new \Exception("Failed to create cache folder at " . $this->Path);
                    }
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }
        }

        public function runCron() {

            $Env = new \App\Server\Controller\Environment();

            // Find the cache, if not exist or is null, default to 5 minutes
            $secondsToLive = $Env->get("CACHE_CLEANUP_TIME") ?? 300;

            $deletedCount = 0;
            $now = time();

            if (!is_dir($this->Path)) {
                return 0;
            }

            $items = scandir($this->Path, SCANDIR_SORT_NONE);

            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $itemPath = $this->Path . $item;
                $age = $now - filemtime($itemPath);

                if ($age >= $secondsToLive) {
                    if (is_file($itemPath)) {
                        if (unlink($itemPath)) $deletedCount++;
                    } elseif (is_dir($itemPath)) {
                        // rmìdir() only works if the folder is empty
                        if (@rmdir($itemPath)) $deletedCount++;
                    }
                }
            }

            return true;
        }

        public function saveCache($cache_file, $data) {
            $filePath = $this->Path . $cache_file;
            
            $content = serialize($data);
            
            return file_put_contents($filePath, $content) !== false;
        }

        public function loadCache($cache_file = null, $secondsToLive = null) {
            if ($cache_file === null) {
                throw new \Exception("Cache file is not specified");
            }

            $filePath = $this->Path . $cache_file;

            if (!file_exists($filePath)) {
                return null;
            }

            if ($secondsToLive !== null) {
                if ((time() - filemtime($filePath)) > $secondsToLive) {
                    unlink($filePath); 
                    return null;
                }
            }

            $content = file_get_contents($filePath);
            
            return unserialize($content);
        }
        
        public function clearCache($cache_file) {
            $filePath = $this->Path . $cache_file;
            if (file_exists($filePath)) {
                return unlink($filePath);
            }
            return false;
        }
    }