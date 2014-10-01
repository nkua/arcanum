    
<h1><?= _("Password Management Service") ?></h1>

<div class="textlines"> 
   <h2><?= _("Available Functions") ?></h2>

   <?= _("The password management serice allows you to reset your forgotten password or change your password safely.") ?>
   <br /><br />

   <?= _("The following functionality is available:") ?>
   <br /><br />

   <div class="services">

    <div class="home-service">
    	<div class="home-icon"><a href="changepassword.php"><img src="images/changepassword_icon.png" /></div>
        <div class="home-text"><div class="paddingtop"><?= _("Change Password") ?></div></div></a>
    </div>
    <div class="home-service">
    	<div class="home-icon"><a href="reset_password.php"><img src="images/forgotpassword_icon.png"/></div>
        <div class="home-text"><?= _("Reset password in case you have forgotten your original password") ?></div>
    </div></a>
    <div class="home-service">
    	<div class="home-icon"><a href="dataentry.php"><img src="images/dataentry_icon.png" /></div>
        <div class="home-text"><?= _("Register a mobile phone number or a secondary e-mail address, for password recovery") ?></div>
    </div></a>
    
</div>
</div>


