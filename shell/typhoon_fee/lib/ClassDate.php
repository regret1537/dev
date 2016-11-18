<?php
    class ClassDate
    {
        /**
         * Format date
         * @param  string $format Date format
         * @param  string $date   Date
         * @return string         Formated date
         */
        public static function format($format, $date) {
            return date($format, strtotime($date));
        }

        /**
         * Get now date
         * @param  string $format Date format
         * @return string         Formated date
         */
        public static function now($format = 'Y/m/d H:i:s') {
            return date($format);
        }
    }