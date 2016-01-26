<?php
/**
 * Main Page Template.
 *
 * @package arcanum
 * @subpackage templates
 * @version $Id: change_password.tpl.php 5954 2012-12-28 10:39:09Z avel $
 */
?>

<form method="post" action="changepassword.php">

    <h1><?= _("Change Password") ?></h1>
    

    <?php
    if(!empty($workflow)) {
        $this->display('change_password_'.$workflow);
    }
    ?>
    <div id="left-home">
        <?php if(!empty($msgs)) { ?>
        <p><?= _("Errors encountered. Please correct the following and try again:") ?></p>
        <?php
        $this->display('alert_messages');
    }
    ?>

    <div class="introtext">
        <strong><?= sprintf( _("Allowed characters: %s"), '<tt>a-z A-Z 0-9 !@#$%^&amp;*()_+-=[]{}:;&quot;\',./&gt;&lt;/?</tt>') ?>...</strong><br /><br />

        <i><?= sprintf( _("Password must consist of at least %s characters; "), $pw_min_len); ?><br />
            <?= sprintf( _("Must contain at least %s number or symbol"), $pw_min_nonalpha); ?><br /></i>

            <div class="more-link"><a href="safety.php"><?= _("More information about password safety") ?></a></div></div>

       
       <strong><?= _("Password Suggestions") ?></strong>

       <p><?= _("Pick one of these easy to pronounce passwords:") ?></p>
      <div id="password_suggestions">
      </div>
      <a href="#" id="get_other_suggestions"><?= _("Get other suggestions") ?></a>

    </div>

        <!--Right part of home page-->
        <div id="right-home">
            
        	<div class="box-form" id="account"> <!--Login with account box -->

                <?php
                if($ask_old_password === true) {
                    ?>
                    <div class="form-line">
                       <div class="form-text"><?= _("Password:") ?></div>
                       <div class="form-input"> <input type="password"  id="cp_oldpass" name="cp_oldpass" value="" maxlength="15" style="width:100%;" /></div>
                   </div>

                   <?php
               }
               ?>
               <div class="form-line">
                   <div class="form-text"><i id="popover_mark_cp_newpass"></i> <?= _("New Password:") ?></div>
                   <div class="form-input"><input type="password" id="cp_newpass" name="cp_newpass" value="" maxlength="15" style="width:100%;" /></div>
               </div>
               <div class="form-line">
                   <div class="form-text"><i id="popover_mark_cp_verify"></i> <?= _("Confirm New Password:") ?></div>
                   <div class="form-input"> <input type="password" id="cp_verify" name="cp_verify" value="" maxlength="15" style="width:100%;" /></div>
               </div>
               
               
               <div class="form-line">
                   <div class="form-text">&nbsp;</div>
                   <div class="form-input"><div class="button-left">
                       <input type="submit" class="button blue btn" value="<?= _("Save") ?>" id="changepass_do" name="changepass_do" />
                   </div>
               </div>
           </div>
           
           
       </div> <!-- End of login with account box -->
   </div>

</form>
