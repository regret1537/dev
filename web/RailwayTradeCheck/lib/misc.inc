<?php
    class misc {
        function __construct() {
        }
        
        public function fmtMsg($cont) {
            $cont_type = gettype($cont);
            $fmt_type = array('array', 'object');
            if (in_array($cont_type, $fmt_type)) {
                return print_r($cont, true);
            } else {
                return $cont;
            }
        }
        
        public function rndKey($length){
            $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for($i=0 ; $i < $length ; $i++) {
                $key .= $pattern{rand(0, strlen($pattern) - 1)};
            }
            return $key;
        }
        
        public function isEmpty($var_name) {
            if (!isset($var_name) or empty ($var_name)) {
                return true;
            } else {
                return false;
            }
        }
    }
?>