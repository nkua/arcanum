<?php
/**
 * CLI program: Decode CTP
 *
 * @package arcanum
 * @version $Id: dectp.php 5614 2012-04-04 09:43:52Z avel $
 */

require_once('cli/cli_common.inc.php');

set_include_path( get_include_path() . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . './lib/phpseclib/');

include_once('Crypt/TripleDES.php');

if($argc != 2) {
    die('No DES provided.');
}
$encoded = $argv[1];

$des = new Crypt_TripleDES(CRYPT_DES_MODE_CBC);
$des->setKey($config->ldap->ctpKey);

echo $des->decrypt(base64_decode($encoded));
echo "\n";

