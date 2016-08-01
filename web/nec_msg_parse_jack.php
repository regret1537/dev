<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
    # 依斷行區分NEC電文
    $nec_msg = 'TPTE2 C 00096831391560113XXXXXXX453940226XXXXXXX45308292501      01      008781463377369002016/05/16 13:424213334109                 ';
    $nec_msg = str_replace("\r", '', $nec_msg);
    $nec_msg_list = explode("\n", $nec_msg);
    
    foreach ($nec_msg_list as $m_idx => $t_msg) {
        # 解析長度
        $cut_len_list = array(6,2,8,8,8,6,8,6,6,9,7,6,9,7,9,9,9,9,9,9,9,9,9,9,9,9);
        
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