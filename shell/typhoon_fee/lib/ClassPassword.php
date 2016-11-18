<?php
    class ClassPassword
    {
        /**
         * Generate random key
         * @param  integer $length Key length
         * @return string          Random key
         */
        public static function randomKey($length = 8){
            $key = '';
            $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for ($i = 0 ; $i < $length ; $i++) {
                $key .= $pattern{rand(0, strlen($pattern) - 1)};
            }
            return $key;
        }
    }