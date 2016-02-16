<?php
    define('WEB_ROOT', 'C:/wamp/www/dev');
    include(WEB_ROOT . '/tpl/header.php');
    include(WEB_ROOT . '/lib/html_common.inc');
    include(WEB_ROOT . '/lib/misc.inc');
    
    $frm_params = array(
        'MerchantTradeNo' => '20160120142152',
        'TotalSuccessTimes' => '',
        'PaymentNo' => '',
        'AlipayID' => '',
        'red_dan' => '',
        'red_yet' => '',
        'eci' => '',
        'red_ok_amt' => '',
        'PeriodAmount' => '',
        'SimulatePaid' => ' 1',
        'AlipayTradeNo' => '',
        'MerchantID' => '2000214',
        'TenpayTradeNo' => '',
        'WebATMAccNo' => '',
        'TradeDate' => '2016/01/20 14:21:52',
        'gwsr' => '',
        'PeriodType' => '',
        'RtnCode' => '1',
        'RtnMsg' => ' 付款成功',
        'PayFrom' => '',
        'ATMAccBank' => '',
        'PaymentType' => 'Alipay_Alipay',
        'TotalSuccessAmount' => '',
        'PaymentTypeChargeFee' => ' 0',
        'stage' => '',
        'WebATMAccBank' => '',
        'TradeNo' => '1601201421527699',
        'card4no' => '',
        'card6no' => '',
        'auth_code' => '',
        'stast' => '',
        'PaymentDate' => '2016/01/20 14:22:10',
        'CheckMacValue' => '9D14C5715A8D3232EEB6D93578988CD5',
        'TradeAmt' => '100',
        'Frequency' => '',
        'red_de_amt' => '',
        'process_date' => '',
        'amount' => '',
        'ATMAccNo' => '',
        'ExecTimes' => '',
        'staed' => '',
        'WebATMBankName' => '',
    );
    
    $sim_str = 'MerchantTradeNo=4067747&TotalSuccessTimes=&PaymentNo=&AlipayID=&red_dan=&red_yet=&eci=&red_ok_amt=&PeriodAmount=&SimulatePaid=1&AlipayTradeNo=&MerchantID=1000139&TenpayTradeNo=&WebATMAccNo=&TradeDate=2016/01/20 15:31:53&gwsr=&PeriodType=&RtnCode=1&RtnMsg=付款成功&PayFrom=&ATMAccBank=&PaymentType=Alipay_Alipay&TotalSuccessAmount=&PaymentTypeChargeFee=0.0000&stage=&WebATMAccBank=&TradeNo=1601201531538752&card4no=&card6no=&auth_code=&stast=&PaymentDate=2016/01/20 15:44:55&CheckMacValue=B55A469F2847D6CC218D2EB080D25B44&TradeAmt=100&Frequency=&red_de_amt=&process_date=&amount=&ATMAccNo=&ExecTimes=&staed=&WebATMBankName=';
    $frm_params = array();
    parse_str($sim_str, $frm_params);
    
    $frm_act = 'https://ecbank.com.tw/_payment/alipay/OrderResultURL.php';
    $html = genSimpleForm($frm_act, $frm_params, '_blank');
    echo $html;
    
    include(WEB_ROOT . '/tpl/footer.php');
?>