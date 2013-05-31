<?php
/**
 * Admin's password change -> results
 *
 * @package Arcanum
 * @subpackage templates
 * @version $Id: admin_change_password_result.tpl.php 5870 2012-10-25 09:05:49Z avel $
 */

?>

<div style="margin: auto; text-align:center;">

<?php
$this->display('messages');
?>
<br/>

    <a href="admin_show_user.php?uid=<?= htmlspecialchars($uid) ?>"><?= sprintf( _("Return to Account Information for user %s"), htmlspecialchars($uid) ) ?></a>

</div>
