    <div class="alert-box error">
        <?= _("Attention: Locked Account.") ?>
    </div>

<p>
<?php

    if(!isset($lockedtime) || $lockedtime == '000001010000Z') {
        echo _("Your account has been locked by an administrator");
    } else {
        echo sprintf( _("Your account was locked by an administrator on %s"), Arcanum_LdapAttributeFormatter::formatLdapDate($lockedtime));
    }
?>

<?= _("and cannot be used for authenticating to services any more.") ?>

<p><?= _("You will have to contact your administrator to address this issue.") ?></p>


