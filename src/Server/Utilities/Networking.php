<?php

    namespace App\Server\Utilities;

    class Networking {

        public function getCode($host = null) {
            if (!$host === null) {
                $ch = curl_init($host);
                
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);           
                curl_setopt($ch, CURLOPT_NOBODY, true);      

                curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                return $code ?? null;
            }
            
            return null;
        }

    }