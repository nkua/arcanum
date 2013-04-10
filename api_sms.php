<?php
/**
 * API entrypoint.
 *
 * @package arcanum
 * @version $Id: api_sms.php 5823 2012-10-02 15:11:31Z avel $
 */

$initLocation = 'api';
require_once('include/init.php');

if(!in_array($_SERVER['REMOTE_ADDR'], $config->smsgw->ip_receive->toArray() )) {
    die("This was not a request by an sms gateway.\n");
}

ini_set('html_errors', 'Off');
ini_set('error_prepend_string', '');
ini_set('error_append_string', '');

$classname = 'Arcanum_SMS_Receiver_'.ucfirst($config->smsgw->receiver);
$receiver = new $classname();

try {
	$receiver->read();
} catch(Exception $e) {
	doReply(
		"SMS Gateway Error - no phone or smsc found in incoming request",
		''
	);
	exit;
}

$text = $receiver->getText();
$mobile = $receiver->getMobile();
$smsc = $receiver->getSmsc();
$log_id = $receiver->getLogId();


// ==================================================================
//
// Main Routine that decides what to reply with, if at all
//
// ------------------------------------------------------------------

if(empty($text)) {
    doReply(
    	sprintf("Empty text sent by %s, SMSC %s.",$mobile, $smsc),
    	''
	);
}

if(isset($smsc_deny) && !(array_search($smsc,$smsc_deny)===false)) {
	doReply(
		sprintf("Request from %s via SMSC %s ignored. (SMSC blacklisted)",$mobile, $smsc),
		''
	);
}

if(isset($smsc_allow) && (array_search($smsc,$smsc_allow)===false)) {
	doReply(
		sprintf("Request from %s via SMSC %s ignored. (SMSC Not allowed)",$mobile, $smsc),
		''
	);
}

$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();

if (!$ldap) {
	doReply(
		sprintf("LDAP Connect failed. Request from %s via SMSC %s.",$mobile, $smsc),
		_("Could not accomodate your request due to a technical issue. Please try again later.")
	);
}

$filter = sprintf($config->ldap->filter->user_receivesms, $mobile);
$result = @ldap_search($ldap, $config->ldap->basedn, $filter, array('uid'),0,9);

if ($result === false) {
	doReply(
		sprintf("LDAP Search failed. Request for %s via SMSC %s.",$mobile, $smsc),
		_("Could not accomodate your request due to a technical issue. Please try again later.")
	);
}

$entries = ldap_get_entries($ldap, $result);
if($entries['count']==0) {
    // don't really reply anything to the user; they shouldn't have sent the sms in the first place
    // without having started a session first!
    // Alternatively we _could_ reply:
    // sprintf( _("No account found for your mobile. Register your mobile at %s"), substr($config->website_home, 7) );
	// string below is solely for gettext

    $dummy = sprintf( _("No account found for your mobile. Register your mobile at %s"), substr($config->website_home, 7) );
	
	doReply(
        sprintf("No UIDs found for %s. Request via SMSC %s denied.",$mobile, $smsc),
        ''
	);
} 

$username = '';

if($entries['count']==1) {
	$username = $entries[0]['uid'][0];
} else {
	$uids=array();
	for ($i=0; $i < $entries["count"]; $i++) {
		$uids[] = $entries[$i]['uid'][0];
	}
	sort($uids);
	$numuids = count($uids);
	if(preg_match('/^[^ 0-9]+ *([1-9])$/', $text, $matches)==1) {
		$choice = intval($matches[1]);
		if($choice > 0 && $choice <= $numuids) {
			$username = $uids[$choice-1];
		}
	}

	if($username == "") {
        $replmsgpart = '';
        // FIXME XXX
        foreach($uids as $k => $v) {
            if($k>0) $replmsgpart .= ",\n";
            $replmsgpart .= sprintf( _("%s for %s"),  strtoupper($config->smsgw->prefix) . ($k+1),  $v);
        }

		doReply(
			sprintf("Multiple UIDs for %s via SMSC %s. List: %s", $mobile,  $smsc, join(",",$uids) ),
	        sprintf( _("You have %s accounts; send %s"), count($uids), $replmsgpart)
		);
	}
}

$envStore = new Arcanum_EnvironmentStore();

$initiated_reset_pw = $envStore->get('initiated_reset_pw');
if($initiated_reset_pw  === false || !isset($initiated_reset_pw[$username]) ) {
    // FAIL! no session. I won't even bother replying to the SMS.
	/*
	doReply(
		sprintf("User %s sent SMS from %s (SMSC: %s) but hadn't started session with the browser", $username, $mobile, $smsc),
		''
	);
	// */
}

$confirmed_reset_pw = $envStore->get('confirmed_reset_pw');
if($confirmed_reset_pw  === false) {
    $confirmed_reset_pw = array();
}
$confirmed_reset_pw[$username] = true;
$envStore->set('confirmed_reset_pw', $confirmed_reset_pw);

$tok = new Arcanum_Token_Sms;
$token = $tok->generate_token();
$tok->set_token($token, $username);

doReply(
	sprintf("Generated password reset token %s for user %s. Request from %s via SMSC %s.", $token, $username, $mobile, $smsc),
	sprintf( _("Please use the number %s"), $token)
);

// Upon error: Not needed at the moment.
//doReply(
//	sprintf("Cannot set token %s for user %s. Request from %s via SMSC %s denied.", $token, $username, $mobile, $smsc)
//  sprintf( _("Failed to change the password of your account %s. Please try again later."), $username);
// );
$dummy = _("Failed to change the password of your account %s. Please try again later.");


// ==================================================================
//
// Function Definitions below
//
// ------------------------------------------------------------------

function doReply($log_message, $reply_message) {
	global $config, $mobile, $username, $log_id;

    // print "SMS from $mobile, resolved to user $username.\nLog message: ".$log_message."\nReply Message: ".$reply_message."\n\n";
	syslog(LOG_DEBUG, "SMS from $mobile, resolved to user $username. Log message: ".$log_message." Reply Message: ".str_replace('\n', '', $reply_message). " Log ID:".$log_id);

    if(isset($username)) {
    	$recipient = $username;
    } else {
    	$recipient = $mobile;
    }
	$replysms = Arcanum_SMS_Sender::Factory($username, $config->smsgw);
    if($log_id) $replysms->setLogId($log_id);
	$replysms->send($reply_message);

	// Logging

	
}