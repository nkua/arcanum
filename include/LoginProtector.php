<?php
/**
 * @package arcanum
 * @author Alexandros Vellis <avel@noc.uoa.gr>
 * @version $Id: LoginProtector.php 5809 2012-09-20 09:06:12Z avel $
 */

/**
 * Class that measures login tries and allows checking to see if there are multiple
 * failed login attempts.
 */
class LoginProtector extends Arcanum_SharedMemory {

    public function __construct() {
        $this->init('lp', 1800);
    }

    public function get_key() {
        return 'hit_'.sprintf('%u', ip2long($_SERVER['REMOTE_ADDR']));
    }
    
    public function increment_tries() {
        $key = $this->get_key();

        if(($hits = $this->store->load($key)) === false) {
            $this->store->save(1, $key);
        } else {
            $this->store->save(++$hits, $key);
        }
     
        return $hits;
    }

    public function get_tries() {
        $key = $this->get_key();

        if( ($hits = $this->store->load($key)) === false ) {
            return 0;
        }
        return $hits;
    }

    public function reset_tries() {
        $this->store->remove($this->get_key());
    }

    public function clean() {
        $this->store->clean(Zend_Cache::CLEANING_MODE_OLD);
    }
}


