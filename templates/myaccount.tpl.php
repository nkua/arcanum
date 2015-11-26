<?php
/**
 * @package arcanum
 * @version $Id: myaccount.tpl.php 5961 2013-01-04 13:10:38Z avel $
 */
?>
<h1><?= _("Account Information") ?></h1>

    <div id="left-home">
        <div class="introtext">


<?php
// ==================================================================
//
// Active | Inactive Account Information
//
// ------------------------------------------------------------------


if(isset($expires_in) && $expires_in > 0) {
    print '<div class="green">'. sprintf( _("Active account. Password expires in %s."), time_duration($expires_in, null)) . '</div><br/>';

} elseif(!empty($entry['pwdreset']) && $entry['pwdreset'][0] == 'TRUE') {
    print '<div class="red">'. _("Warning: Inactive Password") . '</div>&nbsp;
    ' . _("The current password can only be used to enter a new, personalized password. Once you change your password, you will be able to log in to services again.");

} elseif(!empty($entry['pwdaccountlockedtime']) && !empty($entry['pwdaccountlockedtime'][0])) {
    // normally we don't reach this point...
    echo '<div class="red">';
    $this->display('account_locked');
    echo '</div>';
    
} elseif(isset($possibly_expired_password)) {
    echo '<div class="red">'. _("Warning: Inactive Account.") . '</div>&nbsp;
            ' . _("Your current password cannot be used to authenticate you to services, probably due to having expired.") . ' 
            ' . _("Please change your password immediately.");

} elseif(isset($entry['pwdgraceusetime'])) {
    echo '<div class="red">'. _("Warning: Password has expired and must be changed!") . '</div>&nbsp;
            '. _("Your current password has expired and you must change it immediately. If you don't change it, it won't be possible to authenticate you to services.") .'
        ';

} else {
    echo '<div class="green">'. _("Active Account.") . '</div>';
}

?>


<br/><br/><br/>


<?php

// ==================================================================
//
// Failed Login Attempts
//
// ------------------------------------------------------------------


if(!empty($entry['pwdfailuretime'])) {
    echo '<h2>' . _("Failed Login Attempts") . '</h2>';

    echo '<p>' . _("The following failed login attempts (incorrect or expired password) have been recorded:") . '</p>';

    echo '<table class="table table-striped">
        <thead><tr><td>' . _("#") . '</td><td>' . _("Date &amp; Time") . '</td></tr></thead>
        <tbody>';

    for($i=0; $i < $entry['pwdfailuretime']['count']; $i++) {
        echo '<tr><td>'.($i+1).'</td><td>'.Arcanum_LdapAttributeFormatter::formatLdapDate($entry['pwdfailuretime'][$i]).'</td></tr>';
    }
    echo '</tbody></table>';

    echo '<p>'. _("Note: the failed login attempt history is deleted immediately after the first successful login.") . '</p>';
}


?>


<p>
 <?= sprintf( _("To change your password, click %s"),
    sprintf('<a href="changepassword.php">%s</a>', _("Change Password") ) ) ?>
</p>



</div>
</div>
