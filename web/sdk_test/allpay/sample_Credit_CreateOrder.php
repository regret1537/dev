<?php
/**
*    Credit信用卡付款產生訂單範例，參數說明請參考SDK技術文件(https://www.allpay.com.tw/Content/files/allpay_047.pdf)
*/
    
    //載入SDK(路徑可依系統規劃自行調整)
    include('./new/AllPay.Payment.Integration.php');
    try {
        
    	$obj = new AllInOne();
   
        //服務參數
        $obj->ServiceURL  = "https://payment.allpay.com.tw/Cashier/AioCheckOut/V2";   //服務位置
        $obj->HashKey     = 'kRCxusOEUr3PSF29' ;                                            //測試用Hashkey，請自行帶入AllPay提供的HashKey
        $obj->HashIV      = 'FQxU9TMsCkPCPOVV' ;                                            //測試用HashIV，請自行帶入AllPay提供的HashIV
        $obj->MerchantID  = '1064068';                                                      //測試用MerchantID，請自行帶入AllPay提供的MerchantID


        //基本參數(請依系統規劃自行調整)
        $obj->Send['ReturnURL']         = "http://www.allpay.com.tw/receive.php" ;    //付款完成通知回傳的網址
        $obj->Send['MerchantTradeNo']   = "Test".time() ;                             //訂單編號
        $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
        $obj->Send['TotalAmount']       = 10;                                       //交易金額
        $obj->Send['TradeDesc']         = "good to drink" ;                           //交易描述
        $obj->Send['ChoosePayment']     = PaymentMethod::Credit ;                     //付款方式:Credit

        //訂單的商品資料
        array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"10",
                   'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));


        //Credit信用卡分期付款延伸參數(可依系統需求選擇是否代入)
        //以下參數不可以跟信用卡定期定額參數一起設定
        $obj->SendExtend['CreditInstallment'] = 0 ;    //分期期數，預設0(不分期)
        $obj->SendExtend['InstallmentAmount'] = 0 ;    //使用刷卡分期的付款金額，預設0(不分期)
        $obj->SendExtend['Redeem'] = false ;           //是否使用紅利折抵，預設false
        $obj->SendExtend['UnionPay'] = false;          //是否為聯營卡，預設false;


        //產生訂單(auto submit至AllPay)
        $obj->CheckOut();

    
    } catch (Exception $e) {
    	echo $e->getMessage();
    } 


 
?>