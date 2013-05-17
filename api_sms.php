<?php
/**
 * API entrypoint.
 *
 * @package Arcanum
 * @version $Id: api_sms.php 5823 2012-10-02 15:11:31Z avel $
 */

$initLocation = 'api';
require_once 'include/init.php';

if (!in_array($_SERVER['REMOTE_ADDR'], $config->smsgw->ip_receive->toArray())) {
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

if (empty($text)) {
    doReply(
        sprintf("Empty text sent by %s, SMSC %s.", $mobile, $smsc),
        ''
    );
}

if (isset($smsc_deny) && !(array_search($smsc, $smsc_deny)===false)) {
    doReply(
        sprintf("Request from %s via SMSC %s ignored. (SMSC blacklisted)", $mobile, $smsc),
        ''
    );
}

if (isset($smsc_allow) && (array_search($smsc, $smsc_allow)===false)) {
    doReply(
        sprintf("Request from %s via SMSC %s ignored. (SMSC Not allowed)", $mobile, $smsc),
        ''
    );
}



$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();

if (!$ldap) {
    doReply(
        sprintf("LDAP Connect failed. Request from %s via SMSC %s.", $mobile, $smsc),
        _("Could not accomodate your request due to a technical issue. Please try again later.")
    );
}

$filter = sprintf($config->ldap->filter->user_receivesms, $mobile);
$result = @ldap_search($ldap, $config->ldap->basedn, $filter, array('uid'), 0, 9);

if ($result === false) {
    doReply(
        sprintf("LDAP Search failed. Request for %s via SMSC %s.", $mobile, $smsc),
        _("Could not accomodate your request due to a technical issue. Please try again later.")
    );
}

$entries = ldap_get_entries($ldap, $result);

if ($entries['count'] == 0) {
    doReply(
        sprintf("No UIDs found for %s. Request via SMSC %s denied.", $mobile, $smsc),
        ''
    );
} 

$usernames = array();

if ($entries['count'] == 1) {
    // Just one hit, easy.
    $usernames[] = $entries[0]['uid'][0];

} else {
    // This is the case where the same mobile number is in many accounts. We'll simply check to see
    // which username was declared when the user started the session. (The check will be done in the
    // next section)

    for ($i=0; $i < $entries["count"]; $i++) {
        $usernames[] = $entries[$i]['uid'][0];
    }
}


// ==================================================================
//
// Check if the user had started a reset password session from the browser
//
// ------------------------------------------------------------------

$confirmed = false;
$username = '';

$envStore = new Arcanum_EnvironmentStore();

$initiated_reset_pw = $envStore->get('initiated_reset_pw');

if($initiated_reset_pw !== false) {

    foreach($usernames as $u) {
        if(isset($initiated_reset_pw[$u])) {
            $username = $u;
            $confirmed = true;
            break;
        }
    }
}

if(!$confirmed) {
    // This means no browser session had been started. I won't even bother replying to the SMS.
    doReply(
        sprintf("User sent SMS from %s (SMSC: %s) but hadn't started session with the browser. (Last username checked: %s)", $mobile, $smsc, $u),
        ''
    );
}


// ==================================================================
//
// At this point the user is "authenticated". $username, $mobile are trusted.
// Set tokens accordingly.
//
// ------------------------------------------------------------------

$confirmed_reset_pw = $envStore->get('confirmed_reset_pw');
if ($confirmed_reset_pw  === false) {
    $confirmed_reset_pw = array();
}
$confirmed_reset_pw[$username] = true;
$envStore->set('confirmed_reset_pw', $confirmed_reset_pw);

$tok = new Arcanum_Token_Sms;
$token = $tok->generate_token();
$tok->set_token($token, $username);

doReply(
    sprintf("Generated password reset token %s for user %s. Request from %s via SMSC %s.", $token, $username, $mobile, $smsc),
    sprintf(_("Please use the number %s"), $token)
);

// Upon error: Not needed at the moment.
//doReply(
//    sprintf("Cannot set token %s for user %s. Request from %s via SMSC %s denied.", $token, $username, $mobile, $smsc)
//  sprintf( _("Failed to change the password of your account %s. Please try again later."), $username);
// );
$dummy = _("Failed to change the password of your account %s. Please try again later.");


// ==================================================================
//
// Function Definitions below
//
// ------------------------------------------------------------------

/**
 * Reply - actually send SMS.
 *
 * @param string $log_message Log message
 * @param string $reply_message Reply message to be sent
 * @return void Never returns, exits.
 */
function doReply($log_message, $reply_message)
{
    global $config, $mobile, $username, $log_id, $receiver;

    syslog(LOG_DEBUG, "arcanum: SMS from $mobile, ". 
        (!empty($username) ? 'resolved to user ' . $username : 'not resolved to any user') .
        ". Log message: ".$log_message." ".
        (!empty($reply_message) ? 'Reply Message: '.str_replace('\n', '', $reply_message) : 'No Reply Message. ') .
        " Log ID:".$log_id
    );

    if(!empty($reply_message)) {
        if(isset($config->smsgw->institution) && !empty($config->smsgw->institution)) {
            $recipient = $username;
        } else {
            $recipient = $mobile;
        }

        $replysms = Arcanum_SMS_Sender::Factory($recipient, $config->smsgw);
        if ($log_id) $replysms->setLogId($log_id);

        $replystatus = $replysms->send($reply_message);
    }

    // Replying with status
    if( in_array('Arcanum_SMS_Receiver_with_Status_Reply_Interface', class_implements($receiver))) {
        $receiver->status(300);
    }

    exit;
}