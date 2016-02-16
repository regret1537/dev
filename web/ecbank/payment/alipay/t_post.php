<?php
    define('WEB_ROOT', 'C:/wamp/www/dev');
    include(WEB_ROOT . '/tpl/header.php');
    include(WEB_ROOT . '/lib/html_common.inc');
    include(WEB_ROOT . '/lib/misc.inc');
    include(WEB_ROOT . '/lib/ecbank_security.inc');
    
    $frm_params = array(
        'mer_id' => '9493',# 輸入您的商店代號，若您有不同的商店代號可更改此欄位值
        'payment_type' => 'alipay',# 固定為 alipay (大小寫相同)
        'amt' => '100',# 該筆交易金額總數(只接受正整數)，輸入的金額為新台幣。
        'goods_name' => array('A10000','A10000'),# 該筆交易金額總數(只接受正整數)，輸入的金額為新台幣。
        'goods_amount' => array('1','2'),# 單項商品的所購買的數量
        'od_sob' => 'ali' . date('ymdHis'),# 只允許英文及數字及底線(_)，最大長度為30個字元，不輸入由系統產生
        'return_url' => 'http://localhost/dev/web/ecbank_alipay/t_ecbank_returl_url.php',# 交易完成後返回的頁面，如果空白，將導回綠界首頁。所帶的參數將會以GET方式傳回。
        'ok_url' => 'https://ecbank.com.tw/_payment/test.php',# 使用者交易完成後將由系統以背景方式觸發，所帶的參數會以POST方式傳回
        'buyer_email' => 'shawn.chang@greenworld.com.tw',# 信箱資料是傳給支付寶使用，EcBank將不會記錄
        'buyer_tel' => '0911222333',# 電話資料是傳給支付寶使用，EcBank將不會記錄
        'buyer_name' => '消費者',# 姓名資料是傳給支付寶使用，EcBank將不會記錄
    );
    $hash_key = 'b1e02828a01a08eb';
    $hash_iv = '6deabfa3776f4432';
    $mac_val_params = $frm_params;    
    $frm_params['checkmacvalue'] = GenerateCheckMacValue($mac_val_params, $hash_key, $hash_iv);
    if (isset($frm_params['checkmacvalue'])) {
        $frm_act = 'https://ecbank.com.tw/gateway_v2.php';
    } else {
        $frm_act = 'https://ecbank.com.tw/gateway.php';
    }
    
    $html = genSimpleForm($frm_act, $frm_params, '_blank');
    echo $html;
    
    
    include(WEB_ROOT . '/tpl/footer.php');
?>