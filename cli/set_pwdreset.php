<?php
/**
 * CLI program: Decode CTP
 *
 * @package arcanum
 * @version $Id: set_pwdreset.php 5633 2012-05-09 11:15:02Z avel $
 */

require_once('cli/cli_common.inc.php');

if($argc != 2) {
    die('No uid provided.');
}
$uid = $argv[1];

require_once('include/Arcanum_Ldap_Password.php');

$arcanumLdap = new Arcanum_Ldap_Password();
$ldap = $arcanumLdap->connect();

$dn = $arcanumLdap->getUserDn($uid);

$newinfo = array('pwdReset' => 'TRUE');

if(ldap_modify($ldap,$dn,$newinfo) === true) {
    print "OK.\n";
}

