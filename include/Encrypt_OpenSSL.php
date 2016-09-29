<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Enrypt_OpenSSL
 *
 * @author Spiros Trougakos <trougakos at uoa.gr>
 */
 
class Encrypt_OpenSSL implements HashAlgorithm {
  
    
    public static function Generate($cleartext) {
        global $config; 
        $keyname = $config->openssl_public_key;
        $fp=fopen($keyname,"r");
        $pub_key=fread($fp,8192);
        fclose($fp);
        openssl_get_publickey($pub_key);
        openssl_public_encrypt($cleartext,$crypttext, $pub_key );
        return(base64_encode($crypttext));

    }


}
