<?php
/**
 * Login Page Template.
 *
 * @package change_password
 * @subpackage templates
 * @version $Id: login.tpl.php 5954 2012-12-28 10:39:09Z avel $
 */

$this->display('html_header');
$this->display('page_header');

?>

 
<h1><?= _("Password Management Service") ?></h1>

<div id="left-home">
<div class="introtext">

<?php

if($expired) {
    echo '<p>'. _("Your password has expired. In order to continue using the provided services, you have to change your password.") . '</p>'.
        '<p>' . _("Please log in with your old password and change it with a new one.") . '</p>';

} elseif($resetted) {
    echo '<p>' . _("The current password can only be used to enter a new, personalized password. Once you change your password, you will be able to log in to services again.") . '</p>' .
        '<p>' . _("Please log in with your old password and change it with a new one.") . '</p>';

} elseif(!empty($intro)) {
    echo $intro;
} else {
    echo '<p><strong>'. _("Welcome to the User Password Management Service.") . '</strong><br /><br />'.
    _("Here you can change or recover your password.") . '</p>';
}

if(!empty($loginMotd)) {
    echo '<br/>
        <p>' . _("News &amp; Announcements") . '</p>';
    echo '<blockquote id="loginMotd">
        '.$loginMotd.'
    </blockquote>';
}

?>


<?php
$this->display('alert_messages');
?>


</div>
</div>
     
     <!--Right part of home page-->
     <div id="right-home">

            <form method="post" action="redirect.php">
        	<div class="box-form" id="account"> <!--Login with account box -->

            	<h3><?= _("Login") ?></h3>
                <?php
if(isset($message_above_login_box)) {
?>
<strong style="color: red; margin: 0;"><?= htmlspecialchars($message_above_login_box) ?></strong><br/><br/>
<?php    
}
?>


                <div class="form-line">
                	<div class="form-text"><?= _("Username:") ?></div></label>
                    <div class="form-input"><input type="text" name="login_username" id="login_username"   maxlength="25" style="width:100%;" /></div>
                </div>
                <div class="form-line">
                	<div class="form-text"><?= _("Password:") ?></div></label>
                    <div class="form-input"> <input type="password" name="password" id="password"  maxlength="25" style="width:100%;" /></div>
                </div>
                <div class="form-line">
<?php

if(isset($captcha_html)) {
    echo '
        <p>'.$captcha_html.'</p>
    ';
}

?>
    
<?php

if($expired) {
    echo '<input type="hidden" name="expired" value="1" />';
    echo '<input type="hidden" name="startpage" value="changepassword" />';
}
if($renew) {
    echo '<input type="hidden" name="renew" value="1" />';
}
if(isset($service)) {
    echo '<input type="hidden" name="service" value="'.rawurlencode($service).'" />';
}

?>
                   <div class="form-text">&nbsp;</div>
                    <div class="button-left">
                        <input type="submit" name="loginDo" class="button blue" value="<?= _("Login") ?>" />
                   </div>
			   </div>
<?php
if(Arcanum_Util::areSecondaryAccountsActive()) {
?>
                <div class="iconlink-line">
                	<div class="lefticon"><img src="images/forgot_password2.png" title="<?= _("I have forgotten my password") ?>" /></div>
                    <div class="rightlink">
                        <a href="reset_password.php"><?= _("I have forgotten my password") ?></a>
                    </div>
                </div>
<?php
}
?>
                
            
            </div>
            </form>
     </div>
  
<?php
$this->display('page_footer');
$this->display('html_footer');

