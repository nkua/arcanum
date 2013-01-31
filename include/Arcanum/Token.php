<?php
/**
 * @package arcanum
 * @version $Id: Token.php 5823 2012-10-02 15:11:31Z avel $
 */

class Arcanum_Token extends Arcanum_SharedMemory {
    /**
     * Set token key & value based on uid
     *
     * Lookup by uid:
     * uid => token
     *
     * @param string $token
     * @param string $uid
     * @return mixed
     */
    public function set_token($token, $uid) {
        if(($hits = $this->store->load($token)) === false) {
            $this->store->save($uid, $token);
        } else {
            return false;
        }
     
        return $hits;
    }
    
    public function get_token($token) {
        if( ($r = $this->store->load($token)) === false ) {
            return false;
        }
        return $r;
    }

    public function delete_token($token) {
        $this->store->remove($token);
    }
}

