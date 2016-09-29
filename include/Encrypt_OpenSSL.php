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
 
class Encrypt_OpenSSL  {
  
    
    public static function Generate($cleartext,$pub_key) {
        openssl_get_publickey($pub_key);
        openssl_public_encrypt($cleartext,$crypttext, $pub_key );
        return(base64_encode($crypttext));

    }


}
