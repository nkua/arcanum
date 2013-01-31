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

require_once('lib/sms_service_def/sms_service_def.inc.php');
include_once('lib/smsacct/SMSAcct.class.php');
require_once('lib/smsacct/SMSAcctRequest.class.php');
include_once('lib/smsacct/SMSAcctNotification.class.php');

$msgs = array();

$PHONE = !empty($_GET['phone']) ? $_GET['phone'] : NULL;
$SMSC = !empty($_GET['smsc']) ? $_GET['smsc'] : NULL;
$TEXT = !empty($_GET['text']) ? $_GET['text'] : NULL;

if(!empty($config->smsacct->dbsrv->host)) {
    $acct = new SMSAcctRequest($config->smsacct->toArray(), $PHONE, $SMSC, SMSCHP);

    $acct->setRequestData(array(
        'type' => SMSCHP_CHP,
        'message' => !empty($TEXT) ? $TEXT : NULL
    ));
} else {
    $acct = false;
}

if(!isset($SMSC,$PHONE,$TEXT)) {
	Arcanum_SMSDispatch::send(SMS_INVALID_REQUEST,$acct);
}

if(isset($smsc_deny) && !(array_search($SMSC,$smsc_deny)===false)) {
	Arcanum_SMSDispatch::send(SMS_SMSC_DENIED,$acct,$SMSC,$PHONE);
}

if(isset($smsc_allow) && (array_search($SMSC,$smsc_allow)===false)) {
	Arcanum_SMSDispatch::send(SMS_SMSC_NOT_ALLOWED,$acct,$SMSC,$PHONE);
}

$arcanumLdap = new Arcanum_Ldap();
$ldap = $arcanumLdap->connect();

if (!$ldap) {
	Arcanum_SMSDispatch::send(SMS_LDAP_CONNECT,$acct,$SMSC,$PHONE);
}

$filter = sprintf($config->ldap->filter->user_receivesms, $PHONE);
$result = @ldap_search($ldap, $config->ldap->basedn, $filter, array('uid'),0,9);

if ($result === false) {
	Arcanum_SMSDispatch::send(SMS_LDAP_SEARCH,$acct,$SMSC,$PHONE);
}

$entries = ldap_get_entries($ldap, $result);
if($entries['count']==0) {
	Arcanum_SMSDispatch::send(SMSCHP_NOT_FOUND,$acct,$SMSC,$PHONE);
} 

$username="";

if($entries['count']==1) {
	$username=$entries[0]['uid'][0];
} else {
	$uids=array();
	for ($i=0; $i < $entries["count"]; $i++)
		$uids[]=$entries[$i]['uid'][0];
	sort($uids);
	$numuids=count($uids);
	if(preg_match('/^[^ 0-9]+ *([1-9])$/', $TEXT, $matches)==1) {
		$choice=intval($matches[1]);
		if($choice>0 && $choice <= $numuids)
			$username=$uids[$choice-1];
	}
	if($username=="") {
		Arcanum_SMSDispatch::send(SMSCHP_LIST,$acct,$SMSC,$PHONE,$uids);
	}
}

$envStore = new Arcanum_EnvironmentStore();

$initiated_reset_pw = $envStore->get('initiated_reset_pw');
if($initiated_reset_pw  === false || !isset($initiated_reset_pw[$username]) ) {
    // FAIL! no session. I won't even bother replying to the sms.
    exit;
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

Arcanum_SMSDispatch::send(SMSCHP_OK,$acct,$SMSC,$PHONE,array(),$username,$token);

// upon error:
// FIXME
//Arcanum_SMSDispatch::send(SMSCHP_FAIL,$acct,$SMSC,$PHONE,$msgs,$username,$token);


