<?php
/**
 * HTML Headers
 *
 * @package change_password
 * @subpackage templates
 * @version $Id: html_header.tpl.php 5855 2012-10-15 11:26:34Z avel $
 */
// <!DOCTYPE html>
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title><?= $title ?> &mdash; <?= $subtitle ?></title>
	<meta name="viewport" content="width=device-width" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

<?php
    foreach($javascripts as $js) {
        echo '<script type="text/javascript" src="'.$baseuri.'/'.$js.'"></script>'.PHP_EOL;
    }
    foreach($defaultStyles as $css) {
        echo '<link rel="stylesheet" type="text/css" href="'.$baseuri.'/'.$css.'" />'.PHP_EOL;
    }
    foreach($styles as $css) {
        echo '<link rel="stylesheet" type="text/css" href="'.$baseuri.'/'.$css.'" />'.PHP_EOL;
    }
    if(isset($inlinejavascript)) {
        echo '<script type="text/javascript" language="Javascript">
'.$inlinejavascript.'
</script>'.PHP_EOL;
    }

?>
    
<?php
    /*
	<!-- IE Fix for HTML5 Tags -->
	<!--[if lt IE 9]>
		<script src="lib/html5shiv/html5.js"></script>
	<![endif]-->
     */
?>


    <?php echo ( !empty($xtra_head) ? $xtra_head : ''); ?>
</head>
<body>
