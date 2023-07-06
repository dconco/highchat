<?php 
    function decrypt($key, $string, $iv_value) {
        // Store the cipher method
        $ciphering_value = "BF-CBC";
        $options = 0;
        
        // Use openssl_encrypt() function 
        $value = openssl_decrypt($string, $ciphering_value, $key, $options, $iv_value);
        return $value;
    }