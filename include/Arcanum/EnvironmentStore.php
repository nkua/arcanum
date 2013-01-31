<?php
/**
 * @package arcanum
 * @version $Id: EnvironmentStore.php 5823 2012-10-02 15:11:31Z avel $
 */

class Arcanum_EnvironmentStore extends Arcanum_SharedMemory {
    /**
     * Initialize a token store with prefix 'env', lifetime 6 hours
     */
    public function __construct() {
        global $config;
        $this->init('env', 6*3600);
    }
}

