<?php
/**
 * @package arcanum
 * @version $Id$
 */

class Arcanum_SMS_Sender_Kannel extends Arcanum_SMS_Sender {
    
    public function __construct(&$recipient, &$config) {
        parent::__construct($recipient, $config);
    }

    /**
     * @param string $message Message to send
     * @return mixed Boolean true upon success, or string with error message upon error.
     */
    protected function _sendText($message) {
        $url_pref = $this->config->uri
            . '?username=' . rawurlencode($this->config->username)
            . '&password=' . rawurlencode($this->config->password)
            . '&text=' . rawurlencode($message);
        
        return $this->_sendToKannel($url_pref . '&to=' . rawurlencode($this->recipient));
    }

    /**
     * @param string $url GET URL to request from Kannel
     * @return mixed Boolean true upon success, or string with error message upon error.
     */
    private function _sendToKannel($url) {

        $stat = self::SENDSMS_FAILCONN;
        $fp = fsockopen($this->config->host, $this->config->port, $errno, $errstr, $this->config->tout_con);
        if(!$fp) return 'Connection to Kannel failed';
        
        $out = "GET $url HTTP/1.0\r\n\r\n";

        fwrite($fp, "GET $url HTTP/1.0\r\n\r\n");
        $resp = fgets($fp, 128);
        if($resp) {
            list($proto,$stat,$desc) = preg_split('/ /',$resp,3); // was split ( ' ') ...
        }
        while (!feof($fp)) {
            $resp = fgets($fp, 128);
        }
        fclose($fp);
        if($stat == '200') {
            return true;
        } else {
            return sprintf('Kannel responded with error %s: %s', $stat, $desc);
        }
    }
}
