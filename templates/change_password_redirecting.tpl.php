<?php
/**
 * Redirecting - intermediate page after changing password
 *
 * @package Arcanum
 * @subpackage templates
 * @version $Id: change_password_redirecting.tpl.php 6014 2013-01-29 12:53:59Z avel $
 */

?>

<div class="row">
    <div class="span6 offset2 alert alert-success"><?= _("Your password has been updated.") ?></div>
</div>

<div class="row">
    <div class="span6 offset2 well">

<?php
if(isset($service)) {
?>

<p><?= sprintf( _("You will be redirected to the login page, where you'll need to login with your new password, in %s seconds."),
    '<span id="timeout"><?= $timeout ?></span>') ?></p>

<p><a href="<?= $redirect_link ?>"><?= _("(Click here if you do not wish to wait.") ?></a></p>

<?php
} else {
?>

<p><?= sprintf( _("You will be redirected to the login page in %s seconds. If you need to make further changes, please login again."),
    '<span id="timeout"><?= $timeout ?></span>') ?></p>

<?php    
}
?>

    </div>
</div>


<?php
if(isset($timeout)) {
?>
<script type="text/javascript">

    var timeout = <?= $timeout ?>;
    var decrement = function() {
        
        if(timeout > 0) {

            setTimeout(function() {
                var content;
                document.getElementById('timeout').innerHTML = timeout;
                timeout--;
                decrement();
            }, 1000);
        }
    };
    decrement();

</script>
<?php
}