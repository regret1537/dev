<?php
    /*
        Function: HTML Class
        Date: 2016-07-27
        Author: Shawn Chang
    */
    include_once('cls_html.php');
    
    class cls_html_form extends cls_html{
        
        // Public function
        
        public function __construct() {
            
        }
        
        // Generate SELECT object
        public function gen_select($sObj_Name, $aObj_Value, $sObj_Selected_Value) {
            $sIs_Selected = '';
            $sHTML = $this->gen_html('    <select name="' . $this->sanitize_xss($sObj_Name) . '">');
            foreach ($aObj_Value as $sVal => $sDesc) {
                if ($sObj_Selected_Value == $sVal) {
                    $sIs_Selected = ' SELECTED=SELECTED';
                }
                $sHTML .= $this->gen_html('        <option value="' . $this->sanitize_xss($sVal) . '"' . $sIs_Selected . '>' . $this->sanitize_xss($sDesc) . '</option>');
            }
            $sHTML .= $this->gen_html('    </select>');
            return $sHTML;
        }
    }
?>