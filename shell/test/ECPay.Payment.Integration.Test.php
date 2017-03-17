<?php
include(__DIR__ . "/SDK/ECPay.Payment.Integration.php");
include(__DIR__ . "/config/ECPay.Payment.Integration.Test.Config.php");

/**
 * 一般產生訂單 CheckOutString
 */
 $TestName = '一般產生訂單 CheckOutString';
try {
	
	$obj = new ECPay_AllInOne();

	//服務參數
	$obj->ServiceURL  = ECPayTestURL::CHECKOUT;	        //服務位置
	$obj->HashKey     = TestMerchantInfo::HASH_KEY;          //測試用Hashkey，請自行帶入AllPay提供的HashKey
	$obj->HashIV      = TestMerchantInfo::HASH_IV;             //測試用HashIV，請自行帶入AllPay提供的HashIV
	$obj->MerchantID  = TestMerchantInfo::MERCHANT_ID;   //測試用MerchantID，請自行帶入AllPay提供的MerchantID


	//基本參數(請依系統規劃自行調整)
	$obj->Send['ReturnURL']         = "http://www.ecpay.com.tw/receive.php" ;    //付款完成通知回傳的網址
	$obj->Send['MerchantTradeNo']   = "Test".time() ;                             //訂單編號
	$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
	$obj->Send['TotalAmount']       = 2000;                                       //交易金額
	$obj->Send['TradeDesc']         = "good to drink" ;                           //交易描述
	$obj->Send['ChoosePayment']     = ECPay_PaymentMethod::ALL ;                        //付款方式:全功能

	//訂單的商品資料
	array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
			   'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));

	//產生訂單(auto submit至AllPay)
	$ret = $obj->CheckOutString();
	//echo $ret . PHP_EOL;
} catch (Exception $e) {
	echo $e->getMessage();
}

if (isset($e) || strpos($ret, '__ecpayForm')===false) {
	echo "測試失敗 " . $TestName . PHP_EOL;
	exit;
} else {
	echo "測試通過 " . $TestName . PHP_EOL;
	unset($obj, $ret, $e);
}

/**
 * ATM產生訂單 CheckOutString
 */
$TestName = 'ATM產生訂單 CheckOutString';
try {
	
	$obj = new ECPay_AllInOne();

	//服務參數
	$obj->ServiceURL  = ECPayTestURL::CHECKOUT;   //服務位置
	$obj->HashKey     = TestMerchantInfo::HASH_KEY;                                            //測試用Hashkey，請自行帶入AllPay提供的HashKey
	$obj->HashIV      = TestMerchantInfo::HASH_IV;                                            //測試用HashIV，請自行帶入AllPay提供的HashIV
	$obj->MerchantID  = TestMerchantInfo::MERCHANT_ID;                                                      //測試用MerchantID，請自行帶入AllPay提供的MerchantID


	//基本參數(請依系統規劃自行調整)
	$obj->Send['ReturnURL']         = "http://www.ecpay.com.tw/receive.php" ;    //付款完成通知回傳的網址
	$obj->Send['MerchantTradeNo']   = "Test".time() ;                             //訂單編號
	$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
	$obj->Send['TotalAmount']       = 2000;                                       //交易金額
	$obj->Send['TradeDesc']         = "good to drink" ;                           //交易描述
	$obj->Send['ChoosePayment']     = ECPay_PaymentMethod::ATM ;                        //付款方式:ATM

	//訂單的商品資料
	array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
			   'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));

	//ATM 延伸參數(可依系統需求選擇是否代入)
	$obj->SendExtend['ExpireDate'] = 3 ;     //繳費期限 (預設3天，最長60天，最短1天)
	$obj->SendExtend['PaymentInfoURL'] = ""; //伺服器端回傳付款相關資訊。

	//產生訂單(auto submit至AllPay)
	$ret = $obj->CheckOutString();
} catch (Exception $e) {
	echo $e->getMessage();
}  

if (isset($e) || strpos($ret, 'name="ChoosePayment" value=\'ATM\'')===false) {
	echo "測試失敗 " . $TestName . PHP_EOL;
	exit;
} else {
	echo "測試通過 " . $TestName . PHP_EOL;
	unset($obj, $ret, $e);
}

/**
 * CVS超商代碼產生訂單 CheckOutString
 */
$TestName = 'CVS超商代碼產生訂單 CheckOutString';
try {
	
	$obj = new ECPay_AllInOne();

	//服務參數
	$obj->ServiceURL  = ECPayTestURL::CHECKOUT;   //服務位置
	$obj->HashKey     = TestMerchantInfo::HASH_KEY;                                            //測試用Hashkey，請自行帶入AllPay提供的HashKey
	$obj->HashIV      = TestMerchantInfo::HASH_IV;                                            //測試用HashIV，請自行帶入AllPay提供的HashIV
	$obj->MerchantID  = TestMerchantInfo::MERCHANT_ID;                                                      //測試用MerchantID，請自行帶入AllPay提供的MerchantID


	//基本參數(請依系統規劃自行調整)
	$obj->Send['ReturnURL']         = "http://www.ecpay.com.tw/receive.php" ;    //付款完成通知回傳的網址
	$obj->Send['MerchantTradeNo']   = "Test".time() ;                             //訂單編號
	$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
	$obj->Send['TotalAmount']       = 2000;                                       //交易金額
	$obj->Send['TradeDesc']         = "good to drink" ;                           //交易描述
	$obj->Send['ChoosePayment']     = ECPay_PaymentMethod::CVS ;                        //付款方式:CVS超商代碼

	//訂單的商品資料
	array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
			   'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));


	//CVS超商代碼延伸參數(可依系統需求選擇是否代入)
	$obj->SendExtend['Desc_1']            = '';      //交易描述1 會顯示在超商繳費平台的螢幕上。預設空值
	$obj->SendExtend['Desc_2']            = '';      //交易描述2 會顯示在超商繳費平台的螢幕上。預設空值
	$obj->SendExtend['Desc_3']            = '';      //交易描述3 會顯示在超商繳費平台的螢幕上。預設空值
	$obj->SendExtend['Desc_4']            = '';      //交易描述4 會顯示在超商繳費平台的螢幕上。預設空值
	$obj->SendExtend['PaymentInfoURL']    = '';      //預設空值
	$obj->SendExtend['ClientRedirectURL'] = '';      //預設空值
	$obj->SendExtend['StoreExpireDate']   = '';      //預設空值

	//產生訂單(auto submit至AllPay)
	$ret = $obj->CheckOutString();
} catch (Exception $e) {
	echo $e->getMessage();
} 

if (isset($e) || strpos($ret, 'name="ChoosePayment" value=\'CVS\'')===false) {
	echo "測試失敗 " . $TestName . PHP_EOL;
	exit;
} else {
	echo "測試通過 " . $TestName . PHP_EOL;
	unset($obj, $ret, $e);
}

/**
 * BARCODE超商條碼產生訂單 CheckOutString
 */
$TestName = 'BARCODE超商條碼產生訂單 CheckOutString';
try {
	
	$obj = new ECPay_AllInOne();

	//服務參數
	$obj->ServiceURL  = ECPayTestURL::CHECKOUT;   //服務位置
	$obj->HashKey     = TestMerchantInfo::HASH_KEY ;                                            //測試用Hashkey，請自行帶入AllPay提供的HashKey
	$obj->HashIV      = TestMerchantInfo::HASH_IV ;                                            //測試用HashIV，請自行帶入AllPay提供的HashIV
	$obj->MerchantID  = TestMerchantInfo::MERCHANT_ID;                                                      //測試用MerchantID，請自行帶入AllPay提供的MerchantID


	//基本參數(請依系統規劃自行調整)
	$obj->Send['ReturnURL']         = "http://www.ecpay.com.tw/receive.php" ;    //付款完成通知回傳的網址
	$obj->Send['MerchantTradeNo']   = "Test".time() ;                             //訂單編號
	$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
	$obj->Send['TotalAmount']       = 2000;                                       //交易金額
	$obj->Send['TradeDesc']         = "good to drink" ;                           //交易描述
	$obj->Send['ChoosePayment']     = ECPay_PaymentMethod::BARCODE ;                    //付款方式:BARCODE超商代碼

	//訂單的商品資料
	array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
			   'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));


	//BARCODE超商條碼延伸參數(可依系統需求選擇是否代入)
	$obj->SendExtend['Desc_1']            = '';      //交易描述1 會顯示在超商繳費平台的螢幕上。預設空值
	$obj->SendExtend['Desc_2']            = '';      //交易描述2 會顯示在超商繳費平台的螢幕上。預設空值
	$obj->SendExtend['Desc_3']            = '';      //交易描述3 會顯示在超商繳費平台的螢幕上。預設空值
	$obj->SendExtend['Desc_4']            = '';      //交易描述4 會顯示在超商繳費平台的螢幕上。預設空值
	$obj->SendExtend['PaymentInfoURL']    = '';      //預設空值
	$obj->SendExtend['ClientRedirectURL'] = '';      //預設空值
	$obj->SendExtend['StoreExpireDate']   = '';      //預設空值

	//產生訂單(auto submit至AllPay)
	$ret = $obj->CheckOutString();
} catch (Exception $e) {
	echo $e->getMessage();
} 

if (isset($e) || strpos($ret, 'name="ChoosePayment" value=\'BARCODE\'')===false) {
	echo "測試失敗 " . $TestName . PHP_EOL;
	exit;
} else {
	echo "測試通過 " . $TestName . PHP_EOL;
	unset($obj, $ret, $e);
}

/**
 * Credit信用卡付款產生訂單 CheckOutString
 */
$TestName = 'Credit信用卡付款產生訂單 CheckOutString';
try {
	
	$obj = new ECPay_AllInOne();

	//服務參數
	$obj->ServiceURL  = ECPayTestURL::CHECKOUT;   //服務位置
	$obj->HashKey     = TestMerchantInfo::HASH_KEY ;                                            //測試用Hashkey，請自行帶入AllPay提供的HashKey
	$obj->HashIV      = TestMerchantInfo::HASH_IV ;                                            //測試用HashIV，請自行帶入AllPay提供的HashIV
	$obj->MerchantID  = TestMerchantInfo::MERCHANT_ID;                                                      //測試用MerchantID，請自行帶入AllPay提供的MerchantID


	//基本參數(請依系統規劃自行調整)
	$obj->Send['ReturnURL']         = "http://www.ecpay.com.tw/receive.php" ;    //付款完成通知回傳的網址
	$obj->Send['MerchantTradeNo']   = "Test".time() ;                             //訂單編號
	$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
	$obj->Send['TotalAmount']       = 2000;                                       //交易金額
	$obj->Send['TradeDesc']         = "good to drink" ;                           //交易描述
	$obj->Send['ChoosePayment']     = ECPay_PaymentMethod::Credit ;                     //付款方式:Credit

	//訂單的商品資料
	array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
			   'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));


	//Credit信用卡分期付款延伸參數(可依系統需求選擇是否代入)
	//以下參數不可以跟信用卡定期定額參數一起設定
	$obj->SendExtend['CreditInstallment'] = 0 ;    //分期期數，預設0(不分期)
	$obj->SendExtend['InstallmentAmount'] = 0 ;    //使用刷卡分期的付款金額，預設0(不分期)
	$obj->SendExtend['Redeem'] = false ;           //是否使用紅利折抵，預設false
	$obj->SendExtend['UnionPay'] = false;          //是否為聯營卡，預設false;

	//產生訂單(auto submit至AllPay)
	$ret = $obj->CheckOutString();
} catch (Exception $e) {
	echo $e->getMessage();
} 

if (isset($e) || strpos($ret, 'name="ChoosePayment" value=\'Credit\'')===false) {
	echo "測試失敗 " . $TestName . PHP_EOL;
	exit;
} else {
	echo "測試通過 " . $TestName . PHP_EOL;
	unset($obj, $ret, $e);
}

/**
 * WebATM產生訂單 CheckOutString
 */
$TestName = 'WebATM產生訂單 CheckOutString';
try {
	
	$obj = new ECPay_AllInOne();

	//服務參數
	$obj->ServiceURL  = ECPayTestURL::CHECKOUT;   //服務位置
	$obj->HashKey     = TestMerchantInfo::HASH_KEY ;                                            //測試用Hashkey，請自行帶入AllPay提供的HashKey
	$obj->HashIV      = TestMerchantInfo::HASH_IV ;                                            //測試用HashIV，請自行帶入AllPay提供的HashIV
	$obj->MerchantID  = TestMerchantInfo::MERCHANT_ID;                                                      //測試用MerchantID，請自行帶入AllPay提供的MerchantID


	//基本參數(請依系統規劃自行調整)
	$MerchantTradeNo = "Test".time() ;
	$obj->Send['ReturnURL']         = "http://www.ecpay.com.tw/receive.php" ;    //付款完成通知回傳的網址
	$obj->Send['MerchantTradeNo']   = $MerchantTradeNo;                           //訂單編號
	$obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                        //交易時間
	$obj->Send['TotalAmount']       = 2000;                                       //交易金額
	$obj->Send['TradeDesc']         = "good to drink" ;                           //交易描述
	$obj->Send['ChoosePayment']     = ECPay_PaymentMethod::WebATM ;                     //付款方式:WebATM

	//訂單的商品資料
	array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
			   'Currency' => "元", 'Quantity' => 1, 'URL' => "dedwed"));

	//產生訂單(auto submit至AllPay)
	//$obj->CheckOut();
   $ret = $obj->CheckOutString();
} catch (Exception $e) {
	echo $e->getMessage();
} 

if (isset($e) || strpos($ret, 'name="ChoosePayment" value=\'WebATM\'')===false) {
	echo "測試失敗 " . $TestName . PHP_EOL;
	exit;
} else {
	echo "測試通過 " . $TestName . PHP_EOL;
	unset($obj, $ret, $e);
}	
?>
