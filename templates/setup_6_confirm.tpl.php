
<h3><?= _("Confirm and Save") ?></h3>

<form name="arcanumsetupform" class="nice" action="setup.php" method="POST">
<input type="hidden" name="finish" value="true" />

<p><?= sprintf( _("Your settings will be saved in file %s."), '<tt>'.'config/config.php'.'</tt>') ?></p>

<p><?= _("Afterwards, you will be redirected to the login page where you can log in with an LDAP account.") ?>

<p><?= _("We recommend that you log in with a &ldquo;policy administrator&rdquo; account so that you will continue with the service setup (e.g. setup password policies, define additional password administrators per department, setup users' accounts etc.).") ?>
    </p>

<input type="submit" class="span6 btn btn-primary" value="<?= _("Save configuration File") ?>" />

<br/><br/>
<div class="alert alert-info">
    <?= sprintf( _("From now on, these settings will be accessible from inside the application. Just log in as a password policy administrator and go to Setup tab. You can reenable the installer by removing the file %s."), '<tt>config/web_installer_disabled</tt>') ?>
</div>


</form>

