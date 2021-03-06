<?php
/**
 * Configuration Template
 *
 * @package arcanum
 * @version $Id: config.template.php 5962 2013-01-04 14:03:21Z avel $
 */

return array(
    'institution_name' => '',
    'institution_domain' => '',
    'website_home' => '', 
    'terms_link' => '',
    'privacy_policy_link' => '',
    'ldap'  => array(
        'host' => '',
        'basedn' => '',
        'bind' => '',
        'password' => '',
        'secondary_accounts' => 
            array (
              'sms' => array('attribute'=>'pwdresetmethod', 'prefix'=>'sms:'),
              'email' => array('attribute'=>'pwdresetmethod', 'prefix'=>'email:'),
            ),

        "optinattibute" => 'pwdresetoptin',

        'filter' => array(
            //'user' => 'uid=%s',
            'user' => '(&(uid=%s)(objectclass=*))',
            'user_receivesms' => '(&(uid=*)(pwdresetmethod=*%s)(objectclass=*))',
            //
            // Sample LDAP filter for setting specific users as administrators:
            // 'admin' => '(&(uid=%s)(uid=adminuid))',
            // 'admin' => '(&(uid=%s)(|(uid=adminuid1)(uid=adminuid2)))',
            //
            // Sample LDAP filter for setting users that belong to a certain
            // objectClass as administrators:
            // 'admin' => '(&(uid=%s)(objectclass=adminClass))',
            'admin_password' => '(&(uid=%s)(edupersonentitlement=admin_password))',
            'admin_policy' => '(&(uid=%s)(edupersonentitlement=arcanum_admin))',
        ),

        // 'sunds' or anything else
        'servertype' => '',

        'restrictfilters' => array(
        ),

        // Hash to use for new passwords; either 'crypt', 'sha' or 'ssha'.
        'passwordHash' => 'ssha',

        // false for OpenLDAP module (draft09). true if using the latest draft version
        // of ppolicy spec.
        'pwdpolicydraft10' => false,

        // Attribute to use, to store the main password; usually, 'userPassword'.
        'passwordAttribute' => 'userPassword',

        // Attribute to use, to store the Samba NT hash for Windows Services.
        // If left empty, no samba hash will be generated.
        // Example: 'sambaNtPassword'.
        'sambaNtAttribute' => 'sambaNtPassword',
        
        'actpAttribute' => '',
        
        'otpInitKeyAttribute' => '',
        'otpBackupPasswordsAttribute' => '',
        
        'digestha1Attribute' => 'digestha1',
        'digestRealm' => '',
    ),
    'cas'  => array(
        'host' => '',
        'port' => 443,
        'uri' => '',
    ),
    'session_name' => 'change_password',
    'locale' => array(
        'default_language' => 'el_GR',
    ),
    'title' => 'Password Management Service',
    'subtitle' => 'Organization Name',
    'motd' => '',
    'admin' => array(
        'perform_strength_checks' => true,
        'summary_attrs' => array('cn','uid','mail'),
        'show_attrs' => array('cn','uid','title','mail')
    ),
    'password_strength_policy' => array(
        'PW_CHECK_LEVENSHTEIN' => 2, 
        'PW_CHECK_MIN_LEN' => 6,
        'PW_CHECK_MIN_UNIQ' => 5,
        'PW_CHECK_MIN_LCS' => 40,
        'PW_CHECK_MIN_NON_ALPHA' => 2,
        'PW_MIN_CONSECUTIVE_NUMBERS' => 3,
    ),
	'recaptcha' => array(
		'pubkey' => '',
		'privkey' => '',
	),
    'mail' => array(
        'host' => '',
        'from' => '',
        'fromComment' => '',
        'replyto' => '',
        'smtp' => array(
            'ssl' => 'ssl',
            'port' => 465,
            'auth' => '',
            'username' => '',
            'password' => ''
        ),
    ),
    'sms_operator_number' => '',
    'smsgw' => array(
        'sender' => '',
        'receiver' => '',
        'institution' => '',
        'host' => '',
        'port' => 443,
        'uri' => '/sms',
        'username' => '',
        'password' => '',
        'tout_con' => 10,
        'prefix' => 'MyText',
        'ip_receive' => array(
            '88.197.28.138'
        ),
    ),
    'login_servers' => array(
    ),
    'timezone' => 'Europe/Athens',
    /**
     * Development /debugging options for:
     * 1) SMS messages are not sent; instead a simulated message is written in syslog
     * 2) CAPTCHAs not verified (two words, any two words, must be entered though)
     * 3) The e-mail address specified is Cc'ed in every reset password request by e-mail
     */
    'devel' => array(
        'simulate_sms' => false,
        'allow_all_captcha' => true,
        'email_cc' => '',
    ),
);

