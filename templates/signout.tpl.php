<?php
/**
 * Signout / Session Expired Template.
 *
 * @package change_password
 * @subpackage templates
 * @version $Id: signout.tpl.php 5954 2012-12-28 10:39:09Z avel $
 */

$this->display('html_header');
$this->display('page_header');

if($forced > 0) {
    if($forced == Arcanum_Session::LOGOUT_REASON_ACCOUNT_LOCKED) {
        $this->display('account_locked');
    } else {
        $msg = Arcanum_Session::getLogoutReasonMessage($forced);
        
    }
} else {
    $msg = _("You have signed out successfully.");
}

if($success && isset($username)) {
    $msg = sprintf( _("The password of the account %s has been successfully changed."), $username);
}

$this->assign('msgs', array( array('msg' => $msg) ));
if($forced) {
    $this->display('alert_messages');
} elseif($success) {
    $this->display('success_messages');
} else {
    echo '<p style="text-align: center;">'.$msg.'</p>';
}


echo '<br/><div style="text-align: center;">';

if(isset($service)) {
    echo '<p>' . sprintf( _("<a href=\"%s\">Continue to service %s</a> (you will be asked to reenter your password)"),
                     htmlspecialchars($service), htmlspecialchars($service) ) . '</p>';
} else {
    echo '<a href="index.php'.$flags.'">' . _("Login Page") . '</a>';
}
echo '</div>';


if(!empty($error_message)) {
?>

    <br/>
    <p><?= sprintf( _("Note: The server has reported this error message: %s. Please contact your administrator and mention this message."),
     '<br/><tt>'.htmlspecialchars($error_message).'</tt><br/><br/>') ?></p>';
<?php
}
    
$this->display('page_footer');
$this->display('html_footer');

