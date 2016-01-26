<div class="textlines"> 
   <h2><?= _("Tips to choose a safe password") ?></h2>
   
    <?= _("For your safety, choose a password that you can remember easily but one you cannot find in dictionaries. Passwords that include a number or symbol are better and less likely to get cracked.") ?>
	<br /><br />

    <strong><?= _("Your password must:") ?></strong><br /><br />

    <div class="safety-tip">
        <div class="safety-img"><img src="images/arrow2.png" /></div>
        <div class="safety=text"><?= sprintf( _("Consist of at least %s characters"), $pw_min_len); ?></div>
    </div>
    <div class="safety-tip">
        <div class="safety-img"><img src="images/arrow2.png" /></div>
        <div class="safety=text"><?=sprintf(  _("Contain at least %s numbers or symbols"), $pw_min_nonalpha ); ?></div>
    </div>
    <div class="safety-tip">
        <div class="safety-img"><img src="images/arrow2.png" /></div>
        <div class="safety=text"><?= sprintf( _("Contain at least %s different characters"), $pw_check_min_uniq ); ?></div>
    </div>
    <div class="safety-tip">
        <div class="safety-img"><img src="images/arrow2.png" /></div>
        <div class="safety=text"><?= _("Not be too similar to your username") ?></div>
    </div>
    <div class="safety-tip">
        <div class="safety-img"><img src="images/arrow2.png" /></div>
        <div class="safety=text"><?= sprintf( _("Not contain %s or more successive numbers"), $pw_min_consecutive_numbers ); ?></div>
    </div>
</div>

    

