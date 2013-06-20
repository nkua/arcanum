<?php
/**
 * Change Password - Strength check
 *
 * @package arcanum
 * @version $Id: ajax_handler.php 5892 2012-10-31 11:05:29Z avel $
 */

$initLocation = 'ajax_handler';
require_once('include/init.php');

if(!isset($_GET['operation'])) die();
$operation = $_GET['operation'];

class RpcMethods {
    /**
     * Return password strength check results
     */
    public static function strength_check()
    {
        global $config;

        Arcanum_Session::check();
        $username = $_SESSION['login_username'];
        $password = (!empty($_GET['password']) ? $_GET['password'] : '');
        if(!empty($username) && !empty($password) && strlen($password) > 2) {
            
            // 1) Password Validation & check routines
            $arcanumLdap = new Arcanum_LdapPassword();
            $arcanumLdap->setParameters(array(
                'username' => $username,
                'newpass' => $password,
            ));
            
            $oldpass = Arcanum_Security::readPassword();
            if($oldpass === false) {
                $arcanumLdap->setParameters(array(
                    'proxy' => true,
                    'force_change' => false
                ));
            } else {
                $arcanumLdap->setParameters(array(
                    'oldpass' => $oldpass,
                    'proxy' => false,
                    'force_change' => true,
                ));
            }

            if($arcanumLdap->validatePassword(false) === false) {
                $messages = $arcanumLdap->getMsgs();

                $output = array(
                    'validation',
                    $messages
                );
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode($output);
                exit;
            }


            // 2) Password Strength Check
            include_once('password_strength_check/functions.inc.php');
            include_once('password_strength_check/password_strength_check_collection.class.php');
            include_once('password_strength_check/password_strength_check.class.php');

            $check = new passwordStrengthCheck($config->password_strength_policy->toArray());

            //$check->runTests(array($username, $password), explode(',', $config->password_strength_checks));
            $check->runTests(array($username, $password), true);

            $all_tests = $check->retrieveEnabledTests();
            $failed_tests = $check->retrieveFailedTests();
            $failed_tests_msgs = $check->retrieveFailedTestsMessages();

            $successful_tests_count = $check->successfulTestsCount();
            $enabled_tests_count = $check->enabledTestsCount();

            $output = array(
                'strength',
                $successful_tests_count,
                $enabled_tests_count,
                $failed_tests_msgs,
                $all_tests,
                $failed_tests,
            );
            return json_encode($output);
        }
    }


    public static function password_generator()
    {
        global $config;


        Arcanum_Session::check();

        include_once('lib/password_generator/password_generator.inc.php');
        include_once('lib/password_strength_check/functions.inc.php');
        include_once('lib/password_strength_check/password_strength_check_collection.class.php');
        include_once('lib/password_strength_check/password_strength_check.class.php');

        if(isset($_GET['uid'])) {
            $uid = $_GET['uid'];
        } else {
            $uid = '';
        }

        $check_result = false;
        while($check_result === false) {
            $pw = uoa_generate_password();
            $check = new passwordStrengthCheck($config->password_strength_policy->toArray());
            if(!$check->runTests(array($uid, $pw))) {
                $check_result = true;
            }
        }

        return json_encode($pw);
    }

    public static function password_suggestions()
    {
        global $config;

        Arcanum_Session::check();

        include_once('lib/password_generator/password_generator.inc.php');
        include_once('lib/password_strength_check/functions.inc.php');
        include_once('lib/password_strength_check/password_strength_check_collection.class.php');
        include_once('lib/password_strength_check/password_strength_check.class.php');

        if(isset($_GET['uid'])) {
            $uid = $_GET['uid'];
        } else {
            $uid = '';
        }

        $passwords = array();

        $check_result = false;

        while(sizeof($passwords) < 24) {
            $pw = uoa_generate_password();
            $check = new passwordStrengthCheck($config->password_strength_policy->toArray());
            if(!$check->runTests(array($uid, $pw))) {
                $passwords[] = $pw;
            }
        }

        return json_encode($passwords);
    }



    public static function ldap_search()
    {
        global $config, $isAdmin, $role;

        Arcanum_Session::check();
        if(!$isAdmin) {
            echo json_encode(array('result' => -2));
        }
        $query = (!empty($_POST['query']) ? $_POST['query'] : false);
        $filter = ( (!empty($_POST['filter']) && $role == 'admin_policy' ) ? $_POST['filter'] : false);
        if(!$query && !$filter) {
            echo json_encode(array('result' => -1));
        }
        
        $arcanumLdap = new Arcanum_Ldap();
        $arcanumLdap->connect();

        if($query) {
            $filter = Arcanum_Ldap::constructFilterFromQuery($query);
        }

        $reqAttrs = array_values( array_merge( array('uid', 'cn', 'mail'), $config->admin->summary_attrs->toArray()) );
        $sr = @ldap_search($arcanumLdap->ldap, $config->ldap->basedn, $filter, $reqAttrs);
        if($sr === false) {
            echo json_encode(array('result' => -1));
            exit;
        }
        $entries = ldap_get_entries($arcanumLdap->ldap, $sr);
        if($entries['count'] == 0) {
            echo json_encode(array('result' => 0));
            exit;
        }

        $ret = array();
        for($i=0; $i<$entries['count']; $i++) {
            $tmp = array('uid' => $entries[$i]['uid'][0],
                'cn' => (isset($entries[$i]['cn;lang-el']) ? $entries[$i]['cn;lang-el'][0] : $entries[$i]['cn'][0]),
                'mail' => (isset($entries[$i]['mail']) ? $entries[$i]['mail'][0] : '')
            );
            foreach($reqAttrs as $attr) {
                if(in_array($attr, array('cn','uid','mail'))) continue;
                if(isset($entries[$i][$attr])) {
                    $tmp[$attr] = $entries[$i][$attr][0];
                }
            }

            $ret[] = $tmp;
            unset($tmp);
        }

        return json_encode(array('result' => $entries['count'], 'data' => $ret));
    }


    public static function check_sms_received()
    {
        $username = $_SESSION['login_username'];

        $envStore = new Arcanum_EnvironmentStore();
        $initiated_reset_pw = $envStore->get('initiated_reset_pw');
        if($initiated_reset_pw  === false || !in_array($username, $initiated_reset_pw) ) {
            return json_encode(array('result' => 1));
            exit;

        } else {
            $confirmed_reset_pw = $envStore->get('confirmed_reset_pw');
            if($confirmed_reset_pw  === false) {
                $confirmed_reset_pw = array();
            }
            if($confirmed_reset_pw  === false || !in_array($username, $confirmed_reset_pw) ) {
                return json_encode(array('result' => 1));
                exit;
            }
            return json_encode(array('result' => 3));
        }
    }


    public static function setup_admins_show_current_rows()
    {
        global $t, $config;
        $out = '';
        foreach($config->ldap->restrictfilters as $no => $arr) {
            $t->assign('restrictfilteritem', $arr->toArray() );
            $t->assign('restrictfilterindex', $no);

            $out .= $t->fetch('setup_7_admins_row');

        }
        return json_encode(array('html' => $out));

    }

    public static function setup_admins_create_form_row()
    {
        global $t, $config;

        $t->assign('restrictfilterindex', ( (isset($_POST['newindex']) && is_numeric($_POST['newindex']) ) ? $_POST['newindex'] : 0) );
        $t->assign('restrictfilteritem', array('id'=>'', 'description'=>'', 'adminfilter'=>'', 'apply'=>''));
        $t->assign('new_row', true);

        return json_encode(array('html' => $t->fetch('setup_7_admins_row')));
    }

}


if(method_exists('RpcMethods', $operation)) {
    header("Content-Type: application/json; charset=utf-8");
    echo RpcMethods::$operation();
}


