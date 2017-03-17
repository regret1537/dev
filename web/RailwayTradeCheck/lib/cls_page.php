<?php
    /*
        Function: Page Class
        Date: 2016-07-27
        Author: Shawn Chang
    */
    
    class cls_page {
        private $iDisp_Page_Num = 10; // ��ܭ���
        
        function __construct() {
        }
        
        // ���o�������ƶq
        private function get_page_num($iStart_Page, $iEnd_Page) {
            $iDiff_Num = $iEnd_Page - $iStart_Page; // �����t
            if ($iDiff_Num > $this->iDisp_Page_Num) {
                // �]���T�w�|��ܲ� 1 �� / �̥����A�ҥH�����ƴ� 1
                $iPage_num = $this->iDisp_Page_Num - 1; 
            } else {
                $iPage_num = $iDiff_Num;
            }
            
            return $iPage_num;
        }
        
        // �L�X�u...�v
        private function print_page_divider() {
            echo ' ... ';
        }
        
        // ��ܤ���
        public function disp_pages($iLast_Page, $iCurrent_Page, $sHref_Link = '', $aParameters = array()) {
            if ($iLast_Page > 0) {
                $iFirst_Page = 1; // �� 1 ��
                $sHttp_Query_String = '';
                if (!empty($aParameters)) {
                    $sHttp_Query_String = '&' . http_build_query($aParameters);
                }
                
                $sPage_Link = ' <a href="' . $sHref_Link . '?P=%s' . $sHttp_Query_String . '">%s</a> ';
                
                if ($iCurrent_Page > 1) {
                    if ($iCurrent_Page > ($this->iDisp_Page_Num + 1)) {
                        // �� 1 ��
                        echo sprintf($sPage_Link, $iFirst_Page, '1');
                        
                        // �u...�v
                        $this->print_page_divider();
                    }
                    
                    // �e n ��
                    $iBackward_Num = $this->get_page_num($iFirst_Page, $iCurrent_Page);
                    for ($iIdx = 0 ; $iIdx < $iBackward_Num ; $iIdx++) {
                        $iTmp_Page_Num = $iCurrent_Page - ($iBackward_Num - $iIdx);
                        echo sprintf($sPage_Link, $iTmp_Page_Num, $iTmp_Page_Num);
                    }
                }
                
                // ��e��
                echo ' <span style="font-weight:bold; color:#F00; font-size:20px;">' . $iCurrent_Page . '</span> ';
                
                if ($iCurrent_Page < $iLast_Page) {
                    // �� n ��
                    $iForward_Num = $this->get_page_num($iCurrent_Page, $iLast_Page);
                    for ($iIdx = 0 ; $iIdx < $iForward_Num ; $iIdx++) {
                        $iTmp_Page_Num = $iCurrent_Page + $iIdx + 1;
                        echo sprintf($sPage_Link, $iTmp_Page_Num, $iTmp_Page_Num);
                    }
                    
                    if ($iCurrent_Page < ($iLast_Page - $this->iDisp_Page_Num)) {
                        // �u...�v
                        $this->print_page_divider();
                        
                        // �̥���
                        echo sprintf($sPage_Link, $iLast_Page, $iLast_Page);
                    }
                }
                echo '<br />';
            }
        }
    
        // �p���`����
        public function get_total_page($iData_Count, $iRows_Per_Page) {
            return ceil($iData_Count / $iRows_Per_Page);
        }
    }
?>