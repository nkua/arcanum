<?php
/**
 * Page header and navigation for administration pages
 *
 * @package arcanum
 * @subpackage templates
 * @version $Id: page_header_admin.tpl.php 5954 2012-12-28 10:39:09Z avel $
 */
?>
<div class="navbar navbar-fixed-top">
<div class="navbar-inner">
<div class="container">

<a class="brand" href="admin.php"><?php echo htmlspecialchars($institution_name) ?></a>

<ul class="nav">
<li<?php echo ($initLocation == 'admin' ? ' class="active"' : '' ); ?>><a href="admin.php"><?php echo _("Summary") ?></a></li>
<li<?php echo ($initLocation == 'admin_show_user' || $initLocation == 'admin_change_password' ? ' class="active"' : '' ); ?>><a href="admin_show_user.php"><?php echo _("Users") ?></a></li>

<?php
if($role == 'admin_policy') {
?>
    <li<?php echo ($initLocation == 'admin_set_policies' ? ' class="active"' : '' ); ?>><a href="admin_set_policies.php"><?php echo _("Policy") ?></a></li>
<?php
}
?>
    <li<?php echo ($initLocation == 'admin_notifications' ? ' class="active"' : '' ); ?>><a href="admin_notifications.php"><?php echo _("Notifications") ?></a></li>
    <li<?php echo ($initLocation == 'admin_sessions' ? ' class="active"' : '' ); ?>><a href="admin_sessions.php"><?php echo _("Sessions") ?></a></li>

<?php
if($role == 'admin_policy') {
?>
    <li<?php echo ($initLocation == 'setup' ? ' class="active"' : '' ); ?>><a href="setup.php"><?php echo _("Setup") ?></a></li>
<?php
}
?>

</ul>

<ul class="nav pull-right">
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i> <?php echo $login_username ?> <b class="caret"></b></a>
    <ul class="dropdown-menu">
      <li><a tabindex="-1" href="myaccount.php"><?php echo _("About my Administrative Account") ?></a></li>
      <li><a tabindex="-1" href="admin_change_password.php?uid=<?php echo htmlspecialchars($login_username) ?>"><?php echo _("Change my Password") ?></a></li>
    </ul>
  </li>
  
  <li><a href="signout.php"><i class="icon-off"></i> <?php echo _("Logout") ?></a></li>
</ul>


<div class="pull-right">

  <form name="searchuserform" action="admin_show_user.php" method="GET" class="navbar-search pull-right">
    <input type="text" placeholder="<?php echo _("Search") ?>" name="navquery" size="15" id="searchquery" class="search-query input-small" />
  </form>
  
</div>

</div>
</div>
</div>
        


<div class="container">
