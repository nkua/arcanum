<?php
/**
 * CLI program: Password Age report
 *
 * @package arcanum
 * @version $Id: password_age_report.php 5824 2012-10-04 13:26:35Z avel $
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
$sr = ldap_search($ldap, $config->ldap->basedn, sprintf($config->ldap->filter->user, '*'), array('uid', '+'));
//$entries = ldap_get_entries($ldap, $sr);

// setup
$results = array(
    604800 => 0,    // 1W
    2419200 => 0,   // 1M
    14515200 => 0,  // 6M
);

$descs = array(
    604800 => '1 Week',    // 1W
    2419200 => '1 Month',   // 1M
    14515200 => '6 Months',  // 6M
);

for($k=1;$k<8;$k++) {
    $key = 31536000 * $k; 
    $results[$key] = 0;
    $descs[$key] = "$k Year" . ($k>1? "s" : '');
}
unset($key);
unset($k);
$keys = array_keys($results);
$final_key = $keys[sizeof($keys)-1];
// end setup


// for($i=0; $i<$entries['count']; $i++) 
// $entry = &$entries[$i];

$i=0;
for($entryID = ldap_first_entry($ldap,$sr);
    $entryID!=false;
    $entryID=ldap_next_entry($ldap,$entryID) ) {

    $entry = ldap_get_attributes($ldap,$entryID);

    $changedtime = Arcanum_LdapPassword::getChangedTime($entry);
    
    $age = $currenttime - $changedtime;

    // place $age in appropriate group
    foreach($results as $from => $count) {
        if($from == $final_key) {
            // check against final key
            if($age > $final_key) {
                $results[$from]++;
                break;
            }
        } else {
            // find next key
            foreach($keys as $o => $k) {
                if($k == $from) {
                    $to = $keys[$o+1];
                    break;
                }
            }
            //$l->debug("$from => $to");
            if($age > $from && $age < $to) {
                $results[$from]++;
                break;
            }
        }
    }

    //$l->debug($entry['uid'][0] . " age: $age - " . time_duration($age, null, false));

    //$l->info($entry['uid'][0] . " age: " . time_duration($age, null, false));
    $i++;
    //if($i > 30) break;
}

foreach($results as $key => $res) {
    $l->info( $descs[$key] . ": " . $res);
}
