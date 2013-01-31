<?php
/**
 * Template for reset password form
 *
 * @package Arcanum
 * @subpackage Templates
 * @version $Id: reset_password_start.tpl.php 5955 2012-12-28 11:12:41Z avel $
 */

/*
// TODO
    if(in_array('surname', $required_info)) 
    if(in_array('email', $required_info))
*/
?>
     <h1><?= _("Confirm your account information to reset your password") ?></h1>
    
     <!--Left part of home page-->
     <div id="left-home">
   		<div class="introtext">
   		<?= _("If you have forgotten your password, you'll need to confirm your password recovery preferences first before we allow you to reset your password.")?>
  		</div>
        
        <div class="box-form"> <!--Form box -->
        <form method="post" action="reset_password.php" class="form-inline">
                <br />
                <div class="form-line">
                	<div class="formleft-text"><?= _("Enter your username:") ?></div>
                    <div class="formleft-input"><input type="text" name="login_username" id="login_username" value="" class="nice small input-text"  maxlength="25"/></div>
                </div>

<?php
if( in_array('sms', $all_methods) && in_array('email', $all_methods)) {
    // BOTH email & sms
?>

                <div class="form-line">
                	<div class="formleft-text"><?= _("Choose one of the following password recovery methods:") ?></div>
                </div>
                 
                 <div class="formleft-input">
                     <input type="radio" name="method" class="method_radio" id="email_radio" value="email" style="margin: 2px;" /> <label for="email_radio"><?= _("Send via e-mail") ?></label>
                     </div>
                     <div class="form-sendby" id="email">
                        <div class="formconf-text"><?= _("Enter your surname:") ?></div>
                        <div class="formconf-input"><input type="text"  name="email_surname" maxlength="25"/></div>
                        
                        <div class="formconf-text"><?= _("Enter the secondary e-mail that you have registered:") ?></div>
                        <div class="formconf-input"><input type="text" name="email_email"  maxlength="25"/></div>
                    </div>
                    <div class="formleft-input">
                     <input type="radio" name="method" class="method_radio" id="sms_radio" value="sms" style="margin: 2px;" /> <label for="sms_radio"><?= _("Send via SMS") ?></label>
                     <div class="form-sendby" id="sms">
                        <div class="formconf-text"><?= _("Enter your surname:") ?></div>
                        <div class="formconf-input"><input type="text"  name="sms_surname" maxlength="25"/></div>
                        
                        <div class="formconf-text"><?= _("Enter the mobile phone number that you have registered:") ?></div>
                        <div class="formconf-input"><input type="number" name="sms_sms" maxlength="25" size="25"/></div>

                    </div>
                </div>

<?php
} else {
    // ONLY e-mail
?>
                 <div class="formleft-input">
                     
                     <input type="hidden" name="method" value="email" />
                     <div class="form-sendby">
                        <div class="formconf-text"><?= _("Enter your surname:") ?></div>
                        <div class="formconf-input"><input type="text"  name="email_surname" maxlength="25"/></div>
                        
                        <div class="formconf-text"><?= _("Enter the secondary e-mail that you have registered:") ?></div>
                        <div class="formconf-input"><input type="text" name="email_email"  maxlength="25"/></div>
                    </div>
                </div>
<?php
}
?>

                <div class="form-line" id="inputcaptcha">
                    <br/>
                    <div class="formleft-text"><?= _("Finally, please enter these two words:" ) ?></div>
                    <?= $captcha_html ?>
                </div>

                
               <div class="button-right">
                  <input type="submit" name="reset_password_do" class="button blue" value="<?= _("Continue &rarr;") ?>" />
               </div>
               
            
          </form>
          </div> <!-- End of login with account box -->
     </div>
    </div>


