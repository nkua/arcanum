<?php
$this->display('logged_in_and_urgent_msg');
?>
        
<div class="marginline"></div>

<div class="menu">

<ul id="nav">
    <li<?php echo ($initLocation == 'home' ? ' class="current"' : '' ); ?>><a href="home.php"><?php echo _("Home") ?></a></li>
    <li<?php echo ($initLocation == 'changepassword' ? ' class="current"' : '' ); ?>><a href="changepassword.php"><?php echo _("Change Password") ?></a></li>
    <?php
    if(Arcanum_Util::areSecondaryAccountsActive()) {
    ?>
    <li<?php echo ($initLocation == 'dataentry' ? ' class="current"' : '' ); ?>><a href="dataentry.php"><?php echo _("Password Recovery Information") ?></a></li>
    <?php
    }
    ?>
    <li<?php echo ($initLocation == 'myaccount' ? ' class="current"' : '' ); ?>><a href="myaccount.php"><?php echo _("Account Information") ?></a></li>
    <li<?php echo ($initLocation == 'safety' ? ' class="current"' : '' ); ?>><a href="safety.php"><?php echo _("Password Safety") ?></a></li>
</ul>
    
</div>
