<script language="javascript" type="text/javascript">
<?php
foreach($msgs as $m) {
    if($m['class'] == 'warning' && isset($m['attribute'])) {
?>

    $('#<?= $m["attribute"] ?> input').addClass('inputError').append('<span class="help-inline"><?= $m["msg"] ?></span>');

<?php
    }
}
?>
</script>
