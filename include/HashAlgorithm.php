<?php
/**
 * Interface for hash algorithms
 *
 * @package arcanum
 * @version $Id: HashAlgorithm.php 5730 2012-06-18 08:52:20Z avel $
 */

interface HashAlgorithm {
    public static function Generate($cleartext);
}

