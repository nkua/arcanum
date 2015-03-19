<?php
/**
 * Setup / installation routine
 *
 * TODO: 
 * support these config attributes:
 *  - login_servers (array)
 *  - devel-> array(
 *       'simulate_sms' => true,
 *       'allow_all_captcha' => true,
 *       'email_cc' => 'avel@noc.uoa.gr',
 *   ),
 *  - otp options
 *
 * @package Arcanum
 * @version $Id: setup.php 6045 2013-01-31 10:17:41Z avel $
 */

$initLocation = 'setup';

if(isset($_GET['setlanguage'])) {
    $language = $_GET['setlanguage'];
}

require_once 'include/init.php';
require_once 'include/Template.class.php';
require_once 'include/misc.php';

if(isset($_GET['destroy_session'])) {
    session_destroy();
    header("Location: setup.php");
    exit;
}

if(!$loggedin && file_exists('config/config.php') && file_exists('config/web_installer_disabled.php')) {
    @session_destroy();
    header("Location: setup.php");
    exit;
}

$t->assign('defaultStyles', $defaultAdminStyles);
$t->assign('javascripts', $defaultJavascripts);
$t->assign('styles', array());

if(isset($_GET['setlanguage'])) {
    $_SESSION['language'] = $language;
} elseif(isset($_SESSION['language'])) {
    $language = $_SESSION['language'];
    setup_locale();
}


// ==================================================================
//
// Main Routine
//
// ------------------------------------------------------------------

$cfg = new Arcanum_Setup_Configuration();

$operations = array(
    '0_checklist' => _("Checklist"),
    '1_php' => _("Basic Setup"),
    '2_config' => _("LDAP Connection"),
    '2_password_strength' => _("Password Strength Policy"),
    '3_recaptcha' => _("CAPTCHA Setup"),
    '4_mail' => _("E-mail Dispatch"),
    '5_smsgw' => _("SMS Gateway"),
);

if(!$cfg->editingExisting) {
    $operations['6_confirm'] =  _("Confirm Configuration");
} else {
    $operations['7_admins'] = _("Password Administrators");
}

$ops = array_keys($operations);

$msgs = array();


if(isset($_GET['operation']) && isset($operations[$_GET['operation']])) {
    $operation = $_GET['operation'];
} elseif($cfg->editingExisting && isset($_POST['submitstep']) && in_array($_POST['submitstep'], $ops)) {
    $operation = $_POST['submitstep'];
} else {
    $operation = '0_checklist';
}



if(isset($_POST['submitstep']) && in_array($_POST['submitstep'], $ops)) {
    $validation_succeeded = true;
    $validateStep = $_POST['submitstep'];

    switch($validateStep) {
    case '1_php':
        $cfg->saveAttr('institution_name');
        $cfg->saveAttr('institution_domain');
        $cfg->saveAttr('institution_logo');
        $cfg->saveAttr('title');
        $cfg->saveAttr('subtitle');
        $cfg->saveAttr('session_name');
        $cfg->saveAttr('motd');

        $cfg->saveAttr('terms_link');
        $cfg->saveAttr('privacy_policy_link');
        $cfg->saveAttr('website_home');
        
        // auto-generate suggested basedn, bind and website_home from 'domain', if they are not set:
        if(!$cfg->editingExisting && empty($cfg->config->ldap->basedn)) {
            $basedn = Arcanum_Setup_Configuration::calculate_basedn_from_domain($cfg->config->institution_domain);
            $bind = "cn=Manager,$basedn";

            $cfg->saveAttr('ldap__basedn', $basedn);
            $cfg->saveAttr('ldap__bind', $bind);
            $cfg->saveAttr('website_home', 'https://accounts.'.$cfg->config->institution_domain);
        }

        //$msgs[] = array('class' => 'warning', 'msg=> 'Errormsg', 'attribute' => 'institution_domain');
        break;

    case '2_config':
        if(!$cfg->editingExisting) {
            $cfg->saveAttr('ldap__host');
            $cfg->saveAttr('ldap__basedn');
            $cfg->saveAttr('ldap__bind');
            $cfg->saveAttr('ldap__password');
        }
        $cfg->saveAttr('ldap__passwordHash');
        $cfg->saveAttr('ldap__sambaNtAttribute');
        //$cfg->saveAttr('ldap__ctpAttribute');
        //$cfg->saveAttr('ldap__ctpKey');
        $cfg->saveAttr('ldap__digestha1Attribute');
        $cfg->saveAttr('ldap__digestRealm');
        $cfg->saveAttr('ldap__secondary_accounts__sms__attribute');
        $cfg->saveAttr('ldap__secondary_accounts__sms__prefix');
        $cfg->saveAttr('ldap__secondary_accounts__email__attribute');
        $cfg->saveAttr('ldap__secondary_accounts__email__prefix');
        
        $cfg->saveAttr('ldap__filter__user');
        $cfg->saveAttr('ldap__filter__user_receivesms');
        $cfg->saveAttr('ldap__filter__admin_password');
        $cfg->saveAttr('ldap__filter__admin_policy');
        // TODO
        //$cfg->saveAttr('login_servers');
        $cfg->saveAttr('cas__host');
        $cfg->saveAttr('cas__port');
        $cfg->saveAttr('cas__uri');
        //$msgs[] = array(class=>'warning', 'msg' => 'Δοκιμή', 'attribute' => 'ldap__host');
        //print_r($config->ldap->host);
        break;
    
    case '2_password_strength':
        $cfg->saveAttr('password_strength_policy__PW_CHECK_LEVENSHTEIN');
        $cfg->saveAttr('password_strength_policy__PW_CHECK_MIN_LEN');
        $cfg->saveAttr('password_strength_policy__PW_CHECK_MIN_UNIQ');
        $cfg->saveAttr('password_strength_policy__PW_CHECK_MIN_LCS');
        $cfg->saveAttr('password_strength_policy__PW_CHECK_MIN_NON_ALPHA');
        $cfg->saveAttr('password_strength_policy__PW_MIN_CONSECUTIVE_NUMBERS');
        break;

    case '3_recaptcha':
        $cfg->saveAttr('recaptcha__pubkey');
        $cfg->saveAttr('recaptcha__privkey');
        break;

    case '4_mail':
        $cfg->saveAttr('mail__host');
        $cfg->saveAttr('mail__from');
        $cfg->saveAttr('mail__fromComment');
        $cfg->saveAttr('mail__replyto');
        $cfg->saveAttr('mail__smtp__ssl');
        $cfg->saveAttr('mail__smtp__port');
        $cfg->saveAttr('mail__smtp__auth');
        $cfg->saveAttr('mail__smtp__username');
        $cfg->saveAttr('mail__smtp_password');
        break;

    case '5_smsgw':
        $cfg->saveAttr('sms_operator_number');
        $cfg->saveAttr('smsgw__sender');
        $cfg->saveAttr('smsgw__receiver');
        $cfg->saveAttr('smsgw__institution');
        $cfg->saveAttr('smsgw__host');
        $cfg->saveAttr('smsgw__port');
        $cfg->saveAttr('smsgw__uri');
        $cfg->saveAttr('smsgw__username');
        $cfg->saveAttr('smsgw__password');
        $cfg->saveAttr('smsgw__prefix');
        break;
    
    case '7_admins':
        
        if(isset($_POST['save'])) {
            $admins = $_POST['admins_new'];

            // validation
            foreach($admins as $no => $adm) {
                foreach($adm as $k => $v) {
                    // Validation takes place here
                }
            }
            // placeholder
            $validation_succeeded = true;
            if($validation_succeeded === true) {
                $flag_save_admins = true;
            }
        }

        if(isset($_POST['delete'])) {
            $tmp = array_keys($_POST['delete']);
            $id_to_delete = $tmp[0];

            // get admins from session, not from form, because user might not want 
            // to save their changes
            $admins = $_SESSION['setupcfg']['ldap__restrictfilters'];

            foreach($admins as $no => $adm) {
                if($adm['id'] == $id_to_delete) {
                    $index_to_delete = $no;
                    break;
                }
            }
            
            if(isset($index_to_delete)) {
                unset($admins[$index_to_delete]);
                // reorder array properly
                $admins = array_values($admins);
                $flag_save_admins = true;
            }

        }

        if(isset($flag_save_admins)) {
                $_SESSION['setupcfg']['ldap__restrictfilters'] = $admins;
                $cfg->config->ldap->restrictfilters = $admins;
            }

        
        break;
    }
    if($validation_succeeded) {
        if($cfg->editingExisting) {
            if($cfg->write() === true) {
                $msgs[] = array('class' => 'success', 'msg'=> _("Configuration file saved successfully"));
            } else {
                $msgs[] = array('class' => 'error', 'msg' => _("Error while saving"));
            }
        } else {
            // Move on to next operation
            
            foreach($ops as $i=>$o) {
                if($o == $validateStep) {
                    if(empty($msgs['warning'])) {
                        // if validation successful, move on!
                        $operation = $ops[$i+1];
                    } else {
                        // stay here
                        $operation = $ops[$i];
                    }
                    break;
                }
            }

        }
    } else {
    }

} elseif(isset($_GET['step']) && isset($operations[$_GET['step']])) {
    $operation = $_GET['step'];
}

if(isset($_POST['finish']) && $_POST['finish'] == 'true') {
    $cfg->write();
    $cfg->disableInstaller();
    header("Location: index.php");
    exit;
}

if(isset($_POST['disable_installer'])) {
    file_put_contents('config/web_installer_disabled.php', 'Delete this file to reenable the web installer (setup.php)');
    $msgs['warning'] = array('msg' =>
            sprintf( _("Web Installer has been deactivated. %sContinue by logging in to the application.</a>"), '<a href="index.php">') .
            '<br/>' . _("(To reactivate the web installer, delete the file config/web_installer_disabled.php)"),
            'class' => 'info'
        );

    $t->assign('msgs', $msgs);

    $t->display('html_header');
    $t->display('alert_messages');
    $t->display('html_footer');
    exit;
}

if($operation == '7_admins') {
    $t->assign('javascripts',
        array_merge( $defaultJavascripts, array('javascripts/setup_admins.js' ) ) );
}

// ============================================================

switch($operation) {
case '0_checklist':
default: 
    // TODO: does this ever run?
    if(!function_exists('bindtextdomain')) {
        $msgs[] = array('class' => 'error', 'msg' => _("PHP module &quot;gettext&quot; is required. Edit the file <tt>/etc/php5/apache2/php.ini</tt> and add the line <pre>extension=gettext.so</pre>"));
    }

    if(!function_exists('ldap_search')) {
        $msgs[] = array('class' => 'error', 'msg' => sprintf( _("PHP module &quot;%s&quot; is required; Run (as root): %s"),
            'ldap', '<pre># apt-get install php5-ldap</pre>') );
    }
    
    if(!function_exists('curl_version')) {
        $msgs[] = array('class' => 'error', 'msg' => sprintf( _("PHP module &quot;%s&quot; is required; Run (as root): %s"),
            'curl', '<pre># apt-get install php5-curl</pre>') );
    }
    
    if(!function_exists('xcache_get')) {
        $msgs[] = array('class' => 'error', 'msg' => sprintf( _("PHP module &quot;%s&quot; is required; Run (as root): %s"),
            'xcache', '<pre># apt-get install php5-xcache</pre>') .
            '<br/>'.
            sprintf( _("Then, make sure variable cache is enabled in file %s:"), '<tt>/etc/php5/conf.d/xcache.ini</tt>') .
'<pre>
xcache.var_size  =            64M
xcache.var_count =             1
xcache.var_slots =            8K
</pre>'
        );
    }


    if(file_exists('config/config.php')) {
        if(!is_writable('config/config.php')) {
            $msgs[] = array('class' => 'error', 'msg' => sprintf( _("Cannot save configuration file due to file permissions.. Run (as root): %s"), '<pre># chown www-data config config/config.php</pre>') );
        }
    } else {
        if(!is_writable('config')) {
            $msgs[] = array('class' => 'error', 'msg' => sprintf( _("Cannot save configuration file due to file permissions.. Run (as root): %s"), '<pre># chown www-data config</pre>') );
        }
    }


    $dummy = _("The cron job for sending password reminders has not been installed.");
/* TODO - reenable sometime
    $cron = shell_exec('crontab -l');
    if(!preg_match('/password_expiry_reminder.php/', $cron)) {
        $msgs[] = array('class' => 'warning', 'msg' => 
            _("The cron job for sending password reminders has not been installed.")
            'Δεν έχει εγκατασταθεί το cron job για την υπενθύμιση των χρηστών σχετικά με κωδικούς που λήγουν.');
        
    }
    */
    break;
}

$t->assign('msgs', $msgs);
$t->assign('config', $cfg->config);
$t->assign('operation', $operation);
$t->assign('editing_existing', $cfg->editingExisting);

$t->display('setup');

