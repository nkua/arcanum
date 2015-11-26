<?php
/**
 * Change password of other users (admin form).
 *
 * @package arcanum
 * @version $Id: admin.php 5859 2012-10-19 13:07:55Z avel $
 */

$initLocation = 'admin';
require_once('include/init.php');

/**
 * Developer flag to enable LDAP paged results.
 */
define('ENABLE_PAGED_RESULTS', false);

/**
 * Page size for ldap paged results
 */
define('PAGE_SIZE', 400);

$msgs = array();

if(isset($_REQUEST['refresh']) && isset($_SESSION['summaries'])) {
    unset($_SESSION['summaries']);
    session_write_close();
    header("Location: admin.php");
    exit;
}

$arcanumLdap = new Arcanum_LdapPassword();
$ldap = $arcanumLdap->connect();

$f = $config->ldap->filter->user;
if(substr($f, 0, 1) != '(') {
    $f = "($f)";
}
if($restrict) {
    $f = '(&'.$f.$restrict['apply'].')';
}

$summariesDef = array(
    'users' => array(
        'filter' => $f,
        'desc' => _("Users"),
        'group' => 'info',
    ),
    'admins_password' => array(
        'filter' => $config->ldap->filter->admin_password,
        'desc' => _("Administrators who can change users' passwords"),
        'group' => 'admins',
    ),
    'admins_policy' => array(
        'filter' => $config->ldap->filter->admin_policy,
        'desc' => _("Administrations who can change the policy as well"),
        'group' => 'admins',
    ),
    'pwdreset' => array(
        'filter' => '(&'.$f.'(pwdreset=TRUE))',
        'desc' => _("Temporarirly inactive passwords (user must change password)"),
        'group' => 'info',
    ),
    'pwdlocked' => array(
        'filter' => '(&'.$f.'(pwdaccountlockedtime=*))',
        'desc' => _("Locked accounts by administrator (user cannot login)"),
        'group' => 'info',
    ),
    'pwdfailures' => array(
        'filter' => '(&'.$f.'(pwdfailuretime=*))',
        'desc' => _("Accounts with failed login attempts"),
        'group' => 'info',
    ),
    'noguobjectclass' => array(
        'filter' => '(&'.$f.'(!(objectclass=extendedAuthentication)))',
        'desc' => _("Users without the required ObjectClass")." ExtendedAuth",
        'bad' => true,
        'fix' => true,
        'group' => 'manage',
    ),
    'nopmobjectclass' => array(
        'filter' => '(&'.$f.'(!(objectclass=pwdmanagement)))',
        'desc' => _("Users without the required ObjectClass")." pwdManagement",
        'bad' => true,
        'fix' => true,
        'group' => 'manage',
    ),
    'nopass' => array(
        'filter' => '(&'.$f.'(!(userpassword=*)))',
        'desc' => _("Users with no password filled in"),
        'bad' => true,
        'group' => 'manage',
    ),
);

if(!empty($config->ldap->sambaNtAttribute)) {
    $summariesDef['nonthash'] = array(
        'filter' => '(&'.$f.'(!('.$config->ldap->sambaNtAttribute.'=*)))',
        'desc' => _("Users with no NT hash filled in"),
        'bad' => true,
        'fix' => true,
        'group' => 'manage',
    );
}

if(!empty($config->ldap->digestha1Attribute)) {
    $summariesDef['nodigestha1'] = array(
        'filter' => '(&'.$f.'(!('.$config->ldap->digestha1Attribute.'=*)))',
        'desc' => _("Users with no digest HA1 filled in"),
        'bad' => true,
        'fix' => true,
        'group' => 'manage',
    );
}

if(!empty($config->ldap->ctpAttribute)) {
    $summariesDef['noctp'] = array(
        'filter' => '(&'.$f.'(!('.$config->ldap->ctpAttribute.'=*)))',
        'desc' => _("Users with no &ldquo;CTP&rdquo; filled in"),
        'bad' => true,
        'fix' => true,
        'group' => 'manage',
    );
}

foreach($config->ldap->secondary_accounts->toArray() as $method=>$ldapattr) {

    if($ldapattr) {
        $summariesDef['secondaryaccount_'.$method] = array(
            'filter' => '(&'.$f.'('.$ldapattr['attribute'].'='.$ldapattr['prefix'].'*))',
            'desc' => sprintf( _("Users with secondary account information filled in, for password recovery &mdash; method: %s"), $method),
            'group' => 'secondaryaccounts',
        );
    }
}

// end definitions

$flow = 'summary';
foreach($summariesDef as $k=>$s) {
    if(isset($_GET['show_'.$k])) {
        $flow = 'userlist';
        $filter = sprintf($s['filter'], '*');
        $t->assign('userlist_title', $s['desc']);
        break;
    } elseif(isset($s['fix']) && isset($_POST['fix_'.$k])) {

        $flow = 'fix';
        $fix = $k;
    }
}

if($flow == 'summary') {

    $summaries = array();

    if(isset($_SESSION['summaries'])) {
        $summaries = $_SESSION['summaries'];
    } else {
        foreach($summariesDef as $k=>$s) {
            $plusmark = '';

            if(function_exists('ldap_control_paged_result') && ENABLE_PAGED_RESULTS) {
                $sr = ldap_search($ldap, $config->ldap->basedn, sprintf($s['filter'], '*'), array('uid'));
            } else {
                $sr = @ldap_search($ldap, $config->ldap->basedn, sprintf($s['filter'], '*'), array('uid'), 0, 1000);
                if(in_array(ldap_errno($ldap), array(4, 11))) {
                    $plusmark = '+';
                }
            }
            $entries = ldap_get_entries($ldap, $sr);
            //Arcanum_Ldap::sanitize_entry_array($entries);

            $summaries[$k] = $entries['count'].$plusmark;
        }
        $_SESSION['summaries'] = $summaries;
    }

    $t->assign('summariesDef', $summariesDef);
    $t->assign('summaries', $summaries);

} elseif($flow == 'userlist') {

    if(function_exists('ldap_control_paged_result') && ENABLE_PAGED_RESULTS) {
        if(isset($_GET['p']) && isset($_GET['currentpage'])) {
            $pagination = json_decode(urldecode($_GET['p']));
            $currentpage = $_GET['currentpage'];
            $currentcookie = base64_decode($currentpage); 

            //$cookie = '';

            // get all! :-/
            $cookie = '';
            $pagination = array();
            do {
                d(' = ldap_control_paged_result, cookie = '.base64_encode($cookie));
                ldap_control_paged_result($ldap, PAGE_SIZE, true, $cookie);
                
                d('cookie = '.base64_encode($cookie));
                $pagination[] = base64_encode($cookie);

                $sr = ldap_search($ldap, $config->ldap->basedn, $filter,
                    array_merge($config->admin->summary_attrs->toArray(), array('objectclass')));
                $num = ldap_count_entries($ldap, $sr);
                d('got '.$num .' entries');
                ldap_control_paged_result_response($ldap, $sr, $cookie);

                if($cookie == $currentcookie) {
                    $entries = ldap_get_entries($ldap, $sr);
                }
                
            } while($cookie !== null && $cookie != '');

            // determine from where to start counting
            // no cookie => 1,
            // first cookie [0] => 1* PAGE_SIZE (e.g.100)
            // second cookie [1] => 2* PAGE_SIZE (e.g. 200)
            for($i=0; $i<$pagination; $i++) {
                if($pagination[$i] == $currentpage) {
                    $startnum = PAGE_SIZE * ($i);
                    break;
                }
            }
            $t->assign('startnum', $startnum);

        } else {
            // initial request, should get all cookies
            $cookie = '';
            $pagination = array();
            $currentpage = '';
            $firstrun = true;
            do {
                d('before: '.$cookie);
                d(' = ldap_control_paged_result, cookie = '.base64_encode($cookie));
                ldap_control_paged_result($ldap, PAGE_SIZE, true, $cookie);
                d('after: '.$cookie);
                
                d('cookie = '.base64_encode($cookie));
                $pagination[] = base64_encode($cookie);

                $sr = ldap_search($ldap, $config->ldap->basedn, $filter,
                    array_merge($config->admin->summary_attrs->toArray(), array('objectclass')));
                $num = ldap_count_entries($ldap, $sr);
                d('got '.$num .' entries');
                
                d('before: '.$cookie);
                ldap_control_paged_result_response($ldap, $sr, $cookie);
                d('after: '.$cookie);

                // also get the initial entries to show:
                if($firstrun === true) {
                    $entries = ldap_get_entries($ldap, $sr);
                    $firstrun = false;
                }

                
            } while($cookie !== null && $cookie != '');

            d($pagination);
        }

        $t->assign('pagination', $pagination);
        $t->assign('currentpage', $currentpage);

    } else {
        $sizelimit_exceeded = false;

        $sr = @ldap_search($ldap, $config->ldap->basedn, $filter, 
            array_merge($config->admin->summary_attrs->toArray(), array('objectclass')));
        if(in_array(ldap_errno($ldap), array(4, 11))) {
            $sizelimit_exceeded = true;
        }
        
        $entries = ldap_get_entries($ldap, $sr);

        if($sizelimit_exceeded) {
            $msgs[] = array('class' => 'warning', 'msg' => sprintf( _("LDAP size limit was exceeded; displaying only the first %s entries"), $entries['count']));
        }
        //Arcanum_Ldap::sanitize_entry_array($entries);
    }
    
    $t->assign('summary_attrs', $config->admin->summary_attrs->toArray());
    $t->assign('entries', $entries);

} elseif($flow == 'fix') {
    // fixing routine for $fix

    if(isset($_SESSION['summaries'])) unset($_SESSION['summaries']);

    switch($fix) {
    case  'noguobjectclass':
        $sr = ldap_search($ldap, $config->ldap->basedn, sprintf($summariesDef[$fix]['filter'], '*'),
            array('objectclass'));

        $entries = ldap_get_entries($ldap, $sr);
        $new_class_value = array();
        $new_class_value['objectclass'][] = 'ExtendedAuthentication';
        $count_fixed = 0;
        for($i=0; $i < $entries['count']; $i++) {

            ldap_mod_add($ldap, $entries[$i]['dn'], $new_class_value);
            $count_fixed++;
        }
        $msgs[] = array('class' => 'success', 'msg' => sprintf( _("The required objectclass (ExtendedAuthentication) was added to %s entries"), $count_fixed));
        break;

    case  'nopmobjectclass':
        $sr = ldap_search($ldap, $config->ldap->basedn, sprintf($summariesDef[$fix]['filter'], '*'),
            array('objectclass'));

        $entries = ldap_get_entries($ldap, $sr);
        $new_class_value = array();
        $new_class_value['objectclass'][] = 'pwdManagement';
        $count_fixed = 0;
        for($i=0; $i < $entries['count']; $i++) {

            ldap_mod_add($ldap, $entries[$i]['dn'], $new_class_value);
            $count_fixed++;
        }
        $msgs[] = array('class' => 'success', 'msg' => sprintf( _("The required objectclass (pwdManagement) was added to %s entries"), $count_fixed));
        break;





    case  'nonthash':
        require_once('include/HashAlgorithm.php');
        require_once('include/HashAlgorithm.NTHash.php');

        $attrs = array('userpassword');
        if(!empty($config->ldap->ctpAttribute)) {
            $attrs[] = $config->ldap->ctpAttribute;
        }
        $sr = ldap_search($ldap, $config->ldap->basedn, sprintf('(&'.$f.'(userpassword=*)(!('.$config->ldap->sambaNtAttribute.'=*)))', '*'), $attrs);
        $entries = ldap_get_entries($ldap, $sr);

        $count_fixed = 0;
        $count_unfixable = 0;
        for($i=0; $i < $entries['count']; $i++) {
            $pw = $arcanumLdap->getCleartextPassword($entries[$i]);
            if($pw === false) {
                $count_unfixable++;
                continue;
            } else {
                $newinfo = array($config->ldap->sambaNtAttribute => HashAlgorithm_NTHash::Generate($pw));
                ldap_modify($ldap, $entries[$i]['dn'], $newinfo);
                $count_fixed++;
            }
        }
        if($count_fixed == 0) {
            if($count_unfixable == 0) {
                $msgs[] = array('class' => 'success', 'msg' => _("No entries in which to add Samba NT Hash") );
            }
        } else {
            $msgs[] = array('class' => 'success', 'msg' => sprintf( _("Samba NT Hash added to %s entries"), $count_fixed));
        }
        if($count_unfixable > 0) {
            $msgs[] = array(
                'class' => 'warning',
                'msg' => sprintf( _("In %s entries the cleartext password cannot be retrieved. To add the %s, the password must be manually resetted, or the user has to change the password."),
                    $count_unfixable, 'Samba NT Hash')
            );

        }
        break;

    case  'nodigestha1':
        require_once('include/HashAlgorithm.php');
        require_once('include/HashAlgorithm.DigestHA1.php');

        $attrs = array('uid', 'userpassword');
        if(!empty($config->ldap->ctpAttribute)) {
            $attrs[] = $config->ldap->ctpAttribute;
        }
        $sr = ldap_search($ldap, $config->ldap->basedn, sprintf('(&'.$f.'(userpassword=*)(!('.$config->ldap->digestha1Attribute.'=*)))', '*'), $attrs);
        $entries = ldap_get_entries($ldap, $sr);

        $count_fixed = 0;
        $count_unfixable = 0;
        for($i=0; $i < $entries['count']; $i++) {
            $pw = $arcanumLdap->getCleartextPassword($entries[$i]);
            if($pw === false) {
                $count_unfixable++;
                continue;
            } else {
                $newinfo = array($config->ldap->digestha1Attribute => HashAlgorithm_DigestHA1::Generate($entries[$i]['uid'][0], $config->ldap->digestRealm, $pw));
                ldap_modify($ldap, $entries[$i]['dn'], $newinfo);
                $count_fixed++;
            }
        }
        if($count_fixed == 0) {
            if($count_unfixable == 0) {
                $msgs[] = array('class' => 'success', 'msg' => _("No entries in which to add the Digest HA1."));
            }
        } else {
            $msgs[] = array('class' => 'success', 'msg' => sprintf( _("Digest HA1 was added to %s entries."), $count_fixed));
        }
        if($count_unfixable > 0) {
            $msgs[] = array(
                'class' => 'warning',
                'msg' => sprintf( _("In %s entries the cleartext password cannot be retrieved. To add the %s, the password must be manually resetted, or the user has to change the password."),
                    $count_unfixable, 'Digest HA1')
            );
        }
        break;

    case  'noctp':
        require_once('include/HashAlgorithm.php');
        require_once('include/HashAlgorithm.DigestHA1.php');

        $attrs = array('uid', 'userpassword', $config->ldap->ctpAttribute);

        $sr = ldap_search($ldap, $config->ldap->basedn, sprintf('(&'.$f.'(userpassword=*)(!('.$config->ldap->ctpAttribute.'=*)))', '*'), $attrs);
        $entries = ldap_get_entries($ldap, $sr);

        $count_fixed = 0;
        $count_unfixable = 0;
        for($i=0; $i < $entries['count']; $i++) {
            $pw = $arcanumLdap->getCleartextPassword($entries[$i]);
            if($pw === false) {
                $count_unfixable++;
                continue;
            } else {
                $newinfo = array($config->ldap->ctpAttribute => $arcanumLdap->getCTP($pw));
                ldap_modify($ldap, $entries[$i]['dn'], $newinfo);
                $count_fixed++;
            }
        }
        if($count_fixed == 0) {
            if($count_unfixable == 0) {
                $msgs[] = array('class' => 'success', 'msg' => _("No entries in which to add the CTP."));
            }
        } else {
            $msgs[] = array('class' => 'success', 'msg' => sprintf( _("CTP was added to %s entries."), $count_fixed));
        }
        if($count_unfixable > 0) {
            $msgs[] = array(
                'class' => 'warning',
                'msg' => sprintf( _("In %s entries the cleartext password cannot be retrieved. To add the %s, the password must be manually resetted, or the user has to change the password."),
                    $count_unfixable, 'Encoded CTP')
            );
        }
        break;

    default:
        break;
    }
}


// ----------- Presentation Logic ---------------


$t->assign('msgs', $msgs);
$t->assign('javascripts', $defaultJavascripts);

$t->display('html_header');
$t->display('page_header_admin');

if($flow == 'summary') {
    $t->display('admin_summary');

} elseif($flow == 'userlist') {
    $t->display('admin_userlist');

} elseif($flow == 'fix') {
    $t->display('admin_fixed_entries');
}

$t->display('page_footer_admin');
$t->display('html_footer');
