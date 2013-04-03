
<h3><?= _("SMS Gateway Settings") ?></h3>

<form name="arcanumsetupform" class="nice" action="setup.php" method="POST">
<input type="hidden" name="submitstep" value="5_smsgw" />

<fieldset>

<h3><?= _("SMS Gateway") ?></h3>

<div class="form-field" id="sms_operator_number">
<label><?= _("Operator Number") ?></label>
<input type="text" class="input-xlarge" name="sms_operator_number" placeholder="306999999999" value="<?= f_val($config->sms_operator_number); ?>" />
</div>

<div class="form-field" id="smsgw__host">
<label><?= _("SMS Gateway Host") ?></label>
<input type="text" class="input-xlarge" name="smsgw__host" placeholder="ssl://smsgw.example.org" value="<?= f_val($config->smsgw->host); ?>" />
</div>

<div class="form-field" id="smsgw__port">
<label><?= _("Port") ?></label>
<input type="text" class="input-xlarge" name="smsgw__port" placeholder="" value="<?= f_val($config->smsgw->port); ?>" />
</div>

<div class="form-field" id="smsgw__uri">
<label><?= _("URI (path)") ?></label>
<input type="text" class="input-xlarge" name="smsgw__uri" placeholder="/sms" value="<?= f_val($config->smsgw->uri); ?>" />
</div>

<div class="form-field" id="smsgw__username">
<label><?= _("Username") ?></label>
<input type="text" class="input-xlarge" name="smsgw__username" placeholder="" value="<?= f_val($config->smsgw->username); ?>" />
</div>

<div class="form-field" id="smsgw__password">
<label><?= _("Password") ?></label>
<input type="password" class="input-xlarge" name="smsgw__password" placeholder="" value="<?= f_val($config->smsgw->password); ?>" />
</div>

</fieldset>

<?php

$this->display('setup_save_button');

?>

</form>



