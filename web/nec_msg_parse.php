<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
    # 依斷行區分NEC電文
    // $nec_msg = '20160509201605090000102016050923595900811100331762500000000004640001842070012469005725000696557002408056000012663000006454000012288000016517000000223000000044000000000000000000000000000000000000';
    $nec_msg = '20160603201606030000402016060323595901216200541151200000000006780002610520021550008207001026297004080343000018658000010935000008448000031292000000154000000084000000480000003600000000009000000010
 
 ';
    $nec_msg = str_replace("\r", '', $nec_msg);
    $nec_msg_list = explode("\n", $nec_msg);
    
    foreach ($nec_msg_list as $m_idx => $t_msg) {
        if ($m_idx == 0) {
            # Header 解析長度
            $cut_len_list = array(8,8,6,8,6,6,9,7,6,9,7,6,9,9,9,9,9,9,9,9,9,9,9,9);
        } else {
            # Body 解析長度
            $cut_len_list = array(12,9,1,1,10,6,1,10,6,1,1,1,1,5,2,5,1,1,5,2,5,1,1,5,2,5,1,1,5,2,5,1,1,5,2,5,1,1,5,2,5,1,1,5,2,5,1,1,5,2,5,16,1,3,1,3,1,3,1,3);
        }
        
        $s_idx = 0;
        foreach ($cut_len_list as $t_len) {                
            $t_str = substr($t_msg, $s_idx , $t_len);
            disp($t_str);
            $s_idx += $t_len;
        }
        disp('');
    }
    
    include('../tpl/footer.php');
?>