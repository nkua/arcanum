<?php
/**
 * Page Header, a place to put organization logos etc.
 *
 * @package arcanum
 * @subpackage templates
 * @version $Id: page_header.tpl.php 5961 2013-01-04 13:10:38Z avel $
 */

global $config;

if($loggedin) {
    if($isAdmin) {
        $home = 'admin.php';
    } else {
        $home = 'myaccount.php';
    }
} else{
    $home = 'index.php';
}
?>
<div id="contentwrapper">
	<!-- Header-->
    <div id="topheaderwrapper">

       <div id="header">
        <?php
        if(!empty($config->institution_logo)) {
        ?>
         	<div class="logo"><a href="<?= $home ?>"><img src="<?= $config->institution_logo ?>" alt="<?= htmlspecialchars($title) ?>"/></a></div>
        <?php
        }
        ?>
        <div class="logotext">
			<div class="title"><?= htmlspecialchars($title) ?></div> 
			<div class="servicetitle"><?= htmlspecialchars($subtitle) ?></div>
        </div>
                    

       </div>


    </div>

   
    <!-- Main-->
    <div id="main">

