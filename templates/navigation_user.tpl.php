<?php
$this->display('logged_in_and_urgent_msg');
?>
        
<div class="marginline"></div>

<div class="menu">

<ul id="nav">
    <li<? echo ($initLocation == 'home' ? ' class="current"' : '' ); ?>><a href="home.php"><?= _("Home") ?></a></li>
    <li<? echo ($initLocation == 'changepassword' ? ' class="current"' : '' ); ?>><a href="changepassword.php"><?= _("Change Password") ?></a></li>
    <?php
    if(Arcanum_Util::areSecondaryAccountsActive()) {
    ?>
    <li<? echo ($initLocation == 'dataentry' ? ' class="current"' : '' ); ?>><a href="dataentry.php"><?= _("Password Recovery Information") ?></a></li>
    <?php
    }
    ?>
    <li<? echo ($initLocation == 'myaccount' ? ' class="current"' : '' ); ?>><a href="myaccount.php"><?= _("Account Information") ?></a></li>
    <li<? echo ($initLocation == 'safety' ? ' class="current"' : '' ); ?>><a href="safety.php"><?= _("Password Safety") ?></a></li>
</ul>
    
</div>
