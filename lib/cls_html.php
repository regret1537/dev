<?php
    /*
        Function: HTML Class
        Date: 2016-07-27
        Author: Shawn Chang
    */
    
    class cls_html {
        const NL = "\n";
        
        // Public function
        
        public function __construct() {
            
        }
        
        // Generate HTML
        public function gen_html($sHTML) {
            return $sHTML . self::NL;
        }
        
        // Sanitize the content display on Web
        public function sanitize_xss($sContent) {
            $sEncode = mb_detect_encoding($sContent);
            if (!$sEncode) {
                $sEncode = 'BIG5';
            }
            return htmlentities($sContent, ENT_QUOTES | ENT_HTML401, $sEncode, true);
        }
    }
?>