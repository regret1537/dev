<?php
    class Disp
    {
       /**
         * Display string content
         * @param  string $content Content
         */
        public static function dispString($content = '')
        {
            echo $content . PHP_EOL;
        }

        /**
         * Display array content
         * @param  array $content Content
         */
        public static function dispArray($content = [])
        {
            self::dispString(print_r($content, true));
        }
    }