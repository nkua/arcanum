
<form name="arcanumsetupform" class="nice" action="setup.php" method="POST">
<input type="hidden" name="submitstep" value="1_php" />

<h3><?= _("Basic Information") ?></h3>

<div class="form-field" id="website_home">
<label><?= _("Website Home address URL") ?></label>

<input type="text" <?= ($editing_existing ? 'disabled="DISABLED"' : '') ?> class="input input-xxlarge" name="website_home" value="<?= f_val($config->website_home); ?>" />
</div>


<div class="form-field" id="institution_name">
<label><?= _("Name of Organization") ?></label>
<input type="text" class="input-xlarge" name="institution_name" value="<?= f_val($config->institution_name); ?>" />
</div>

<div class="form-field" id="institution_domain">
<label><?= _("Domain Name of Organization") ?></label>
<input type="text" class="input-xlarge" name="institution_domain" placeholder="institution.gr" value="<?= f_val($config->institution_domain); ?>" />
</div>


<div class="form-field" id="title">
<label><?= _("Page Title (e.g. Truncated Organization Name)") ?></label>
<input type="text" class="input-xlarge" name="title" placeholder="Ίδρυμα" value="<?= f_val($config->title); ?>" />
</div>


<div class="form-field" id="subtitle">
<label><?= _("Page Subtitle (e.g. Description of Service)") ?></label>
<input type="text" class="input-xlarge" name="subtitle" placeholder="<?= _("Password Management Service") ?>" value="<?= f_val($config->subtitle); ?>" />
</div>

<div class="form-field" id="institution_logo">
<label><?= _("Page Logo. Either full URL or relative path to image location. <small>(Optional)</small>") ?></label>
<input type="text" class="input-xlarge" name="institution_logo" placeholder="images/logo.png" value="<?= f_val($config->institution_logo); ?>" />
</div>

<div class="form-field" id="motd">
<label><?= _("Message of the Day") ?><br/>
<?= _("This text will appear in the login page. It is meant for important announcements.") ?></label>
<textarea class="input-xlarge input-block-level" rows="5" name="motd"><?= f_val($config->motd); ?></textarea>
</div>

<div class="form-field" id="terms_link">
<label><?= _("URL of Terms of Service") ?></label>
<input type="text" class="input-xlarge" name="terms_link" placeholder="http://example.org/terms_of_service" value="<?= f_val($config->terms_link); ?>" />
</div>

<div class="form-field" id="privacy_policy_link">
<label><?= _("URL of Privacy Policy") ?></label>
<input type="text" class="input-xlarge" name="privacy_policy_link" placeholder="http://example.org/privacy_policy" value="<?= f_val($config->privacy_policy_link); ?>" />
</div>


<h3><?= _("PHP Settings") ?></h3>

<div class="form-field" id="session_name">
<label><?= _("PHP Session Name (if there are a lot of installations of this application in the same host)") ?>
<br/><small><?php echo ($editing_existing ? _("Note: if you change this attribute, you will have to relogin.") : '') ?></small>
</label>
<input type="text" class="input-xlarge" name="session_name" placeholder="" value="<?= f_val($config->session_name); ?>" />
</div>

<?php

$this->display('setup_save_button');

?>
</form>
