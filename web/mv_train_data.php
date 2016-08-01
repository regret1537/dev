<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
    if (isset($_GET['mv_y_m'])) {
        $mv_y_m = $_GET['mv_y_m'];
    } else {
        $mv_y_m = date('Ym');
    }
    $mv_y = substr($mv_y_m, 0, 4);# 備份西元年
    $mv_m = sprintf('%02s', substr($mv_y_m, -2));# 搬移月份
    
    $mv_date = date('Y/m/d H:i:s', strtotime($mv_y . '-' . $mv_m . '-01 00:00:00'));
    if ($mv_date == '1970/01/01 08:00:00') {
        echo 'Format error!';
    }
       
    # newecpay_yyyy
    $trd_src_db = 'newecpay';
    $trd_bk_db = $trd_src_db . '_' . $mv_y;
    # train_yyyy
    $train_src_db = 'train';
    $train_bk_db = $train_src_db . '_' . $mv_y;
    $sql_list = array(
        'INSERT ' . $trd_bk_db . '.o_cdno SELECT * FROM ' . $trd_src_db . '.o_cdno WHERE sr IN (SELECT sr FROM ' . $trd_src_db . '.o_auth WHERE dtymd BETWEEN "%s%s%s" AND "%s%s%s");',
        'INSERT ' . $trd_bk_db . '.o_auth SELECT * FROM ' . $trd_src_db . '.o_auth WHERE dtymd BETWEEN "%s%s%s" AND "%s%s%s";',
        'INSERT ' . $trd_bk_db . '.o_close SELECT * FROM ' . $trd_src_db . '.o_close WHERE dtymd BETWEEN "%s%s%s" AND "%s%s%s";',
        'DELETE FROM ' . $trd_src_db . '.o_cdno WHERE sr IN (SELECT sr FROM ' . $trd_src_db . '.o_auth WHERE dtymd BETWEEN "%s%s%s" AND "%s%s%s");',
        'DELETE FROM ' . $trd_src_db . '.o_auth WHERE dtymd BETWEEN "%s%s%s" AND "%s%s%s";',
        'DELETE FROM ' . $trd_src_db . '.o_close WHERE dtymd BETWEEN "%s%s%s" AND "%s%s%s";',
        'INSERT ' . $train_bk_db . '.askdt SELECT * FROM ' . $train_src_db . '.askdt WHERE yyyymmdd BETWEEN "%s%s%s" AND "%s%s%s";',
        'INSERT ' . $train_bk_db . '.return_search_log SELECT * FROM ' . $train_src_db . '.return_search_log WHERE isday BETWEEN "%s%s%s" AND "%s%s%s";',
        'INSERT ' . $train_bk_db . '.tarin_003b SELECT * FROM ' . $train_src_db . '.tarin_003b WHERE dt BETWEEN "%s/%s/%s 00:00:00" AND "%s/%s/%s 23:59:59";',
        'INSERT ' . $train_bk_db . '.uorder SELECT * FROM ' . $train_src_db . '.uorder WHERE rtime BETWEEN "%s/%s/%s 00:00" AND "%s/%s/%s 23:59";',
        'INSERT ' . $train_bk_db . '.uorder_bk SELECT * FROM ' . $train_src_db . '.uorder_bk WHERE rtime BETWEEN "%s/%s/%s 00:00" AND "%s/%s/%s 23:59";',
        'INSERT ' . $train_bk_db . '.uorder_gwp SELECT * FROM ' . $train_src_db . '.uorder_gwp WHERE rtime BETWEEN "%s/%s/%s 00:00" AND "%s/%s/%s 23:59";',
        'DELETE FROM ' . $train_src_db . '.askdt WHERE yyyymmdd BETWEEN "%s%s%s" AND "%s%s%s";',
        'DELETE FROM ' . $train_src_db . '.return_search_log WHERE isday BETWEEN "%s%s%s" AND "%s%s%s";',
        'DELETE FROM ' . $train_src_db . '.tarin_003b WHERE dt BETWEEN "%s/%s/%s 00:00:00" AND "%s/%s/%s 23:59:59";',
        'DELETE FROM ' . $train_src_db . '.uorder WHERE rtime BETWEEN "%s/%s/%s 00:00" AND "%s/%s/%s 23:59";',
        'DELETE FROM ' . $train_src_db . '.uorder_bk WHERE rtime BETWEEN "%s/%s/%s 00:00" AND "%s/%s/%s 23:59";',
        'DELETE FROM ' . $train_src_db . '.uorder_gwp WHERE rtime BETWEEN "%s/%s/%s 00:00" AND "%s/%s/%s 23:59";',
    );
    # 搬移單位(天)
    $mv_unit = 10;
    $last_d = date('t', strtotime($mv_date));
    $mv_num = round(intval($last_d) / $mv_unit);
    for ($mv_idx = 0 ; $mv_idx < $mv_num ; $mv_idx++) {
        $s_d = sprintf('%02s', ($mv_idx * $mv_unit) + 1);
        if ($mv_idx == 2) {
            $e_d = $last_d;
        } else {
            $e_d = sprintf('%02s', ($mv_idx + 1) * $mv_unit);
        }
        foreach ($sql_list as $sql) {
            echo sprintf($sql, $mv_y , $mv_m , $s_d, $mv_y, $mv_m, $e_d) . '<br />';
        }
        echo '<br />';
    }
    include('../tpl/footer.php');
?>

