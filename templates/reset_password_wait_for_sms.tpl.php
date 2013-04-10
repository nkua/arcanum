    <div id="main">
    
     <h1><?= _("Confirm your account information to reset your password") ?></h1>
     
     
     <div id="left-home">
     	<div class="box-form" id="sendinstructions">
            <?= _("Please send the following text message via your mobile phone.") ?>
            <br /><br />
            <strong><?= _("Text message:") ?> <?= $sms_body ?><br />
              <?= _("Recipient number:") ?> <?= $sms_to ?></strong>
              <br /><br /><br />

              <i><?= _("* Standard text message cost") ?></i>

<?php
/* TODO
            <img src="qrcode.php?operation=qr_sms&amp;to=<?= $sms_to ?>&amp;content=<?= urlencode($sms_body) ?>" alt="<?= _("QR Code") ?>"/>
  */
            $dummy = _("QR Code");
  ?>  
    		<div class="waitsms" id="smsstatus"><?= _("Waiting to receive text message&hellip;") ?></div>
    	</div>
        
        <div class="box-form" id="tokenform" style="display: none;">
    	   <?= _("Your details have been confirmed.") ?>
            <br /><br /><br />
            <?= _("Enter the verification number that we just sent to your mobile, in order to reset your password:") ?>
            <br /><br />
            
            <form action="changepassword.php" method="POST">
                <input type="text" name="token" value="" placeholder="123456789012" size="25" /><br /><br />
                <div class="button-right">
                    <input type="submit" name="sms_token" value="<?= _("Continue &rarr;") ?>" class="button blue" />
                </div>
            </form>
    		
    	</div>

     </div>
</div>

