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
       <div id="topheader"> 
       		<div class="loginlogo"><img src="images/logo_login.png" /></div>
            <div class="servicetitle"><?= htmlspecialchars($subtitle) ?></div>
   	   </div>
    </div>
       <div id="header">
        <?php
        if(!empty($config->institution_logo)) {
        ?>
         	<div class="logo"><a href="<?= $home ?>"><img src="<?= $config->institution_logo ?>" alt="<?= htmlspecialchars($title) ?>" width="50px"/></a></div>
        <?php
        }
        ?>
        <div class="title"><a href="<?= $home ?>"?><?= htmlspecialchars($title) ?></a></div>
       </div>
   
    <!-- Main-->
    <div id="main">

