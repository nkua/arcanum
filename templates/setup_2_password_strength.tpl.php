
<form name="arcanumsetupform" class="nice" action="setup.php" method="POST">
<input type="hidden" name="submitstep" value="2_password_strength" />

<fieldset>


<h3><?= _("Password Strength Policy") ?></h3>

<p><?= _("The following settings affect the intensity of the password strength checks. For each setting, set a value of 0 to disable that check entirely.") ?></p>

<div class="form-field" id="password_strength_policy__PW_CHECK_MIN_LEN">
<label><?= _("Minimum length in characters") ?></label>
<input type="number"  class="input input-small" name="password_strength_policy__PW_CHECK_MIN_LEN" value="<?= f_val($config->password_strength_policy->PW_CHECK_MIN_LEN); ?>" />
</div>

<div class="form-field" id="password_strength_policy__PW_CHECK_MIN_UNIQ">
<label><?= _("Minimum unique characters") ?></label>
<input type="number"  class="input input-small" name="password_strength_policy__PW_CHECK_MIN_UNIQ" value="<?= f_val($config->password_strength_policy->PW_CHECK_MIN_UNIQ); ?>" />
</div>

<div class="form-field" id="password_strength_policy__PW_CHECK_MIN_NON_ALPHA">
<label><?= _("Minimum non-alpha characters (symbols or numbers)") ?></label>
<input type="number"  class="input input-small" name="password_strength_policy__PW_CHECK_MIN_NON_ALPHA" value="<?= f_val($config->password_strength_policy->PW_CHECK_MIN_NON_ALPHA); ?>" />
</div>

<div class="form-field" id="password_strength_policy__PW_MIN_CONSECUTIVE_NUMBERS">
<label><?= _("Avoid more than this count of consecutive numbers") ?></label>
<input type="number"  class="input input-small" name="password_strength_policy__PW_MIN_CONSECUTIVE_NUMBERS" value="<?= f_val($config->password_strength_policy->PW_MIN_CONSECUTIVE_NUMBERS); ?>" />
</div>

<div class="form-field" id="password_strength_policy__PW_CHECK_LEVENSHTEIN">
<label><?= _("Similarity with username (Levenenshtein Distance)") ?></label>
<input type="number"  class="input input-small" name="password_strength_policy__PW_CHECK_LEVENSHTEIN" value="<?= f_val($config->password_strength_policy->PW_CHECK_LEVENSHTEIN); ?>" />
</div>


<div class="form-field" id="password_strength_policy__PW_CHECK_MIN_LCS">
<label><?= _("Similarity with username (LCS test)") ?></label>
<input type="number"  class="input input-small" name="password_strength_policy__PW_CHECK_MIN_LCS" value="<?= f_val($config->password_strength_policy->PW_CHECK_MIN_LCS); ?>" />
</div>

</fieldset>

<?php

$this->display('setup_save_button');

?>

</form>
