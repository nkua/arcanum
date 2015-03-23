<?php
/**
 * @version $Id: dataentry.tpl.php 5961 2013-01-04 13:10:38Z avel $
 */
?>

<?php
$this->display('alert_messages');
?>

<form id="secondaryaccountsform" name="secondaryaccountsform" method="POST" action="<?= $formtarget ?>">

     <h1><?= _("Password Recovery Information") ?></h1>
    
     <div id="left-home">
   		<div class="introtext">

<?php
if($modified === true) {
?>
            <div class="box-success">
                <img src="images/tick.png" />
                <?= _("Your information has been updated.") ?>
            </div>
<br/>
<br/>
<?php
}
?>

            <p><?= _("For the case that you ever forget your password, you should register a secondary e-mail address or a mobile phone number with us; this way, you can reset your password easily.") ?></p>

   		</div>
     </div>
     
     <div id="right-home">
        	<div class="box-form" id="account"> <!--Login with account box -->
            	
<?php
if($ask_old_password === true) {
?>
                <div class="form-line">
                	<div class="form-text"><?= _("Password:") ?></div>
                    <div class="form-input"> <input type="password"  id="oldpass" name="pass" maxlength="26" style="width:100%;" /></div>
                </div>
<?php
}
?>

                <div class="form-line">
                	<div class="form-text"><?= _("Secondary e-mail address:") ?></div>
                    <div class="form-input"> <input type="text" name="email" maxlength="45" value="<?= htmlspecialchars($secondary_accounts_values['email']) ?>"style="width:100%;"  /></div>
                </div>
                <div class="form-line">
                	<div class="form-text"><?= _("Mobile phone number:") ?></div>
                    <div class="form-input"> <input type="text" name="sms" maxlength="15" value="<?= htmlspecialchars($secondary_accounts_values['sms']) ?>" style="width:100%;"  /></div>
                </div>
                
                <div class="form-line">
                	<div class="form-text">&nbsp;</div>
                    <div class="form-input"><div class="button-left">
					<input type="submit" name="submit_values" class="button blue" value="<?= _("Save") ?>" /> 
				</div>
                	</div>
                </div>
               
            
            </div>
     </div>

<?php
if( isset($service) && ( !isset($opted_out) || $opted_out == false) &&
    (!empty($secondary_accounts['sms']) && empty($secondary_accounts_values['sms'])) &&
    (!empty($secondary_accounts['email']) && empty($secondary_accounts_values['email'])) 
) {
    // Nothing is set and we are in the form that asks the password as well (e.g. intermediate
    // step after CAS login), so display the optout / skip buttons
?>
     <div class="clear"></div>
         <!--Left part of home page-->
     <div id="left-home">
     <div class="smaller-text">
     <div class="left"><a href="<?= htmlspecialchars($service, ENT_QUOTES) ?>"><?= _("Skip") ?></a></div>
     <div class="right"><a href="dataentry.php?enable_optout=1"><?= _("Disable reminder") ?></a>
        </div>
     </div>
     </div>
<?php    
}
?>
     
</form>
