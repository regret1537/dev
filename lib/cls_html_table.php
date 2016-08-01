<?php
    /*
        Function: HTML Table Class
        Date: 2016-07-27
        Author: Shawn Chang
    */
    
    include_once('cls_html.php');
    
    class cls_html_table extends cls_html{
        
        // Public function
        
        public function __construct() {
            
        }
        
        // Generate table
        public function gen_table($sTitle, $aColumn_Name, $aData, $iStart_SN = 1) {
            $sHTML = $this->gen_html('    <table style="border-collapse:collapse;width:95%;">');
            
            if (!empty($sTitle)) {
                // Set the table title
                $sHTML .= $this->gen_html('        <tr>');
                $sHTML .= $this->gen_html('            <th colspan="' . (count($aColumn_Name) + 1) . '" style="border:1px solid #888;text-align:center;padding:5px;background-color:#FEC;">');
                $sHTML .= $this->gen_html('                ' . $this->sanitize_xss($sTitle));
                $sHTML .= $this->gen_html('            </th>');
                $sHTML .= $this->gen_html('        </tr>');
            }
            
            // Set the column names
            $sHTML .= $this->gen_html('        <tr>');
            $sHTML .= $this->gen_html('            <td style="width:5%;border:1px solid #888;text-align:center;background-color:#EEE;padding:5px;">');
            $sHTML .= $this->gen_html('                SN');
            $sHTML .= $this->gen_html('            </td>');
            foreach ($aColumn_Name as $sTmp_Info) {
                $sHTML .= $this->gen_html('            <td style="width:' . $this->sanitize_xss($sTmp_Info['width']) . '%;border:1px solid #888;text-align:center;background-color:#EEE;padding:5px;">');
                $sHTML .= $this->gen_html('                ' . $this->sanitize_xss($sTmp_Info['desc']));
                $sHTML .= $this->gen_html('            </td>');
            }
            $sHTML .= $this->gen_html('        </tr>');
            
            // Set the contents
            foreach ($aData as $iIdx => $aTmp_Row) {
                $sHTML .= $this->gen_html('        <tr>');
                $sHTML .= $this->gen_html('            <td style="border:1px solid #888;text-align:center;padding:5px;">');
                $sHTML .= $this->gen_html('                ' . ($iStart_SN + $iIdx));
                $sHTML .= $this->gen_html('            </td>');
                foreach ($aTmp_Row as $sTmp_Content) {
                    $sHTML .= $this->gen_html('            <td style="border:1px solid #888;text-align:center;padding:5px;">');
                    $sHTML .= $this->gen_html('                ' . $this->sanitize_xss($sTmp_Content));
                    $sHTML .= $this->gen_html('            </td>');
                }
                $sHTML .= $this->gen_html('        </tr>');
            }
            
            $sHTML .= $this->gen_html('    </table>');
            $sHTML .= $this->gen_html('    <br />');
            
            return $sHTML;
        }
    }
?>