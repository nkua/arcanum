<?php
/**
 * Template for massive change results
 */

?>

<div class="row">
	<div class="span8">

        <h2><?= _("Massive Changes to LDAP Users") ?></h2>

<p><?= sprintf( _("The results of the massive changes to be applied to %s users are:" ), $counters['total']) ?> </p> 


<?php

if ($counters['modsuccess'] > 0) {    
    echo '<p class="alert alert-success">'. sprintf( _("%s entries have been successfully modified.") , $counters['modsuccess']).'</p>';
}


if ($counters['delnochange'] > 0) {
    echo '<p class="alert alert-info">'. sprintf( _("%s entries have remained the same (no need to remove attribute that did not exist") , $counters['delnochange']). '</p>';
}


if ($counters['delsuccess'] > 0) {
    echo '<p class="alert alert-success">'. sprintf( _("%s entries have been succesfully modified (attribute deletion).") , $counters['delsuccess']).' </p>';
}


if ($counters['error'] > 0) {
    echo '<p class="alert alert-error">'. sprintf( _("%s entries have failed to modify due to LDAP error.") , $counters['error']).' </p>';
}

?>



<a href="admin_show_user.php"> <?= _("Back to initial search users page") ?></a>

</div>
</div>