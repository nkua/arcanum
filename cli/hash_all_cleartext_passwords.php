<?php
/**
 * CLI program: 
 *
 * @package arcanum
 * @version $Id$
 */

require_once('cli/cli_common.inc.php');

set_include_path( get_include_path() . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . './lib/phpseclib/');

include_once('include/misc.php');

$l = Zend_Registry::get('logger');
$l->addWriter($writerConsole);

$currenttime = time();

// Get users
$arcanumLdap = new Arcanum_LdapPassword();
$ldap = $arcanumLdap->connect();
$sr = ldap_search($ldap, $config->ldap->basedn, sprintf($config->ldap->filter->user, '*'), array('uid', 'userPassword'));

$to_modify = array();

$i=0;
for($entryID = ldap_first_entry($ldap,$sr);
    $entryID!=false;
    $entryID=ldap_next_entry($ldap,$entryID) ) {

    $entry = ldap_get_attributes($ldap,$entryID);

    $ldap_password = $entry['userPassword'][0];

    $hashed = false;
    if(strtoupper(substr($ldap_password, 0, 7)) == '{CRYPT}' ||
       strtoupper(substr($ldap_password, 0, 5)) == '{SHA}' ||
       strtoupper(substr($ldap_password, 0, 6)) == '{SSHA}') {
           $hashed = true;
    }

    if($hashed) {
        continue;
    }


    $to_modify[ldap_get_dn($ldap, $entryID)] = array('userPassword' => "{SSHA}".HashAlgorithm_SSHA::Generate($ldap_password));

    $i++;
}

if(empty($to_modify)) {
    $l->info('Nothing to do; exiting');
    exit;
}


foreach($to_modify as $dn => $ldapmod) {
    $l->info('Modifying '.$dn);
    if(ldap_modify($ldap, $dn, $ldapmod) === false) {
        $l->info('error while modifying '.$dn. ' - '.ldap_error($ldap));
    }
}

