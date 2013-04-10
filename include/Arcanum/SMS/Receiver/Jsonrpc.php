<?php
/**
 * Receiver from GUnet Web Service. It acts as intermediary between an application and an
 * SMS gateway. It uses JSONRPC 2.0 calls for communication
 *
 * Example for testing: 
 * <pre>
 * curl -i -X POST 'http://.../arcanum/api_sms.php' -d '{"jsonrpc":"2.0","method":"incoming_sms_message","params":{"mobile":"777777777","smsc":"34782387","text":"Hello World"},"id":103}'
 * </pre>
 *
 * @package arcanum
 */

class Arcanum_SMS_Receiver_Jsonrpc extends Arcanum_SMS_Receiver implements Arcanum_SMS_Receiver_Interface {

    public function __construct() {
        parent::__construct();
    }

    public function read() {
        $server = new Zend_Json_Server();

        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            throw new Arcanum_SMS_Receiver_InvalidRequestException(); return false;
        }

        $request = $server->getRequest();
        
        if($request->getMethod() != 'incoming_sms_message') {
            throw new Arcanum_SMS_Receiver_InvalidRequestException(); return false;
        }

        $ret = $request->getParams();

        $this->mobile = $ret['mobile'];
        $this->smsc = $ret['smsc'];
        $this->text = $ret['text'];

        if(isset($ret['log_id'])) {
            $this->log_id = $ret['log_id'];
        }

        return;
    }

}
