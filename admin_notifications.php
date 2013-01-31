<?php
/**
 * @package arcanum
 * @version $Id: admin_notifications.php 5849 2012-10-11 06:27:20Z avel $
 */

$initLocation = 'admin_notifications';
require_once('include/init.php');

$msgs = array();

$show_all = (isset($_GET['show_all']) ? true : false);
$show_template_unvalidated = (isset($_GET['show_template']) ? $_GET['show_template'] : false);
$show_template = false;

$notifications = Arcanum_Notifications::get_configured_notifications();
$status = Arcanum_Notifications::get_current_status();

// counts
$counts = array();
foreach($notifications as $n) {
    $counts[$n['id']] = 0;

    // validation of get parameter
    if($show_template_unvalidated) {
        if($n['id'] === $show_template_unvalidated) {
            $show_template = $n['id'];
        }
    }
}
foreach($status as $uid => $data) {
    foreach($data as $key => $val) {
        if($val == 'SENT') {
            $counts[$key]++;
        }
    }
}


// ================= Display =================

$t->assign('show_all', $show_all);
$t->assign('show_template', $show_template);
$t->assign('status', $status);
$t->assign('msgs', $msgs);
$t->assign('notifications', $notifications);
$t->assign('counts', $counts);
$t->assign('javascripts', $defaultJavascripts);

$t->display('html_header');
$t->display('page_header_admin');

if(!empty($msgs)) {
    $t->assign('msgs', $msgs);
    $t->display('alert_messages');
}

if($show_template) {
    $t->display('admin_notifications_show_template');

} else {
    $t->display('admin_notifications');

    if($show_all) {
        $t->display('admin_notifications_show_all');
    }
}


$t->display('page_footer_admin');
$t->display('html_footer');

