<?php
/**
 * Receiver from Kannel SMS Gateway
 *
 * Example for testing: 
 * <pre>
 * curl 'http://../arcanum/api_sms.php?phone=%2b30123456789&smsc=699999999&text=GUPASS'
 * </pre>
 *
 * @package arcanum
 */

class Arcanum_SMS_Receiver_Kannel extends Arcanum_SMS_Receiver implements Arcanum_SMS_Receiver_Interface {

    public function __construct() {
        parent::__construct();
    }

    public function read() {
        if(empty($_GET['phone']) || empty($_GET['smsc'])) {
            throw new Arcanum_SMS_Receiver_InvalidRequestException(); return false;
        }

        $this->mobile = $_GET['phone'];
        $this->smsc = $_GET['smsc'];
        $this->text = !empty($_GET['text']) ? $_GET['text'] : '';

        return;
    }

    /**
     * Echo response via HTTP Response. Currently not used here.
     */
    protected function _reply($reply_message) {
        global $config;

        if(isset($config->smsgw->greek_sms) && $config->smsgw->greek_sms === true) {
            $m = recode('UTF-8..ISO-8859-7', $reply_message);
            $maxlength = 160;
        } else {
            // assume us-ascii, but could be UTF-8. TODO - utf-8 detection.
            $maxlength = 160;
            $m = $reply_message;
        }
        
        $m = substr($m, 0, $maxlength);

        header("Content-Type: text/plain");
        header("Content-Length: " . strlen($m) );
        echo $m;
    }

}

