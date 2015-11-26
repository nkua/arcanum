<?php
/**
 * @package arcanum
 * @version $Id: SharedMemory.php 5823 2012-10-02 15:11:31Z avel $
 */

include_once('Zend/Cache.php');
include_once('Zend/Cache/Frontend/Function.php');

/**
 * Base class for all shared memory classes.
 *
 * + Arcanum_SharedMemory
 *     + Arcanum_EnvironmentStore   generic variables
 *     + Arcanum_Token              authentication tokens
 *          + Arcanum_Token_Email   authentication tokens via e-mail
 *          + Arcanum_Token_Sms     authentication tokens via SMS
 *
 */
class Arcanum_SharedMemory {
    protected $store = null;

    /**
     * Return a Zend_Cache instant with specified prefix and lifetime.
     * To be used by backends
     */
    public function init($prefix = '', $lifetime = '') {
        if(!is_string($prefix) || !is_numeric($lifetime)) {
            throw new InsufficientSharedMemoryOptionException();
        }

        $frontend_options = array(
            'lifetime' => $lifetime,
            'cache_id_prefix' => $this->get_global_prefix() . (!empty($prefix) ? $prefix . '_' : ''),
            'caching' => true,
        );

        $this->store = Zend_Cache::factory(
            // Frontend    
            'Function',
            // Backend
            'Xcache',
            // Frontend options
            $frontend_options,
            // Backend options
            array(
                'compression' => false,
                'compatibility' => false,
            )
        );

        $this->clean();
    }
    
    public function get_global_prefix() {
        global $config;
        return str_replace('.', '', $config->institution_domain) . '_';
    }

    /**
     * Generic setter
     */
    public function set($key, $val) {
        if($this->store === null) {
            throw new Arcanum_UnconfiguredSharedMemoryException();
        }
        return $this->store->save($val, $key);
    }
    
    /**
     * Generic getter
     */
    public function get($key) {
        if($this->store === null) {
            throw new Arcanum_UnconfiguredSharedMemoryException();
        }
        return $this->store->load($key);
    }

    /**
     * Clean old entries from the store, according to its lifetime
     */
    public function clean() {
        if($this->store === null) {
            throw new Arcanum_UnconfiguredSharedMemoryException();
        }
        $this->store->clean(Zend_Cache::CLEANING_MODE_OLD);
    }
}

class Arcanum_InsufficientSharedMemoryOptionException extends Exception {}
class Arcanum_UnconfiguredSharedMemoryException extends Exception {}

