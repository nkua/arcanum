<?php
/**
 * CLI program: Password Expiry Reminders dispatch
 *
 * Time cheat sheet
 * #      300   5 M                 #    604800   1 W
 * #     2700  45 M                 #   1814400   3 W
 * #     3600   1 H                 #   2419200   1 M
 * #    54000  15 H                 #  14515200   6 M
 * #    86400   1 D                 #  26611200  11 M
 *
 * @package arcanum
 * @version $Id: password_expiry_reminder.php 5900 2012-11-05 11:27:49Z avel $
 */

require_once('cli/cli_common.inc.php');

set_include_path( get_include_path() . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . './lib/phpseclib/');

include_once('include/misc.php');
include_once('Zend/Ldap/Attribute.php');
include_once('Zend/View.php');

try {
    $opts = new Zend_Console_Getopt(
      array(
        'quiet|q' => 'quiet operation',
        'debug|d=s' => 'e-mail address for debugging operation; all e-mail notifications will be sent to a custom address',
      )
    );
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}
$opt_quiet = $opts->getOption('q');
$opt_debug = $opts->getOption('d');

// Define notification messages and their methods

// How often does this cron run
$runs_every = 86400; // 1 day

$notifications = Arcanum_Notifications::get_configured_notifications();
$status = Arcanum_Notifications::get_current_status();

// Setup, Connect
arcanumSetupEmail();
$arcanumLdap = new Arcanum_LdapPassword();
$ldap = $arcanumLdap->connect();
$currenttime = time();

$l = Zend_Registry::get('logger');
if($opt_quiet === true) {
    $l->addWriter($writerSyslog);
} else {
    $l->addWriter($writerConsole);
}

if($opt_debug) {
    $l->info('Debug operation; all e-mail will be dispatched to ' . $opt_debug);
}

// Get policies
$sr = ldap_search($ldap, $config->ldap->basedn, '(objectclass=pwdpolicy)',
    array_merge(array('cn'), array_keys($arcanumLdap->policyAttributes)) );
$entries = ldap_get_entries($ldap, $sr);
if($entries['count'] == 0) die('No password policies');
$policy = $entries[0];
$pwdmaxage = $entries[0]['pwdmaxage'][0];
unset($entries);

// Get users
$sr = ldap_search($ldap, $config->ldap->basedn, sprintf($config->ldap->filter->user, '*'), array('uid', 'mail', '+'));
$entries = ldap_get_entries($ldap, $sr);

$l->info("Expiration script.");
$l->info("Current Time: ". time());
$l->info("Password Max. Age: ". $pwdmaxage);

for($i=0; $i<$entries['count']; $i++) {
    // Local variables to the loop:
    // uid, changedtime, age, expired, expires_in

    $uid = $entries[$i]['uid'][0];

    if(!isset($status[$uid])) { $status[$uid] = array(); }

    $changedtime = Arcanum_LdapPassword::getChangedTime($entries[$i]);

    if(isset($status[$uid]['changedtime'])) {
        if($status[$uid]['changedtime'] != $changedtime) {
            // user has changed their password since we last ran. Reset all notification history!
            unset($status[$uid]);
            $status[$uid] = array('changedtime' => $changedtime);
        }
    } else {
        $status[$uid]['changedtime'] = $changedtime;
    }
    
    $age = $currenttime - $changedtime;

    if($age > $pwdmaxage) {
        $expired = true;
        $expires_in = 0;
    } else {
        $expired = false;
        $expires_in = $pwdmaxage - $age;
    }

    // Make decisions based on:
    // 1) expired = true | false 
    // 2) expires_in

    //print "uid: ". $entries[$i]['uid'][0] . " - age: " . $age. ( $expired ? " EXPIRED"  : '  expires in '. $expires_in ) . "\n";

    $l->info("uid: ". $uid . " - age: " . $age. 
          ( $expired ? " EXPIRED"  : '  expires in '. $expires_in . ' (' . time_duration($expires_in, null, false) . ')' ));

    if($expired) {
        // in this place we could send some sort of notification for already expired accounts, in the future.

    } else {

        for($k=0; $k < sizeof($notifications); $k++) {
            // if($expires_in > $notification['seconds_to_expiry'] && $expires_in < ($notification['seconds_to_expiry'] + $runs_every))

            if(isset($status[$uid][$notifications[$k]['id']]) &&  $status[$uid][$notifications[$k]['id']] == 'SENT') {
                $l->debug('Already sent ' .$notifications[$k]['id']);
                continue;
            }

            if($k+1 == sizeof($notifications)) {
                $l->debug("test = $expires_in < " . $notifications[$k]['seconds_to_expiry']);
                $test = ($expires_in < $notifications[$k]['seconds_to_expiry']);
            } else {
                $l->debug("test = $expires_in < ".$notifications[$k]['seconds_to_expiry']. ") && ($expires_in > " . $notifications[$k+1]['seconds_to_expiry']. " )");
                $test = ($expires_in < $notifications[$k]['seconds_to_expiry']) && ($expires_in > $notifications[$k+1]['seconds_to_expiry']);
            }
            if($test) {
                $l->debug(" Triggered ". $notifications[$k]['id'] . " for ". $uid . " (expires in ".time_duration($expires_in, null, false).")");
                $mailaddress = Arcanum_Notifications::get_notification_address($entries[$i]);
                
                if($opt_debug) {
                    if($mailaddress) {
                        $l->info("Debug mode: Using $opt_debug instead of $mailaddress");
                    } else {
                        $l->info("No destination to send notification; however, dispatching to debug e-mail");
                    }
                    $mailaddress = $opt_debug;
                }
                
                if(!$mailaddress) {
                    $l->info("No destination to send notification");
                } else {
                    $l->info("Dispatching notification to $mailaddress");

                    $body = new Zend_View();
                    $body->setScriptPath('./templates/emails');
                    $body->uid = $uid;

                    $zmail = new Zend_Mail('UTF-8');
                    $zmail->setSubject($notifications[$k]['subject']);
                    $zmail->setBodyText($body->render($notifications[$k]['message'] . '.tpl.php'));

                    $zmail->addTo($mailaddress);
                    $zmail->send();

                    $status[$uid][$notifications[$k]['id']] = 'SENT';
                }
            } 
        }
        /*
        print time_duration($expires_in, null, false);
        print "\n";
        // */
    }
    //if($i > 30) break;
}

file_put_contents(Arcanum_Notifications::STATUS_FILE, json_encode($status));


ldap_close($ldap);

//print_r($status);

