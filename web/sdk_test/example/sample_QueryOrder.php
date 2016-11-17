<?php
/**
*    WebATM產生訂單範例
*/
    
    //載入SDK(路徑可依系統規劃自行調整)
    include('ECPay.Payment.Integration.php');
    try {
        
    	$obj = new ECPay_AllInOne();
   
        //服務參數
        $obj->ServiceURL  = "https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V2"; //服務位置
        $obj->HashKey     = '5294y06JbISpM5x9' ;                                          //測試用Hashkey，請自行帶入ECPay提供的HashKey
        $obj->HashIV      = 'v77hoKGq4kWxNNIS' ;                                          //測試用HashIV，請自行帶入ECPay提供的HashIV
        $obj->MerchantID  = '2000132';                                                    //測試用MerchantID，請自行帶入ECPay提供的MerchantID
		$obj->Query['MerchantTradeNo'] = '1610201457309471';
        $result = $obj->QueryTradeInfo();
		echo '<pre>'. print_r($result, true) . '</pre>';# test

    
    } catch (Exception $e) {
    	echo $e->getMessage();
    } 


 
?>