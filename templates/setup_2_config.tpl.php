
<form name="arcanumsetupform" class="nice" action="setup.php" method="POST">
<input type="hidden" name="submitstep" value="2_config" />

<fieldset>

<h3><?= _("LDAP Server Connection") ?></h3>

<div class="form-field" id="ldap__host">
<label><?= _("LDAP Host or LDAP URI, e.g. ldaps://host:636") ?></label>
<input type="text"  <?= ($editing_existing ? 'disabled="DISABLED"' : '') ?>  class="input input-xxlarge" name="ldap__host" value="<?= f_val($config->ldap->host); ?>" />
</div>

<div class="form-field" id="ldap__basedn">
<label><?= _("LDAP Base DN") ?></label>
<input type="text"  <?= ($editing_existing ? 'disabled="DISABLED"' : '') ?> class="input input-xxlarge" name="ldap__basedn" value="<?= f_val($config->ldap->basedn); ?>" />
</div>

<div class="form-field" id="ldap__bind">
<label><?= _("Bind DN") ?></label>
<input type="text"  <?= ($editing_existing ? 'disabled="DISABLED"' : '') ?> class="input input-xxlarge" name="ldap__bind" value="<?= f_val($config->ldap->bind); ?>" />
</div>

<div class="form-field" id="ldap__password">
<label><?= _("Bind Password") ?></label>
<input type="password"  <?= ($editing_existing ? 'disabled="DISABLED"' : '') ?> class="input" name="ldap__password"
  value="<?= ($editing_existing ? '**********' : f_val($config->ldap->password) ) ?>" />
</div>

</fieldset>

<?php
if(!$editing_existing) {
?>

<div>
    <button class="btn btn-primary" id="ldap_test_connection"><?= _("Test Connection") ?></button>
</div>
<br/>
<div id="ldap_test_result">
    <div class="alert alert-info"><?= _("Click button to test connection parameters.")?></div>
</div>
<?php
}
?>


<h3><?= _("CAS (Login) Server Connection") ?></h3>

<fieldset>

<div class="form-field" id="cas__host">
<label><?= _("CAS Host") ?></label>
<input type="text" class="input input-xxlarge" placeholder="login.example.org" name="cas__host" value="<?= f_val($config->cas->host); ?>" />
</div>

<div class="form-field" id="cas__port">
<label><?= _("CAS Port") ?></label>
<input type="text" class="input" name="cas__port" value="<?= f_val($config->cas->port); ?>" />
</div>

<div class="form-field" id="cas__uri">
<label><?= _("CAS URI Path") ?></label>
<input type="text" class="input input-xxlarge" placeholder="/path" name="cas__uri" value="<?= f_val($config->cas->uri); ?>" />
</div>

</fieldset>

<fieldset>

<h3><?= _("LDAP Configuration with regard to Passwords") ?></h3>

<?php
/*
<div class="form-field" id="ldap__passwordAttribute">
<label>Όνομα attribute στο οποίο θα αποθηκεύεται το password</label>
<input type="text" class="input input-xxlarge" name="ldap__" value="<?= f_val($config->ldap->passwordAttribute); ?>" />
</div>
 */
?>


<div class="form-field" id="ldap__passwordHash">
<label><?= _("LDAP Password Hash") ?>
<br/><?= _("Note: affects only newly created passwords; existing passwords will remain as they are") ?></label>
<select class="input input-medium" name="ldap__passwordHash">
<option value="crypt" <?php if($config->ldap->passwordHash == 'crypt') echo 'selected=""'; ?>>CRYPT</option>
<option value="sha" <?php if($config->ldap->passwordHash == 'sha') echo 'selected=""'; ?>>SHA</option>
<option value="ssha" <?php if($config->ldap->passwordHash == 'ssha') echo 'selected=""'; ?>>SSHA</option>
</select>
</div>

<div class="form-field" id="ldap__sambaNtAttribute">
<label><?= _("NT Hash attribute (optional)") ?></label>
<input type="text" class="input-medium" name="ldap__sambaNtAttribute" value="<?= f_val($config->ldap->sambaNtAttribute); ?>" />
</div>

<div class="form-field" id="ldap__ctpAttribute">
<label><?= _("Attribute for symmetrically encrypted cleartext password (optional)") ?></label>
<input type="text" class="input-medium" name="ldap__ctpAttribute" value="<?= f_val($config->ldap->ctpAttribute); ?>" />
</div>

<div class="form-field" id="ldap__ctpKey">
<label><?= _("24-byte key for symmetric password encryption (base64-encoded)") ?></label>
<input type="text" class="input-xxlarge" name="ldap__ctpKey" value="<?= f_val($config->ldap->ctpKey); ?>" />
</div>

<div class="form-field muted" id="ldap__actpAttribute">
<label><?= _("Attribute for asymmetrically encrypted cleartext password (optional)") ?></label>
<input type="text" disabled="DISABLED" class="input-medium" />
<span class="label"><?= _("Feature not implemented yet") ?></span>
</div>

<div class="form-field muted" id="ldap__otpInitKeyAttribute">
<label><?= _("Attribute for Time-Based OTP Init Key (format: base-32-encoded) (optional)") ?></label>
<input type="text" disabled="DISABLED" class="input-medium" />
<span class="label"><?= _("Feature not implemented yet") ?></span>
</div>

<div class="form-field muted" id="ldap__otpBackupPasswordsAttribute">
<label><?= _("Attribute for storage of One-Time Backup Passwords (optional)") ?></label>
<input type="text" disabled="DISABLED" class="input-medium" />
<span class="label"><?= _("Feature not implemented yet") ?></span>
</div>


<div class="form-field" id="ldap__digestha1Attribute">
<label><?= _("DigestHA1 Attribute (optional)") ?></label>
<input type="text" class="input-medium" name="ldap__digestha1Attribute" value="<?= f_val($config->ldap->digestha1Attribute); ?>" />
</div>

<div class="form-field" id="ldap__digestRealm">
<label><?= _("DigestHA1 Realm (optional)") ?></label>
<input type="text" class="input-medium" name="ldap__digestRealm" value="<?= f_val($config->ldap->digestRealm); ?>" />
</div>


</fieldset>


<fieldset>
<h3><?= _("LDAP Attributes for storing secondary account information, intended for forgotten password functionality") ?></h3>

<div class="form-field" id="ldap__secondary_accounts__sms">
<label><?= _("Attribute for mobile phone number, for recovery via SMS text message") ?><br/>
<?= _("(Leave blank to disable this password reset method)") ?></label>
<input type="text" class="medium input-text" name="ldap__secondary_accounts__sms" value="<?= f_val($config->ldap->secondary_accounts->sms); ?>" />
</div>

<div class="form-field" id="ldap__secondary_accounts__email">
<label><?= _("Attribute for <strong>secondary</strong> e-mail address") ?><br/>
<?= _("(Leave blank to disable this password reset method)") ?></label>
<input type="text" class="medium input-text" name="ldap__secondary_accounts__email" value="<?= f_val($config->ldap->secondary_accounts->email); ?>" />
</div>

<div class="form-field" id="ldap__secondary_accounts__openid">
<label><?= _("Attribute for OpenID account") ?><br/>
<?= _("(Leave blank to disable this password reset method)") ?></label>
<input type="text" class="medium input-text" name="ldap__secondary_accounts__openid" value="<?= f_val($config->ldap->secondary_accounts->openid); ?>" />
<span class="label"><?= _("Feature not implemented yet") ?></span>
</div>

</fieldset>


<fieldset>
<h3><?= _("LDAP Filters and Administrators") ?></h3>

<div class="form-field" id="ldap__filter__user">
<label><?= _("Filter for finding out actual users.") ?>
    <?= _("The %s will be substituted by the username.") ?>
    <?php

    Arcanum_ViewHelper_Setup::example_accordion('(uid=%s)
(&(uid=%s)(objectclass=Person))'
);
?>
</label>

<input type="text" class="input-xxlarge" name="ldap__filter__user" value="<?= f_val($config->ldap->filter->user); ?>" />
</div>

<div class="form-field" id="ldap__filter__user_receivesms">
<label><?= _("Filter to determine which users are allowed to receive SMS text messages.") ?> <?= _("The %s will be substituted by the username.") ?>
    <?php

Arcanum_ViewHelper_Setup::example_accordion('(objectclass=Person)
(&(objectclass=Person)(eduPersonAffiliation=faculty))');
?>
</label>

<input type="text" class="input-xxlarge" name="ldap__filter__user_receivesms" value="<?= f_val($config->ldap->filter->user_receivesms); ?>" />
</div>


<div class="form-field" id="ldap__filter__admin_password">
<label><?= _("Filter to determine the password administrator(s) of this application. A password administrator can change passwords and lock accounts for everyone in this directory.") ?>
 <?= _("The %s will be substituted by the username.") ?>

<?php

Arcanum_ViewHelper_Setup::example_accordion(
    '(&(uid=%s)(edupersonentitlement=dbadmin))
(&(uid=%s)(objectclass=uoaadmin))
(&(uid=%s)(uid=username))
(&(uid=%s)(|(uid=username1)(uid=username2)))'
    );
    
    ?>

</label>

<input type="text" class="input-xxlarge" name="ldap__filter__admin_password" value="<?= f_val($config->ldap->filter->admin_password); ?>" />
</div>


<div class="form-field" id="ldap__filter__admin_policy">
<label><?= _("Filter to determine the global policy administrator(s) for this application. A policy administrator, in addition to changing users' passwords and locking accounts, can also change the password policy that is in effect (e.g. the maximum password age).") ?>
    <?= _("The %s will be substituted by the username.") ?>

<?php

Arcanum_ViewHelper_Setup::example_accordion('(&(uid=%s)(edupersonentitlement=dbadmin))
(&(uid=%s)(objectclass=uoaadmin))
(&(uid=%s)(uid=username))
(&(uid=%s)(|(uid=username1)(uid=username2)))');
?>
</label>

<input type="text" class="input-xxlarge" name="ldap__filter__admin_policy" value="<?= f_val($config->ldap->filter->admin_policy); ?>" />
</div>

</fieldset>

<?php

$this->display('setup_save_button');

?>

</form>


<?php
if(!$editing_existing) {
?>


<script language="javascript">
    $(function () {
        $('#ldap_test_connection').on('click', function (e) {
            $.post("setup_rpc.php?method=ldap_test_connection", {
                host: $('[name=ldap__host]').val(),
                basedn: $('[name=ldap__basedn]').val(),
                bind: $('[name=ldap__bind]').val(),
                password: $('[name=ldap__password]').val()
            }, function(response) {
                if(response.result == 0) {
                    $('#ldap_test_result').html('<div class="alert alert-error">'+response.error+'</div>');

                } else if(response.result == 1) {
                    $('#ldap_test_result').html('<div class="alert alert-success">Success.</div>');

                } else {
                    $('#ldap_test_result').html('<div class="alert alert-warning">Incorrect response received</div>');
                }
            }, 'json');
            e.preventDefault();
        });
    });
</script>

<?php
}
?>
