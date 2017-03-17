<?php
    include('tpl/header.php');
    include('lib/html_common.inc');
    include('lib/misc.inc');
    
    // define('CHECK_OPTION', 'PAY');
    define('CHECK_OPTION', 'BOTH');
    
    function sum_nec_data($src, $key_idx, $amt_idx, $trd_type_idx) {
        $t_pay_sum = 0;
        $t_refund_sum = 0;
        
        $t_sum = array();
        for ($r_i = 1 ; $r_i < count($src) ; $r_i++) {
            $t_ary = explode(',', trim($src[$r_i]));
            $t_card_no = trim($t_ary[$key_idx]);
            $t_trd_type = trim($t_ary[$trd_type_idx]);
            $t_amt = trim($t_ary[$amt_idx]) + 0;
            if (!empty($t_card_no) and !empty($t_amt)) {
                switch ($t_trd_type) {
                    case '5':
                        $t_amt = 0 - $t_amt;
                        break;
                    case '1':
                        break;
                    default:
                }
                $t_trans_card_no = '';
                
                switch (CHECK_OPTION) {
                    case 'BOTH':
                         // 購退票都檢查
                        if ($t_amt > 0) {
                            $t_pay_sum += $t_amt;
                            $t_trans_card_no = substr($t_card_no, 0, 2) . substr($t_card_no, -4) . '******' . substr($t_card_no, 2, 4);
                        } else {
                            $t_refund_sum += $t_amt;
                            $t_trans_card_no = substr($t_card_no, 0, 6) . '******' . substr($t_card_no, -4);
                        }
                        if (!isset($t_sum[$t_trans_card_no])) {
                            $t_sum[$t_trans_card_no] = $t_amt;
                        } else {
                            $t_sum[$t_trans_card_no] += $t_amt;
                        }
                        break;
                    case 'PAY':
                        // 只檢查購票
                        if ($t_amt > 0) {
                            $t_pay_sum += $t_amt;
                            $t_trans_card_no = substr($t_card_no, 0, 2) . substr($t_card_no, -4) . '******' . substr($t_card_no, 2, 4);
                            if (!isset($t_sum[$t_trans_card_no])) {
                                $t_sum[$t_trans_card_no] = $t_amt;
                            } else {
                                $t_sum[$t_trans_card_no] += $t_amt;
                            }
                        }
                        break;
                    default:
                }
            }
        }
        disp('NEC pay:'.$t_pay_sum.', refund:'. $t_refund_sum);
        return $t_sum;
    }
    
    function sum_gw_data($src, $key_idx, $amt_idx) {
        $t_pay_sum = 0;
        $t_refund_sum = 0;
        
        $t_sum = array();
        for ($r_i = 1 ; $r_i < count($src) ; $r_i++) {
            $t_ary = explode(',', trim($src[$r_i]));
            $t_key = trim($t_ary[$key_idx]);
            $t_amt = trim($t_ary[$amt_idx]) + 0;
            if (!empty($t_key) and !empty($t_amt)) {
                switch (CHECK_OPTION) {
                    case 'BOTH':
                        // 購退票都檢查
                        if ($t_amt > 0) {
                            $t_pay_sum += $t_amt;
                        } else {
                            $t_refund_sum += $t_amt;
                        }
                        if (!isset($t_sum[$t_key])) {
                            $t_sum[$t_key] = $t_amt;
                        } else {
                            $t_sum[$t_key] += $t_amt;
                        }
                        break;
                    case 'PAY':
                        // 只檢查購票
                        if ($t_amt > 0) {
                            $t_pay_sum += $t_amt;
                            if (!isset($t_sum[$t_key])) {
                                $t_sum[$t_key] = $t_amt;
                            } else {
                                $t_sum[$t_key] += $t_amt;
                            }
                        }
                        break;
                    default:
                }
            }
        }
        disp('綠界 pay:'.$t_pay_sum.', refund:'. $t_refund_sum);
        return $t_sum;
    }
    
    $chk_dir_path = 'file';
    $diff_pay_total = 0; // 付款差異總額
    $diff_refund_total = 0; // 退款差異總額
    
    // 台鐵交易
    $nec_trd = array();
    $nec_raw = file($chk_dir_path . '/nec.csv');
    
    // 比對
    // $nec_tck_no_idx = 3;
    $nec_card_no_idx = 5;
    $nec_amt_idx = 4;
    $nec_trd_type_idx = 6;
    $nec_trd = sum_nec_data($nec_raw, $nec_card_no_idx, $nec_amt_idx, $nec_trd_type_idx);
    ksort($nec_trd);
    
    // 綠界交易
    $gw_raw = file($chk_dir_path . '/gw.csv');
    $gw_trd = array();
    
    // 比對
    // $gw_tck_no_idx = 2;
    $gw_card_no_idx = 18;
    $gw_amt_idx = 17;
    $gw_authsr_idx = 19;
    $gw_trd = sum_gw_data($gw_raw, $gw_card_no_idx, $gw_amt_idx);
    $diff_count = 0;
    ksort($gw_trd);
    
    $src_trd = array();
    $comp_trd = array();
    $src_desc = '';
    $comp_desc = '';
    if (count($gw_trd) > count($nec_trd)) {
        $src_trd = $gw_trd;
        $comp_trd = $nec_trd;
        $src_desc = '綠界';
        $comp_desc = 'NEC';
    } else {
        $src_trd = $nec_trd;
        $comp_trd = $gw_trd;
        $src_desc = 'NEC';
        $comp_desc = '綠界';
    }
    

    foreach ($src_trd as $t_key => $t_val) {
        if (!isset($comp_trd[$t_key])) {
            $diff_count++;
            disp($comp_desc . '缺少交易-' . '卡號:' . $t_key . '; 金額:' . $t_val . '; 刷卡單號:');
            if ($t_val > 0) {
                $diff_pay_total += $t_val;
            } else {
                $diff_refund_total += $t_val;
            }
        } else {
            if ($t_val != $comp_trd[$t_key]) {
                // 差異筆數計算
                $diff_count++;
                
                // 顯示差異訊息
                $t_diff = $t_val - $comp_trd[$t_key];
                $t_diff_msg = '金額不符[卡號:' . $t_key . ';';
                $t_diff_msg .= ' 金額:' . $src_desc . ' ' . $t_val . ' - (' . $comp_desc . ' ' . $comp_trd[$t_key] . ') = ' . $t_diff . ';';
                $t_diff_msg .= ']';
                disp($t_diff_msg);
                
                // 差異總金額計算
                if ($t_diff > 0) {
                    $diff_pay_total += $t_diff;
                } else {
                    $diff_refund_total += $t_diff;
                }
            }
        }
    }
    disp('差異總金額:' . $diff_pay_total . '|' . $diff_refund_total . '; 筆數:' . $diff_count);
    
    include('tpl/footer.php');
?>