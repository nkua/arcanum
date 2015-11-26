<?php


set_include_path(get_include_path() . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . './include/' . PATH_SEPARATOR . '../lib/phpseclib/');
require_once('lib/Zend/Loader/Autoloader.php');
require_once('Arcanum/ExceptionHandler.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Arcanum_');
require_once('Arcanum/Session.php');
require_once('misc.php');





/**
 * Description of Backdoor
 *
 * @author spiros
 */
class Backdoor {


    public function get_url($uid) {

	global $config;

        $tokenstore = new Arcanum_Token_Email;
        $token = $tokenstore->generate_token();
        $tokenstore->set_token($token, $uid);
        $url = $config->website_home . "changepassword.php?token=" . $token;
        
        $url = "http://localhost/myarcanum/changepassword.php?token=".$token."&service=idm";
       // Arcanum_Session::destroy();
        return $url;
    }

}
