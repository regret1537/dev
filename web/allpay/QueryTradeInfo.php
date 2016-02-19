<?php
    define('ROOT_PATH', 'C:/wamp/www');
    $dev_root_path = ROOT_PATH . '/dev';
    include($dev_root_path . '/tpl/header.php');
    include($dev_root_path . '/lib/html_common.inc');
    include($dev_root_path . '/lib/misc.inc');
    
    $query_url = 'https://payment-stage.allpay.com.tw/Cashier/QueryTradeInfo';
    $params = array(
        'MerchantID' => '2000214',
        'MerchantTradeNo' => '20160216173620',
        'TimeStamp' => time(),
        // 'EncryptType' => 1
    );
    
    $gen_code_url = 'https://payment-stage.allpay.com.tw/AioHelper/GenCheckMacValue';
    $check_mac_value = ServerPost($params, $gen_code_url);
    $params['CheckMacValue'] = $check_mac_value;
    
    $query_res_str = ServerPost($params, $query_url);
    
    $query_res = array();
    parse_str($query_res_str, $query_res);
    
    disp(print_r($query_res, true), true);
    
    function ServerPost($parameters, $url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        $rs = curl_exec($ch);

        curl_close($ch);

        return $rs;
    }
    include($dev_root_path . '/tpl/footer.php');
?>