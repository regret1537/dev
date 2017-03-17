<?php
    /*
        Function: Java Script Class
        Date:     2016-07-27
        Author:   Shawn Chang
    */
    
    include_once('cls_html.php');
    
    class cls_js extends cls_html{
        
        // Public function
        
        public function __construct() {
            
        }
        // JavaScript ´£¥Ü + ¾É­¶
        public function alert($sMsg, $sLocation = '') {
            echo $this->gen_html('    <script language="JavaScript">');
            echo $this->gen_html('        alert("' . $this->sanitize_xss($sMsg) . '");');
            if (!empty($sLocation)) {
                echo $this->gen_html('        location.href="' . $this->sanitize_xss($sLocation) . '";');
            }
            echo $this->gen_html('    </script>');
        }
        
        // Private function
    }
?>