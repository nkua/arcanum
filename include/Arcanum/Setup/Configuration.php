<?php
/**
 *
 * @package arcanum
 * @version $Id: Configuration.php 5895 2012-10-31 13:17:13Z avel $
 */

/**
 * Configuration Object
 */
class Arcanum_Setup_Configuration {
    public $config;

    public $editingExisting = false;

    public $all_config_vars_flat = array(
        'locale__default_language',
        'institution_name', 'institution_domain', 'institution_logo', 'title', 'subtitle', 'motd', 'session_name',
        'website_home', 'ldap__host', 'ldap__basedn',
        'ldap__bind', 'ldap__password',
        'ldap__passwordHash', 'ldap__sambaNtAttribute',
        'ldap__digestha1Attribute', 'ldap__digestRealm',
        'ldap__secondary_accounts__sms', 'ldap__secondary_accounts__email',
        'ldap__secondary_accounts__openid', 'ldap__filter__user', 'ldap__filter__user_receivesms',
        'ldap__filter__admin_password', 'ldap__filter__admin_policy', 'recaptcha__pubkey',
        'ldap__restrictfilters',
        'password_strength_policy__PW_CHECK_LEVENSHTEIN', 'password_strength_policy__PW_CHECK_MIN_LEN',
        'password_strength_policy__PW_CHECK_MIN_UNIQ', 'password_strength_policy__PW_CHECK_MIN_LCS',
        'password_strength_policy__PW_CHECK_MIN_NON_ALPHA', 'password_strength_policy__PW_MIN_CONSECUTIVE_NUMBERS',         'recaptcha__privkey', 'mail__host',
        'mail__from', 'mail__fromComment',
        'mail__replyto', 'mail__smtp__ssl',
        'mail__smtp__port', 'mail__smtp__auth',
        'mail__smtp__username', 'mail__smtp__password',
        // TODO - GUI and method saveArray() for login_servers option
        // 'login_servers',
        'cas__host', 'cas__port', 'cas__uri',
        'sms_operator_number', 'smsgw__sender', 'smsgw__receiver', 'smsgw__institution', 'smsgw__host', 'smsgw__port',
        'smsgw__uri', 'smsgw__username', 'smsgw__password', 'smsgw__prefix'
    );

    /**
     * These variables are not allowed to be edited inside the application;
     * they are only for inital setup
     */
    public $restricted_vars_flat = array(
        'website_home', 'ldap__host', 'ldap__basedn',
        'ldap__bind'
    );
        
    public function spl($str) {
        $parts = preg_split('/__/', $str);
        return $parts;
    }

    public function __construct() {
        if(isset($_SESSION['setupcfg'])) {
            // init from session
            
            $this->editingExisting = $_SESSION['editing_existing_config_file'];

            $this->config = new Zend_Config(require('include/config.template.php'), true);

            foreach($_SESSION['setupcfg'] as $var => $val) {
                $this->_saveInConfigObject($var, $val);
            }

        } elseif(file_exists('config/config.php')) {
            
            // init from existing config file
            $this->editingExisting = $_SESSION['editing_existing_config_file'] =  true;

            $this->config = new Zend_Config(require('config/config.php'), true);

            $_SESSION['setupcfg'] = array();

            foreach($this->all_config_vars_flat as $var){
                $parts = $this->spl($var);
                if(sizeof($parts) == 1) {
                    $$var = $this->config->$var;
                } elseif(sizeof($parts) == 2) {
                    $$var = $this->config->$parts[0]->$parts[1];
                } elseif(sizeof($parts) == 3) {
                    $$var = $this->config->$parts[0]->$parts[1]->$parts[2];
                } elseif(sizeof($parts) == 4) {
                    $$var = $this->config->$parts[0]->$parts[1]->$parts[2]->$parts[3];
                }
                $_SESSION['setupcfg'][$var] = $$var;
            }

        } else {
            // init from template config file
            $this->config = new Zend_Config(require('include/config.template.php'), true);

            $this->editingExisting = $_SESSION['editing_existing_config_file'] = false;
            
            $_SESSION['setupcfg'] = array();
        }
    }

    /**
     * Save a single variable to $this->config and session
     * @param string $var
     * @param mixed $val
     * @return void
     */
    public function saveAttr($var, $val = false) {
        if ($this->editingExisting === true && in_array($var, $this->restricted_vars_flat) ) {
            return;
        }
        if($val === false) {
            if(isset($_POST[$var])) {
                $val = $_POST[$var];
                if($var === 'mail__smtp__ssl') $val = 'ssl';
            } else {
                $val = '';
            }
        }

        // 1) save in session
        $_SESSION['setupcfg'][$var] = $val;

        // 2) save in $this->config, which is used in forms GUI
        $this->_saveInConfigObject($var, $val);
    }

    protected function _saveInConfigObject($var, $val = '') {
        $parts = $this->spl($var);
        
        if(sizeof($parts) == 1) {
            $this->config->$var = $val;
        } elseif(sizeof($parts) == 2) {
            $this->config->$parts[0]->$parts[1] = $val;
        } elseif(sizeof($parts) == 3) {
            $this->config->$parts[0]->$parts[1]->$parts[2] = $val;
        } elseif(sizeof($parts) == 4) {
            $this->config->$parts[0]->$parts[1]->$parts[2]->$parts[3] = $val;
        }
    }

    public static function calculate_basedn_from_domain($domain) {
        $domparts = preg_split('/\./', $domain);
        $basedn = '';
        for($i=0; $i<sizeof($domparts); $i++) {
            $basedn .= 'dc='.$domparts[$i];
            if($i+1 < sizeof($domparts)) $basedn .= ',';
        }
        return $basedn;
    }

    public function write($path = 'config/config.php') {
        $writer = new Zend_Config_Writer_Array();
        $writer->setConfig($this->config)
           ->setFilename($path);

        $writer->write();

        return true;
   }

   public static function disableInstaller() {
        file_put_contents('config/web_installer_disabled.php', 'Delete this file to reenable the web installer (setup.php)');
    }
}
