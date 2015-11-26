<div class="msgconnected">
    <?php
    if(isset($login_username)) {
        ?>
        <strong><?= _("Logged in as:") ?></strong> <?= $login_username ?>
        <img src="images/logout.png" alt="<?= _("Logout") ?>" width="14" height="14" /> <a href="signout.php"><?= _("Logout") ?></a> 
        <?php
    }
    ?>
</div>


