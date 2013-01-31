<?php
/**
 * CLI tools bootstrap
 *
 * @package arcanum
 * @subpackage cli
 * @version $Id: cli_common.inc.php 5824 2012-10-04 13:26:35Z avel $
 */

// Bail out if not running from CLI
if(isset($_SERVER['REMOTE_ADDR'])) exit(1);

set_include_path( get_include_path() . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . './include/' . PATH_SEPARATOR . './lib/phpseclib/');

require_once('lib/Zend/Loader/Autoloader.php');
require_once('include/Arcanum/ExceptionHandler.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Arcanum_');

global $config;
$config = new Zend_Config(require('config/config.php'), true);

include_once('include/Template.class.php');
include_once('include/misc.php');

date_default_timezone_set($config->timezone);

// Export some stuff from config object to global variables (for "legacy" code)
$pw_check_params = array(
    'PW_CHECK_LEVENSHTEIN' => $config->password_strength_policy->PW_CHECK_LEVENSHTEIN,
    'PW_CHECK_MIN_LEN' => $config->password_strength_policy->PW_CHECK_MIN_LEN,
    'PW_CHECK_MIN_UNIQ' => $config->password_strength_policy->PW_CHECK_MIN_UNIQ,
    'PW_CHECK_MIN_LCS' => $config->password_strength_policy->PW_CHECK_MIN_LCS
);

$logger = new Zend_Log();
$writerSyslog = new Zend_Log_Writer_Syslog(array('application' => 'arcanum_cli'));
$writerConsole = new Zend_Log_Writer_Stream('php://output');
Zend_Registry::set('logger', $logger);


