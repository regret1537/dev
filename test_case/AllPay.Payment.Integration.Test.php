<?php
/**
 * allPay 金流 SDK 功能測試
 *             allPay 金流 SDK 相關功能
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @copyright  綠界科技 www.greenworld.com.tw
 * @filesource ./payment/AllPay.Payment.Integration.php
 * @filesource ./AllPay.Payment.Integration.Test.Config.php
 * @version    1.0
 */
 
include(__DIR__ . "/payment/AllPay.Payment.Integration.php");
include(__DIR__ . "/AllPay.Payment.Integration.Test.Config.php");

/**
 *  印出訊息
 *             將訊息顯示在網頁上
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @category   misc
 * @param      String $sContent 顯示訊息
 * @version    1.0
 */
function disp($sContent) {
    // echo $sContent . PHP_EOL;
    echo mb_convert_encoding($sContent, 'BIG5', 'UTF-8') . PHP_EOL;
}

/**
 *  比對測試結果是否包含關鍵字
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @category   test
 * @param      String $sHaystack 比對來源
 * @param      String $sSecond_Str 比對字串
 * @version    1.0
 */
function assert_have($sHaystack, $sNeedle, $sFunction_Name = "") {
    if (substr_count($sHaystack, $sNeedle) === 1) {
        disp($sFunction_Name . " 測試通過");
    } else {
        disp($sFunction_Name . " 測試失敗");
    }
}

/**
 * 全功能產生訂單 CheckOutString
 */
$sTest_Subject = "全功能產生訂單 CheckOutString";
$sTest_Result = "";
try {
	
	$oAIO = new AllInOne();

	//服務參數
	$oAIO->ServiceURL  = AllpayTestURL::CHECKOUT;	          //服務位置
	$oAIO->HashKey     = TestMerchantInfo::HASH_KEY;          //測試用Hashkey，請自行帶入AllPay提供的HashKey
	$oAIO->HashIV      = TestMerchantInfo::HASH_IV;           //測試用HashIV，請自行帶入AllPay提供的HashIV
	$oAIO->MerchantID  = TestMerchantInfo::MERCHANT_ID;       //測試用MerchantID，請自行帶入AllPay提供的MerchantID


	//基本參數(請依系統規劃自行調整)
	$oAIO->Send["ReturnURL"]         = "http://www.allpay.com.tw/receive.php";     //付款完成通知回傳的網址
	$oAIO->Send["MerchantTradeNo"]   = "Test" . time();                            //訂單編號
	$oAIO->Send["MerchantTradeDate"] = date("Y/m/d H:i:s");                        //交易時間
	$oAIO->Send["TotalAmount"]       = 2000;                                       //交易金額
	$oAIO->Send["TradeDesc"]         = "good to drink";                            //交易描述
	$oAIO->Send["ChoosePayment"]     = PaymentMethod::ALL;                         //付款方式:全功能

    //測試商品資訊
    $aGood_Info = array(
        "Name" => "歐付寶黑芝麻豆漿",
        "Price" => (int)"2000",
        "Currency" => "元",
        "Quantity" => (int) "1",
        "URL" => "dedwed"
    );
    
	//訂單的商品資料
	array_push($oAIO->Send["Items"], $aGood_Info);

	//產生訂單(auto submit至AllPay)
	$sTest_Result = $oAIO->CheckOutString();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

$sTest_Message = assert_have($sTest_Result, "__allpayForm", $sTest_Subject);
unset($oAIO, $aGood_Info, $sTest_Subject, $sTest_Resultm, $sTest_Message);

/**
 * ATM 產生訂單 CheckOutString
 */
$sTest_Subject = "ATM 產生訂單 CheckOutString";
$sTest_Result = "";
try {
	
	$oAIO = new AllInOne();

	//服務參數
	$oAIO->ServiceURL  = AllpayTestURL::CHECKOUT;	          //服務位置
	$oAIO->HashKey     = TestMerchantInfo::HASH_KEY;          //測試用Hashkey，請自行帶入AllPay提供的HashKey
	$oAIO->HashIV      = TestMerchantInfo::HASH_IV;           //測試用HashIV，請自行帶入AllPay提供的HashIV
	$oAIO->MerchantID  = TestMerchantInfo::MERCHANT_ID;       //測試用MerchantID，請自行帶入AllPay提供的MerchantID


	//基本參數(請依系統規劃自行調整)
	$oAIO->Send["ReturnURL"]         = "http://www.allpay.com.tw/receive.php";     //付款完成通知回傳的網址
	$oAIO->Send["MerchantTradeNo"]   = "Test" . time();                            //訂單編號
	$oAIO->Send["MerchantTradeDate"] = date("Y/m/d H:i:s");                        //交易時間
	$oAIO->Send["TotalAmount"]       = 2000;                                       //交易金額
	$oAIO->Send["TradeDesc"]         = "good to drink";                            //交易描述
	$oAIO->Send["ChoosePayment"]     = PaymentMethod::ATM;                         //付款方式:ATM

    //測試商品資訊
    $aGood_Info = array(
        "Name" => "歐付寶黑芝麻豆漿",
        "Price" => (int)"2000",
        "Currency" => "元",
        "Quantity" => (int) "1",
        "URL" => "dedwed"
    );
    
	//訂單的商品資料
	array_push($oAIO->Send["Items"], $aGood_Info);
    
    $oAIO->SendExtend["ExpireDate"] = 7;//允許繳費的有效天數
    $oAIO->SendExtend["PaymentInfoURL"] = "http://www.allpay.com.tw/receive.php";//伺服器端回傳付款相關資訊
    $oAIO->SendExtend["ClientRedirectURL"] = "http://www.allpay.com.tw/receive.php";//伺服器端回傳付款相關資訊

	//產生訂單(auto submit至AllPay)
	$sTest_Result = $oAIO->CheckOutString();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

$sTest_Message = assert_have($sTest_Result, "__allpayForm", $sTest_Subject);
unset($oAIO, $aGood_Info, $sTest_Subject, $sTest_Resultm, $sTest_Message);
?>
