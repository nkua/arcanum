<?php
/**
 * NTHash
 *
 * @package change_password
 * @version $Id: HashAlgorithm.NTHash.php 5591 2012-03-09 12:47:40Z avel $
 */

/**
 * NTHash class
 */
class HashAlgorithm_NTHash implements HashAlgorithm {
    static function rhex($num) {
        $hex_chr = "0123456789abcdef";
        $str = "";
        for($j = 0; $j <= 3; $j++)
            $str .= $hex_chr{($num >> ($j * 8 + 4)) & 0x0F} .
                    $hex_chr{($num >> ($j * 8)) & 0x0F};
        return $str;
    }
    static function str2blks($str) {
        $nblk = ((strlen($str) + 8) >> 6) + 1;
        for($i = 0; $i < $nblk * 16; $i++) $blks[$i] = 0;
        for($i = 0; $i < strlen($str); $i++)
            $blks[$i >> 2] |= ord($str{$i}) << (($i % 4) * 8);
        $blks[$i >> 2] |= 0x80 << (($i % 4) * 8);
        $blks[$nblk * 16 - 2] = strlen($str) * 8;
        return $blks;
    }
    static function safe_add($x, $y) {
        $lsw = ($x & 0xFFFF) + ($y & 0xFFFF);
        $msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);
        return ($msw << 16) | ($lsw & 0xFFFF);
    }
    static function zeroFill($a, $b) {
        $z = (int)0x80000000;
        if ($z & $a) {
            $a >>= 1;
            $a &= (~$z);
            $a |= 0x40000000;
            $a >>= ($b-1);
        } else {
            $a >>= $b;
        }
        return $a;
    }
    static function rol($num, $cnt) {
        return ($num << $cnt) | (self::zeroFill($num, (32 - $cnt)));
    }
    static function cmn($q, $a, $b, $x, $s, $t) {
        return self::safe_add(self::rol(self::safe_add(self::safe_add($a, $q), self::safe_add($x, $t)), $s), $b);
    }
    static function ffMD4($a, $b, $c, $d, $x, $s) {
        return self::cmn(($b & $c) | ((~(int)$b) & $d), $a, 0, $x, $s, 0);
    }
    static function ggMD4($a, $b, $c, $d, $x, $s) {
        return self::cmn(($b & $c) | ($b & $d) | ($c & $d), $a, 0, $x, $s, 1518500249);
    }
    static function hhMD4($a, $b, $c, $d, $x, $s) {
        return self::cmn($b ^ $c ^ $d, $a, 0, $x, $s, 1859775393);
    }

    static function md4_php($str) {
        $x = self::str2blks($str);

        $a =  1732584193;
        $b = -271733879;
        $c = -1732584194;
        $d =  271733878;

        for($i = 0; $i < count($x); $i += 16) {
            $olda = $a;
            $oldb = $b;
            $oldc = $c;
            $oldd = $d;

            $a = self::ffMD4($a, $b, $c, $d, $x[$i+ 0], 3 );
            $d = self::ffMD4($d, $a, $b, $c, $x[$i+ 1], 7 );
            $c = self::ffMD4($c, $d, $a, $b, $x[$i+ 2], 11);
            $b = self::ffMD4($b, $c, $d, $a, $x[$i+ 3], 19);
            $a = self::ffMD4($a, $b, $c, $d, $x[$i+ 4], 3 );
            $d = self::ffMD4($d, $a, $b, $c, $x[$i+ 5], 7 );
            $c = self::ffMD4($c, $d, $a, $b, $x[$i+ 6], 11);
            $b = self::ffMD4($b, $c, $d, $a, $x[$i+ 7], 19);
            $a = self::ffMD4($a, $b, $c, $d, $x[$i+ 8], 3 );
            $d = self::ffMD4($d, $a, $b, $c, $x[$i+ 9], 7 );
            $c = self::ffMD4($c, $d, $a, $b, $x[$i+10], 11);
            $b = self::ffMD4($b, $c, $d, $a, $x[$i+11], 19);
            $a = self::ffMD4($a, $b, $c, $d, $x[$i+12], 3 );
            $d = self::ffMD4($d, $a, $b, $c, $x[$i+13], 7 );
            $c = self::ffMD4($c, $d, $a, $b, $x[$i+14], 11);
            $b = self::ffMD4($b, $c, $d, $a, $x[$i+15], 19);

            $a = self::ggMD4($a, $b, $c, $d, $x[$i+ 0], 3 );
            $d = self::ggMD4($d, $a, $b, $c, $x[$i+ 4], 5 );
            $c = self::ggMD4($c, $d, $a, $b, $x[$i+ 8], 9 );
            $b = self::ggMD4($b, $c, $d, $a, $x[$i+12], 13);
            $a = self::ggMD4($a, $b, $c, $d, $x[$i+ 1], 3 );
            $d = self::ggMD4($d, $a, $b, $c, $x[$i+ 5], 5 );
            $c = self::ggMD4($c, $d, $a, $b, $x[$i+ 9], 9 );
            $b = self::ggMD4($b, $c, $d, $a, $x[$i+13], 13);
            $a = self::ggMD4($a, $b, $c, $d, $x[$i+ 2], 3 );
            $d = self::ggMD4($d, $a, $b, $c, $x[$i+ 6], 5 );
            $c = self::ggMD4($c, $d, $a, $b, $x[$i+10], 9 );
            $b = self::ggMD4($b, $c, $d, $a, $x[$i+14], 13);
            $a = self::ggMD4($a, $b, $c, $d, $x[$i+ 3], 3 );
            $d = self::ggMD4($d, $a, $b, $c, $x[$i+ 7], 5 );
            $c = self::ggMD4($c, $d, $a, $b, $x[$i+11], 9 );
            $b = self::ggMD4($b, $c, $d, $a, $x[$i+15], 13);

            $a = self::hhMD4($a, $b, $c, $d, $x[$i+ 0], 3 );
            $d = self::hhMD4($d, $a, $b, $c, $x[$i+ 8], 9 );
            $c = self::hhMD4($c, $d, $a, $b, $x[$i+ 4], 11);
            $b = self::hhMD4($b, $c, $d, $a, $x[$i+12], 15);
            $a = self::hhMD4($a, $b, $c, $d, $x[$i+ 2], 3 );
            $d = self::hhMD4($d, $a, $b, $c, $x[$i+10], 9 );
            $c = self::hhMD4($c, $d, $a, $b, $x[$i+ 6], 11);
            $b = self::hhMD4($b, $c, $d, $a, $x[$i+14], 15);
            $a = self::hhMD4($a, $b, $c, $d, $x[$i+ 1], 3 );
            $d = self::hhMD4($d, $a, $b, $c, $x[$i+ 9], 9 );
            $c = self::hhMD4($c, $d, $a, $b, $x[$i+ 5], 11);
            $b = self::hhMD4($b, $c, $d, $a, $x[$i+13], 15);
            $a = self::hhMD4($a, $b, $c, $d, $x[$i+ 3], 3 );
            $d = self::hhMD4($d, $a, $b, $c, $x[$i+11], 9 );
            $c = self::hhMD4($c, $d, $a, $b, $x[$i+ 7], 11);
            $b = self::hhMD4($b, $c, $d, $a, $x[$i+15], 15);

            $a = self::safe_add($a, $olda);
            $b = self::safe_add($b, $oldb);
            $c = self::safe_add($c, $oldc);
            $d = self::safe_add($d, $oldd);
        }
        return self::rhex($a) . self::rhex($b) . self::rhex($c) . self::rhex($d);
    }
    
    static function md4($str) {
        if(function_exists('mhash')) {
            // using mhash module
            return bin2hex(mhash(MHASH_MD4, $str));

        } elseif((int)0x80000000 == -2147483648) {
            // Fallback for 32-bit systems only.
            return self::md4_php($str);

        } else {
            // Fail miserably
            die('The mhash extension is needed for samba NT hash generation to work.');
        }
    }

    static function str2unicode($str) {
        $uni = '';
        $str = (string) $str;
        for ($i = 0; $i < strlen($str); $i++) {
            $a = ord($str{$i}) << 8;
            $uni .= sprintf("%X", $a);
        }
        return pack('H*', $uni);
    }

    public static function Generate($cleartext) {
        $cleartext = substr(isset($cleartext)?$cleartext:"",0,128); /* trim to 128 bytes */
        return(strtoupper(self::md4(self::str2unicode($cleartext))));
    }
}

/* vim:set et ts=4: */
