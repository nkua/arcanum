<?php
/**
 * Dispatch of SMS Change password requests.
 *
 * @author Stefanos Stamatis <stef@noc.uoa.gr>
 * @package arcanum
 * @version $Id: SMSDispatch.php 5809 2012-09-20 09:06:12Z avel $
 */

/** Component: SMS service definitions */
include_once('sms_service_def/sms_service_def.inc.php');

class Arcanum_SMSDispatch {
    /**
     * Dispatcher function
     * 
     * According to $status, it logs the transaction using syslog and sends
     * the reply text back to the client. Never returns!
     */
    public static function send($status,$smsc="",$phone="",$msg=array(),$uid="",$pw="") {
        global $config;

        switch($status) {
            case SMS_INVALID_REQUEST:
                $sls=LOG_CRIT;
                $slm=sprintf("Invalid request by IP %s.",$_SERVER['REMOTE_ADDR']);
                $replmsg = '';
                break;

            case SMS_SMSC_DENIED:
                $sls=LOG_ERR;
                $slm=sprintf("Request for %s by SMSC %s ignored. (SMSC blacklisted)",$phone,$smsc);
                $replmsg = '';
                break;

            case SMS_SMSC_NOT_ALLOWED:
                $sls=LOG_ERR;
                $slm=sprintf("Request for %s by SMSC %s ignored. (SMSC Not allowed)",$phone,$smsc);
                $replmsg="";
                break;

            case SMS_LDAP_CONNECT:
                $sls=LOG_ALERT;
                $slm=sprintf("LDAP Connect failed. Request for %s by SMSC %s denied.",$phone,$smsc);
                $replmsg = _("Could not accomodate your request due to a technical issue. Please try again later.");
                break;

            case SMS_LDAP_BIND:
                $sls=LOG_ALERT;
                $slm=sprintf("LDAP Bind failed. Request for %s by SMSC %s denied.",$phone,$smsc);
                $replmsg = _("Could not accomodate your request due to a technical issue. Please try again later.");
                break;

            case SMS_LDAP_SEARCH:
                $sls=LOG_ALERT;
                $slm=sprintf("LDAP Search failed. Request for %s by SMSC %s denied.",$phone,$smsc);
                $replmsg = _("Could not accomodate your request due to a technical issue. Please try again later.");
                break;

            case SMSCHP_NOT_FOUND:
                $sls=LOG_WARNING;
                $slm=sprintf("No UIDs found for %s. Request by SMSC %s denied.",$phone,$smsc);
                // don't really reply anything to the user; they shouldn't have sent the sms in the first place
                // without having started a session first!
                // $replmsg = sprintf( _("No account found for your mobile. Register your mobile at %s"), substr($config->website_home, 7) );
                // for gettext
                $replmsg = '';
                $dummy = sprintf( _("No account found for your mobile. Register your mobile at %s"), substr($config->website_home, 7) );
                break;

            case SMSCHP_FAIL:
                $sls=LOG_ALERT;
                $slm=sprintf("Cannot set password for '%s' to '%s'. Request from %s by SMSC %s denied. Msgs: %s",
                    $uid, $pw, $phone, $smsc, join(",",$msg)
                );
                $replmsg = sprintf( _("Failed to change the password of your account %s. Please try again later."), $uid);
                break;

            case SMSCHP_OK:
                $sls=LOG_NOTICE;
                $slm=sprintf("Generated password reset token for '%s'. Request from %s by SMSC %s.",
                    $uid, $phone, $smsc
                );
                $replmsg = sprintf( _("Please use the number %s"), $pw);
                break;

            case SMSCHP_LIST:
                $sls=LOG_INFO;
                $slm=sprintf("Multiple UIDs for %s by SMSC %s. List: %s", $phone, $smsc, join(",",$msg) );

                $replmsgpart = '';
                foreach($msg as $k => $v) {
                    if($k>0) $replmsgpart .= ",\n";
                    $replmsgpart .= sprintf( _("%s for %s"),  strtoupper($config->smsgw->prefix) . ($k+1),  $v);
                }
                $replmsg = sprintf( _("You have %s accounts; send %s"), count($msg), $replmsgpart);
                break;
        }

        // write to syslog
        openlog('arcanum', LOG_ODELAY, LOG_AUTH);
        syslog($sls, $slm);
        closelog();
        
        // write to accounting db
        if($status == SMSCHP_OK) {
            $respMsg = str_replace($pw, '********', $replmsg);
        } else {
            $respMsg = $replmsg;
        }

        self::echoResponse($replmsg);
        exit();
    }
    
    /**
     * Echo response appropriately for sms gateway
     */
    public static function echoResponse($msg) {
        global $config;
        if(isset($config->smsgw->greek_sms) && $config->smsgw->greek_sms === true) {
            $m = recode('UTF-8..ISO-8859-7', $msg);
            $maxlength = 160;
        } else {
            // assume us-ascii, but could be UTF-8. TODO - utf-8 detection.
            $maxlength = 160;
            $m = $msg;
        }
        
        $m = substr($m, 0, $maxlength);

        header("Content-Type: text/plain");
        header("Content-Length: " . strlen($m) );
        echo $m;
    }

}

