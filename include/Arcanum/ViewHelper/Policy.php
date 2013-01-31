<?php

class Arcanum_ViewHelper_Policy {

    public static function time_form($name, $val = '') {
    ?>
    <div class="form form-inline">
        <input type="text" name="dur_val_<?= $name ?>" id="dur_val_<?= $name ?>" value="<?= htmlspecialchars($val) ?>" class="input-small" size="5" style="display: inline;" />

        <select name="dur_unit_<?= $name ?>" id="dur_unit_<?= $name ?>" style="display: inline;" class="input-small" >
        <option value="seconds"><?= _("Seconds") ?></option>
        <option value="minutes"><?= _("Minutes") ?></option>
        <option value="hours"><?= _("Hours") ?></option>
        <option value="days"><?= _("Days") ?></option>
        <option value="months"><?= _("Months") ?></option>
        </select>
    </div>
    <?php
    }
}

?>
