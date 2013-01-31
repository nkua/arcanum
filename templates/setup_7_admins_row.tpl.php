<?php
/** 
 * Required variables:
 * restrictfilteritem: array with indexes ...
 * restrictfilterindex: integer
 */
?>

<div class="well well-small">
   <fieldset class="form-horizontal setup_admins_fieldset" id="admins_fieldset_<?= $restrictfilterindex ?>">

    <div class="control-group">
        <label class="control-label" for="admins_id_<?= $restrictfilterindex ?>">
            <?= _("Identifier") ?>
        </label>
        
        <div class="controls">

            <input type="text" class="input-medium" name="admins_new[<?= $restrictfilterindex ?>][id]"
            id="admins_id_<?= $restrictfilterindex ?>" value="<?= htmlspecialchars($restrictfilteritem['id']) ?>"
            placeholder="id" />
        </div>

    </div>

    <div class="control-group">
        <label class="control-label" for="admins_description_<?= $restrictfilterindex ?>">
            <?= _("Description") ?>
        </label>

        <div class="controls">

            <input type="text" class="input-xxlarge" name="admins_new[<?= $restrictfilterindex ?>][description]"
            id="admins_description_<?= $restrictfilterindex ?>" value="<?= htmlspecialchars($restrictfilteritem['description']) ?>"
            placeholder="Department Name or Filter Description" />
        </div>

    </div>
    
    <div class="control-group">
        <label class="control-label" for="admins_adminfilter_<?= $restrictfilterindex ?>">
            <?= _("Administrator Filter") ?>
        </label>

        <div class="controls">

            <input type="text" class="input-xxlarge" name="admins_new[<?= $restrictfilterindex ?>][adminfilter]"
            id="admins_adminfilter_<?= $restrictfilterindex ?>" value="<?= htmlspecialchars($restrictfilteritem['adminfilter']) ?>"
            placeholder="(uid=adminusername)" />
        </div>

    </div>

    <div class="control-group">
        <label class="control-label" for="admins_apply_<?= $restrictfilterindex ?>">
            <?= _("Apply to Users Filter") ?>
        </label>
        <div class="controls">

            <input type="text" class="input-xxlarge" name="admins_new[<?= $restrictfilterindex ?>][apply]"
            id="admins_apply_<?= $restrictfilterindex ?>" value="<?= htmlspecialchars($restrictfilteritem['apply']) ?>"
            placeholder="(department=DepartmentId)" />
        </div>

    </div>

    <?php
    if(!isset($new_row)) {
        ?>  
        <div class="control-group">
            <div class="pull-right">
                <input type="submit" name="delete[<?= $restrictfilteritem['id'] ?>]" class="btn btn-danger" value="<?= _("Delete") ?>" />
            </div>
        </div>

        <?php
    }
    ?>
</fieldset>

</div>