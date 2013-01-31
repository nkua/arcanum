<?php
/**
 * Change password of other users (admin form).
 *
 * @package arcanum
 * @version $Id: admin_sessions.php 5849 2012-10-11 06:27:20Z avel $
 */

$initLocation = 'admin_sessions';
require_once('include/init.php');

$msgs = array();

$sessions = array();

$sessiondir = ini_get('session.save_path');

if(strstr($sessiondir, ';')) {
    $msgs[] = array('class' => 'error', 'msg' => 'The application does not support hashed session directories yet.');
} else {

    if ($handle = opendir($sessiondir)) { 
        while (false !== ($entry = readdir($handle))) {
            if($entry == '.' || $entry == '..') continue;

            $variables = array();
            
            $serialized2 = file_get_contents($sessiondir . '/' . $entry);
            $arr = preg_split( "/(\w+)\|/", $serialized2, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
            for( $i = 0; $i < count( $arr ); $i = $i+2 ) {
                $variables[$arr[$i]] = unserialize( $arr[$i+1] );
            }

            $sessions[] = $variables;
        }
        closedir($handle);
    }
}

$t->assign('sessions', $sessions);


// get xcache active tokens

$vcnt = xcache_count(XC_TYPE_VAR);

//print "count = $vcnt";
$cacheinfos = array();
for ($i = 0; $i < $vcnt; $i ++) {
	$data = xcache_info(XC_TYPE_VAR, $i);
    $data += xcache_list(XC_TYPE_VAR, $i);
	$data['type'] = XC_TYPE_VAR;
	$data['cache_name'] = "var#$i";
	$data['cacheid'] = $i;
	$cacheinfos[] = $data;
    /*
	if ($pcnt >= 2) {
		calc_total($total, $data);
	}
     */
}

$all_tokens = array();
$active_sms_initiated_tokens = array();
$active_sms_tokens = array();
$active_mail_tokens = array();


if(isset($cacheinfos[0]['cache_list'])) {
    // --- get all tokens -- 
    $tokenStore = new Arcanum_SharedMemory();
    $tokenStore->init('', 0); 

    $prefix = $tokenStore->get_global_prefix(); 
    $regex = '/^'.$prefix.'/'; 

    foreach($cacheinfos[0]['cache_list'] as $tok) {
        //if(!preg_match($regex, $tok['name'])) continue;
        $tokenstore_id = preg_replace($regex, '', $tok['name']); 
        
        $all_tokens[] = array(
            'name' => $tok['name'],
            'timestamp' => $tok['ctime'],
            'data' => $tokenStore->get($tokenstore_id),
        );
    }

    unset($tokenStore);

    // --- get sms tokens -- 
    $tokenStore = new Arcanum_Token_Sms();
    $prefix = $tokenStore->get_global_prefix() . $tokenStore::PREFIX . '_'; 
    $regex = '/^'.$prefix.'/'; 

    foreach($cacheinfos[0]['cache_list'] as $tok) {
        if(!preg_match($regex, $tok['name'])) continue;

        $tokenstore_id = preg_replace($regex, '', $tok['name']); 

        $active_sms_tokens[] = array(
            'uid' => $tokenStore->get_token($tokenstore_id),
            'timestamp' => $tok['ctime'],
        );
    }

    unset($tokenStore);

    // --- get mail tokens -- 

    $tokenStore = new Arcanum_Token_Email();
    $prefix = $tokenStore->get_global_prefix() . $tokenStore::PREFIX . '_'; 
    $regex = '/^'.$prefix.'/'; 

    foreach($cacheinfos[0]['cache_list'] as $tok) {
        if(!preg_match($regex, $tok['name'])) continue;

        $tokenstore_id = preg_replace($regex, '', $tok['name']); 
        $active_mail_tokens[] = array(
            'uid' => $tokenStore->get_token($tokenstore_id),
            'timestamp' => $tok['ctime'],
        );
    }
}

$t->assign('active_sms_tokens', $active_sms_tokens);
$t->assign('active_mail_tokens', $active_mail_tokens);


// ================= Display =================

$t->assign('msgs', $msgs);
$t->assign('javascripts', $defaultJavascripts);

$t->display('html_header');
$t->display('page_header_admin');

if(!empty($msgs)) {
    $t->assign('msgs', $msgs);
    $t->display('alert_messages');
}

$t->display('admin_sessions');

$t->display('page_footer_admin');
$t->display('html_footer');

