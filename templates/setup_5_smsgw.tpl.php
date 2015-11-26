
<h3><?= _("SMS Gateway Settings") ?></h3>

<form name="arcanumsetupform" class="nice" action="setup.php" method="POST">
<input type="hidden" name="submitstep" value="5_smsgw" />

<fieldset>

<h3><?= _("SMS Gateway") ?></h3>

<div class="form-field" id="sms_operator_number">
<label><?= _("Operator Number") ?></label>
<input type="text" class="input-xlarge" name="sms_operator_number" placeholder="6931234567" value="<?= f_val($config->sms_operator_number); ?>" />
</div>

<div class="form-field" id="smsgw__receiver">
<label><?= _("Method to use for receiving Text Messages") ?></label>
<select class="input-xxlarge" name="smsgw__receiver">
    <option value="" <?= ($config->smsgw->receiver == '' ? 'selected=""' : '' ) ?>>
        <?= _("None") ?>
    </option>
    <option value="kannel" <?= ($config->smsgw->receiver == 'kannel' ? 'selected=""' : '' ) ?>>
        <?= _("HTTP(S) connection to Kannel Gateway") ?>
    </option>
    <option value="jsonrpc" <?= ($config->smsgw->receiver == 'jsonrpc' ? 'selected=""' : '' ) ?>>
        <?= _("JSON-RPC over HTTP(S) to GunetSMS (or compatible) Gateway") ?>
    </option>
</select>
</div>


<div class="form-field" id="smsgw__sender">
<label><?= _("Method to use for sending Text Messages") ?></label>
<select class="input-xxlarge" name="smsgw__sender">
    <option value="" <?= ($config->smsgw->sender == '' ? 'selected=""' : '' ) ?>>
        <?= _("None") ?>
    </option>
    <option value="kannel" <?= ($config->smsgw->sender == 'kannel' ? 'selected=""' : '' ) ?>>
        <?= _("HTTP(S) connection to Kannel Gateway") ?>
    </option>
    <option value="jsonrpc" <?= ($config->smsgw->sender == 'jsonrpc' ? 'selected=""' : '' ) ?>>
        <?= _("JSON-RPC over HTTP(S) to GunetSMS (or compatible) Gateway") ?>
    </option>
</select>

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
<input type="text" class="input-xlarge" name="smsgw__username" autocomplete="off" placeholder="" value="<?= f_val($config->smsgw->username); ?>" />
</div>

<div class="form-field" id="smsgw__password">
<label><?= _("Password") ?></label>
<input type="password" class="input-xlarge" name="smsgw__password" autocomplete="off" placeholder="" value="<?= f_val($config->smsgw->password); ?>" />
</div>


<div class="form-field" id="smsgw__tout_con">
<label><?= _("Timeout when connecting to SMS Gateway, in seconds") ?></label>
<input type="text" class="input-xlarge" name="smsgw__tout_con" placeholder="10" value="<?= f_val($config->smsgw->tout_con); ?>" />
</div>

<div class="form-field" id="smsgw__prefix">
<label><?= _("SMS Prefix / Text to be sent by the user") ?></label>
<input type="text" class="input-xlarge" name="smsgw__prefix" placeholder="PASS" value="<?= f_val($config->smsgw->prefix); ?>" />
</div>

</fieldset>

<?php

$this->display('setup_save_button');

?>

</form>



