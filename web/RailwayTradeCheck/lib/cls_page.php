<?php
    /*
        Function: Page Class
        Date: 2016-07-27
        Author: Shawn Chang
    */
    
    class cls_page {
        private $iDisp_Page_Num = 10; // 顯示頁數
        
        function __construct() {
        }
        
        // 取得分頁的數量
        private function get_page_num($iStart_Page, $iEnd_Page) {
            $iDiff_Num = $iEnd_Page - $iStart_Page; // 分頁差
            if ($iDiff_Num > $this->iDisp_Page_Num) {
                // 因為固定會顯示第 1 頁 / 最末頁，所以分頁數減 1
                $iPage_num = $this->iDisp_Page_Num - 1; 
            } else {
                $iPage_num = $iDiff_Num;
            }
            
            return $iPage_num;
        }
        
        // 印出「...」
        private function print_page_divider() {
            echo ' ... ';
        }
        
        // 顯示分頁
        public function disp_pages($iLast_Page, $iCurrent_Page, $sHref_Link = '', $aParameters = array()) {
            if ($iLast_Page > 0) {
                $iFirst_Page = 1; // 第 1 頁
                $sHttp_Query_String = '';
                if (!empty($aParameters)) {
                    $sHttp_Query_String = '&' . http_build_query($aParameters);
                }
                
                $sPage_Link = ' <a href="' . $sHref_Link . '?P=%s' . $sHttp_Query_String . '">%s</a> ';
                
                if ($iCurrent_Page > 1) {
                    if ($iCurrent_Page > ($this->iDisp_Page_Num + 1)) {
                        // 第 1 頁
                        echo sprintf($sPage_Link, $iFirst_Page, '1');
                        
                        // 「...」
                        $this->print_page_divider();
                    }
                    
                    // 前 n 頁
                    $iBackward_Num = $this->get_page_num($iFirst_Page, $iCurrent_Page);
                    for ($iIdx = 0 ; $iIdx < $iBackward_Num ; $iIdx++) {
                        $iTmp_Page_Num = $iCurrent_Page - ($iBackward_Num - $iIdx);
                        echo sprintf($sPage_Link, $iTmp_Page_Num, $iTmp_Page_Num);
                    }
                }
                
                // 當前頁
                echo ' <span style="font-weight:bold; color:#F00; font-size:20px;">' . $iCurrent_Page . '</span> ';
                
                if ($iCurrent_Page < $iLast_Page) {
                    // 後 n 頁
                    $iForward_Num = $this->get_page_num($iCurrent_Page, $iLast_Page);
                    for ($iIdx = 0 ; $iIdx < $iForward_Num ; $iIdx++) {
                        $iTmp_Page_Num = $iCurrent_Page + $iIdx + 1;
                        echo sprintf($sPage_Link, $iTmp_Page_Num, $iTmp_Page_Num);
                    }
                    
                    if ($iCurrent_Page < ($iLast_Page - $this->iDisp_Page_Num)) {
                        // 「...」
                        $this->print_page_divider();
                        
                        // 最末頁
                        echo sprintf($sPage_Link, $iLast_Page, $iLast_Page);
                    }
                }
                echo '<br />';
            }
        }
    
        // 計算總頁數
        public function get_total_page($iData_Count, $iRows_Per_Page) {
            return ceil($iData_Count / $iRows_Per_Page);
        }
    }
?>