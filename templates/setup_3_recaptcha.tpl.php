<img src="images/recaptcha-logo.jpg" alt="recaptcha log" class="pull-right"/>

<h3><?= _("Usage of ReCAPTCHA Service") ?></h3>

<p><?= _("This application can use the ReCAPTCHA Service  to protect login forms from robots and dictionary attacks.") ?></p>


<p><?= sprintf( _("To enable, go to the page %s, choose %s and create an API key for this site."),
        '<a href="http://www.google.com/recaptcha/whyrecaptcha" target="_blank">Get ReCAPTCHA</a>',
        '&quot;Sign up Now!&quot;' ) ?></p>

<p>
<?= _("Then, enter your Public and Private keys here:") ?>
</p>

<form name="arcanumsetupform" class="nice" action="setup.php" method="POST">
<input type="hidden" name="submitstep" value="3_recaptcha" />

<fieldset>
<div class="form-field" id="recaptcha__pubkey">
<label><?= _("Public Key") ?></label>
<input type="text" class="input-xxlarge" name="recaptcha__pubkey" placeholder="123456abcd" value="<?= f_val($config->recaptcha->pubkey); ?>" />
</div>

<div class="form-field" id="recaptcha__privkey">
<label><?= _("Private Key") ?></label>
<input type="text" class="input-xxlarge" name="recaptcha__privkey" placeholder="123456abcd" value="<?= f_val($config->recaptcha->privkey); ?>" />
</div>

<?php

$this->display('setup_save_button');

?>

</form>



