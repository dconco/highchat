<?php 
    function encrypt($string) {
        $original_string = $string;
        
        // Store the cipher method
        $ciphering_value = "BF-CBC";
        $iv_length = openssl_cipher_iv_length($ciphering_value);
        $options = 0;
        $encryption_iv_value = random_bytes($iv_length);
        
        if (strlen($encryption_iv_value) > 7) {
            // Store the encryption key
            $encryption_key =  openssl_digest(php_uname(), 'MD5', TRUE);
            
            // Use openssl_encrypt() function 
            $encryption_value = openssl_encrypt($original_string, $ciphering_value, $encryption_key, $options, $encryption_iv_value);
            
            $encrypt = ["key" => $encryption_key, "value" => $encryption_value, "iv_value" => $encryption_iv_value];
            return $encrypt;
        }
    }