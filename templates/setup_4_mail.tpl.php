
<h3><?= _("E-mail Settings") ?></h3>

<p><?= _("Here you can set up the e-mail submission settings. The e-mail messages that are sent from this application can be instant notifications (&ldquo;Your password has just changed&rdquo;), password reset requests (&ldquo;Click here to reset your password&rdquo;) or periodical notifications (&ldquo;Attention, your password expires in one week&rdquo;).") ?></p>


<form name="arcanumsetupform" class="nice" action="setup.php" method="POST">
<input type="hidden" name="submitstep" value="4_mail" />

<fieldset>

<div class="form-field" id="mail__host">
<label><?= _("Mail Host (SMTP / Submission)") ?></label>
<input type="text" class="input-xlarge" name="mail__host" placeholder="mail.organization.gr" value="<?= f_val($config->mail->host); ?>" />
</div>

<div class="form-field" id="mail__from">
<label><?= _("Mail From: Address") ?></label>
<input type="text" class="input-xlarge" name="mail__from" placeholder="no-reply@organization.gr" value="<?= f_val($config->mail->from); ?>" />
</div>

<div class="form-field" id="mail__fromComment">
<label><?= _("Mail From: Name") ?></label>
<input type="text" class="input-xlarge" name="mail__fromComment" placeholder="<?= _("Password Management Service") ?>" value="<?= f_val($config->mail->fromComment); ?>" />
</div>

<div class="form-field" id="mail__replyto">
<label><?= _("Mail Reply-To: address (optional)") ?></label>
<input type="text" class="input-xlarge" name="mail__replyto" value="<?= f_val($config->mail->replyto); ?>" />
</div>

<div class="form-field" id="mail__smtp__ssl">
<label><?= _("SSL") ?></label>
<input type="checkbox" class="medium" name="mail__smtp__ssl" <?php echo ($config->mail->smtp->ssl == 'ssl' ? ' selected="" ' : '' ); ?> />
</div>

<div class="form-field" id="mail__smtp__port">
<label><?= _("SMTP Port") ?></label>
<input type="text" class="input-small" name="mail__smtp__port" value="<?= f_val($config->mail->smtp->port); ?>" />
</div>

<div class="form-field" id="mail__smtp__auth">
<label><?= _("SMTP authentication (optional, enter 'login' to perform authentication)") ?></label>
<input type="text" class="input-xlarge" name="mail__smtp__auth" value="<?= f_val($config->mail->smtp->auth); ?>" />
</div>

<div class="form-field" id="mail__smtp__username">
<label><?= _("SMTP username (optional)") ?></label>
<input type="text" class="input-xlarge" name="mail__smtp__username" value="<?= f_val($config->mail->smtp->username); ?>" />
</div>

<div class="form-field" id="mail__smtp__password">
<label><?= _("SMTP password (optional)") ?></label>
<input type="password" class="input-xlarge" name="mail__smtp__password" value="<?= f_val($config->mail->smtp->password); ?>" />
</div>



<?php

$this->display('setup_save_button');

?>

</form>



