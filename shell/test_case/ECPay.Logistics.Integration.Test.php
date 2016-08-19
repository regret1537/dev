<?php
/**
 * ECPay 物流 SDK 功能測試
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @filesource ./logistic/ECPay.Logistics.Integration.php
 * @filesource ./ECPay.Logistics.Integration.Test.Config.php
 * @version    1.0
 */
 
include(__DIR__ . "/SDK/ECPay.Logistics.Integration.php");
include(__DIR__ . "/config/ECPay.Logistics.Integration.Test.Config.php");

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
    echo $sContent . PHP_EOL;
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
 *  比對測試結果是否相符
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @category   test
 * @param      Mix $mHaystack 比對來源
 * @param      Mix $mNeedle 比對內容
 * @version    1.0
 */
function assert_equals($mHaystack, $mNeedle, $sFunction_Name = "") {
    if ($mHaystack === $mNeedle) {
        disp($sFunction_Name . " 測試通過");
    } else {
        disp($sFunction_Name . " 測試失敗");
    }
}

/**
 *  比對測試結果是否大於預期數值
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @category   test
 * @param      Int $iExpect_Num 預期數值
 * @param      Int $iActual_Num 實際數值
 * @version    1.0
 */
function assert_great_than($iExpect_Num, $iActual_Num, $sFunction_Name = "") {
	if (!is_integer($iExpect_Num) or !is_integer($iActual_Num) or ($iActual_Num <= $iExpect_Num)) {
        disp($sFunction_Name . " 測試失敗");
    } else {
        disp($sFunction_Name . " 測試通過");
    }
}

/**
 *  比對測試結果資料型態是否相符
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @category   test
 * @param      String $sExpect_Type 預期類別
 * @param      String $sActual_Type 實際類別
 * @version    1.0
 */
function assert_internal_type($sExpect_Type, $sActual_Type, $sFunction_Name = "") {
	if ($sActual_Type === $sExpect_Type) {
        disp($sFunction_Name . " 測試通過");
    } else {
        disp($sFunction_Name . " 測試失敗");
    }
}

/**
 *  比對測試結果資料是否為空
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @category   test
 * @param      String $mActual_Content 實際內容
 * @version    1.0
 */
function assert_empty($mActual_Content, $sFunction_Name = "") {
	if (empty($mActual_Content)) {
        disp($sFunction_Name . " 測試通過");
    } else {
        disp($sFunction_Name . " 測試失敗");
    }
}

/**
 * 統一電子地圖串接(CvsMap)
 */
$sTest_Subject = "ECPay 統一電子地圖串接(CvsMap)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"MerchantTradeNo" => "no" . date("YmdHis"),
		"LogisticsSubType" => LogisticsSubType::UNIMART,
		"IsCollection" => IsCollection::NO,
		"ServerReplyURL" => $sHome_URL . "/ServerReplyURL.php",
		"ExtraData" => "測試額外資訊",
		"Device" => Device::PC
	);
	$sTest_Result = $oA_L->CvsMap("電子地圖(統一)");
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_have($sTest_Result, "ECPayForm", $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 全家電子地圖串接(CvsMap)
 */
$sTest_Subject = "ECPay 全家電子地圖串接(CvsMap)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"MerchantTradeNo" => "no" . date("YmdHis"),
		"LogisticsSubType" => LogisticsSubType::FAMILY,
		"IsCollection" => IsCollection::NO,
		"ServerReplyURL" => $sHome_URL . "/ServerReplyURL.php",
		"ExtraData" => "測試額外資訊",
		"Device" => Device::PC
	);
	$sTest_Result = $oA_L->CvsMap("電子地圖(全家)");
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_have($sTest_Result, "ECPayForm", $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 幕前宅配物流訂單產生(CreateShippingOrder)
 */
$sTest_Subject = "ECPay 宅配物流訂單產生(CreateShippingOrder)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"MerchantTradeNo" => "no" . date("YmdHis"),
		"MerchantTradeDate" => date("Y/m/d H:i:s"),
		"LogisticsType" => LogisticsType::HOME,
		"LogisticsSubType" => LogisticsSubType::TCAT,
		"GoodsAmount" => 1500,
		"CollectionAmount" => 10,
		"IsCollection" => IsCollection::NO,
		"GoodsName" => "測試商品",
		"SenderName" => "測試寄件者",
		"SenderPhone" => "0226550115",
		"SenderCellPhone" => "0911222333",
		"ReceiverName" => "測試收件者",
		"ReceiverPhone" => "0226550115",
		"ReceiverCellPhone" => "0933222111",
		"ReceiverEmail" => "test_emjhdAJr@test.com.tw",
		"TradeDesc" => "測試交易敘述",
		"ServerReplyURL" => $sHome_URL . "/ServerReplyURL.php",
		"ClientReplyURL" => $sHome_URL . "/ClientReplyURL.php",
		"LogisticsC2CReplyURL" => $sHome_URL . "/LogisticsC2CReplyURL.php",
		"Remark" => "測試備註",
		"PlatformID" => "",
	);
	$oA_L->SendExtend = array(
		"SenderZipCode" => "11560",
		"SenderAddress" => "台北市南港區三重路 19-2 號 10 樓 D 棟",
		"ReceiverZipCode" => "11560",
		"ReceiverAddress" => "台北市南港區三重路 19-2 號 5 樓 D 棟",
		"Temperature" => Temperature::ROOM,
		"Distance" => Distance::SAME,
		"Specification" => Specification::CM_150,
		"ScheduledDeliveryTime" => ScheduledDeliveryTime::TIME_17_20
	);
	// CreateShippingOrder(Button 名稱, Form target)
	$sTest_Result = $oA_L->CreateShippingOrder("宅配物流訂單建立");
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_have($sTest_Result, "ECPayForm", $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 幕後宅配物流訂單產生(BGCreateShippingOrder)
 */
$sTest_Subject = "ECPay 幕後宅配物流訂單產生(BGCreateShippingOrder)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"MerchantTradeNo" => "no" . date("YmdHis"),
		"MerchantTradeDate" => date("Y/m/d H:i:s"),
		"LogisticsType" => LogisticsType::HOME,
		"LogisticsSubType" => LogisticsSubType::TCAT,
		"GoodsAmount" => 1500,
		"CollectionAmount" => 10,
		"IsCollection" => IsCollection::NO,
		"GoodsName" => "測試商品",
		"SenderName" => "測試寄件者",
		"SenderPhone" => "0226550115",
		"SenderCellPhone" => "0911222333",
		"ReceiverName" => "測試收件者",
		"ReceiverPhone" => "0226550115",
		"ReceiverCellPhone" => "0933222111",
		"ReceiverEmail" => "test_emjhdAJr@test.com.tw",
		"TradeDesc" => "測試交易敘述",
		"ServerReplyURL" => $sHome_URL . "/ServerReplyURL.php",
		"LogisticsC2CReplyURL" => $sHome_URL . "/LogisticsC2CReplyURL.php",
		"Remark" => "測試備註",
		"PlatformID" => "",
	);
	$oA_L->SendExtend = array(
		"SenderZipCode" => "11560",
		"SenderAddress" => "台北市南港區三重路 19-2 號 10 樓 D 棟",
		"ReceiverZipCode" => "11560",
		"ReceiverAddress" => "台北市南港區三重路 19-2 號 5 樓 D 棟",
		"Temperature" => Temperature::ROOM,
		"Distance" => Distance::SAME,
		"Specification" => Specification::CM_150,
		"ScheduledDeliveryTime" => ScheduledDeliveryTime::TIME_17_20
	);
	$sTest_Result = $oA_L->BGCreateShippingOrder();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_internal_type("array", gettype($sTest_Result), $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 幕前超商取貨物流訂單產生(CreateShippingOrder)
 */
$sTest_Subject = "ECPay 超商取貨物流訂單產生(CreateShippingOrder)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"MerchantTradeNo" => "no" . date("YmdHis"),
		"MerchantTradeDate" => date("Y/m/d H:i:s"),
		"LogisticsType" => LogisticsType::CVS,
		"LogisticsSubType" => LogisticsSubType::FAMILY,
		"GoodsAmount" => 1500,
		"CollectionAmount" => 10,
		"IsCollection" => IsCollection::YES,
		"GoodsName" => "測試商品 A#測試商品 B",
		"SenderName" => "測試寄件者",
		"SenderPhone" => "0226550115",
		"SenderCellPhone" => "0911222333",
		"ReceiverName" => "測試收件者",
		"ReceiverPhone" => "0226550115",
		"ReceiverCellPhone" => "0933222111",
		"ReceiverEmail" => "test_emjhdAJr@test.com.tw",
		"TradeDesc" => "測試交易敘述",
		"ServerReplyURL" => $sHome_URL . "/ServerReplyURL.php",
		"ClientReplyURL" => $sHome_URL . "/ClientReplyURL.php",
		"LogisticsC2CReplyURL" => $sHome_URL . "/LogisticsC2CReplyURL.php",
		"Remark" => "測試備註",
		"PlatformID" => "",
	);
	$oA_L->SendExtend = array(
		"ReceiverStoreID" => "001779",
		"ReturnStoreID" => "001779"
	);
	// CreateShippingOrder(Button 名稱, Form target)
	$sTest_Result = $oA_L->CreateShippingOrder("超商取貨物流訂單建立");
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_have($sTest_Result, "ECPayForm", $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 幕後超商取貨物流訂單產生(CreateShippingOrder)
 */
$sTest_Subject = "ECPay 幕後超商取貨物流訂單產生(BGCreateShippingOrder)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"MerchantTradeNo" => "no" . date("YmdHis"),
		"MerchantTradeDate" => date("Y/m/d H:i:s"),
		"LogisticsType" => LogisticsType::CVS,
		"LogisticsSubType" => LogisticsSubType::UNIMART,
		"GoodsAmount" => 1500,
		"CollectionAmount" => 10,
		"IsCollection" => IsCollection::YES,
		"GoodsName" => "測試商品",
		"SenderName" => "測試寄件者",
		"SenderPhone" => "0226550115",
		"SenderCellPhone" => "0911222333",
		"ReceiverName" => "測試收件者",
		"ReceiverPhone" => "0226550115",
		"ReceiverCellPhone" => "0933222111",
		"ReceiverEmail" => "test_emjhdAJr@test.com.tw",
		"TradeDesc" => "測試交易敘述",
		"ServerReplyURL" => $sHome_URL . "/ServerReplyURL.php",
		"LogisticsC2CReplyURL" => $sHome_URL . "/LogisticsC2CReplyURL.php",
		"Remark" => "測試備註",
		"PlatformID" => "",
	);
	$oA_L->SendExtend = array(
		"ReceiverStoreID" => "991182",
		"ReturnStoreID" => "991182"
	);
	$sTest_Result = $oA_L->BGCreateShippingOrder();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_internal_type("array", gettype($sTest_Result), $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 宅配逆物流訂單產生(CreateHomeReturnOrder)
 */
$sTest_Subject = "ECPay 宅配逆物流訂單產生(CreateHomeReturnOrder)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"AllPayLogisticsID" => "15609",
		"SenderName" => "測試寄件者",
		"SenderPhone" => "0226550115",
		"SenderCellPhone" => "0933222111",
		"SenderZipCode" => "11560",
		"SenderAddress" => "台北市南港區三重路 19-2 號 5 樓 D 棟",
		"ReceiverName" => "測試收件者",
		"ReceiverPhone" => "0226550116",
		"ReceiverCellPhone" => "0911222333",
		"ReceiverEmail" => "test_emjhdAJr@test.com.tw",
		"ReceiverZipCode" => "11560",
		"ReceiverAddress" => "台北市南港區三重路 19-2 號 5 樓 D 棟",
		"ServerReplyURL" => $sHome_URL . "/ServerReplyURL.php",
		"PlatformID" => "",
	);
	$sTest_Result = $oA_L->CreateHomeReturnOrder();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_internal_type("array", gettype($sTest_Result), $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 超商取貨逆物流訂單(全家超商 B2C)(CreateFamilyB2CReturnOrder)
 */
$sTest_Subject = "ECPay 超商取貨逆物流訂單(全家超商 B2C)(CreateFamilyB2CReturnOrder)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"AllPayLogisticsID" => "15614",
		"ServerReplyURL" => $sHome_URL . "/ServerReplyURL.php",
		"GoodsName" => "測試商品 A#測試商品 B",
		"GoodsAmount" => 1500,
		"SenderName" => "歐付寶(寄)",
		"SenderPhone" => "0226550115",
		"Remark" => "測試備註",
		"Quantity" => "1#2",
		"Cost" => "100#700",
		"PlatformID" => "",
	);
	$sTest_Result = $oA_L->CreateFamilyB2CReturnOrder();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_internal_type("array", gettype($sTest_Result), $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 全家逆物流核帳(全家超商 B2C)(CheckFamilyB2CLogistics)
 */
$sTest_Subject = "ECPay 全家逆物流核帳(全家超商 B2C)(CheckFamilyB2CLogistics)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"RtnMerchantTradeNo" => "1601121637065",
		"PlatformID" => ""
	);
	// CheckFamilyB2CLogistics()
	$sTest_Result = $oA_L->CheckFamilyB2CLogistics();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}
assert_internal_type("array", gettype($sTest_Result), $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 廠商修改物流資訊(統一超商 B2C)(UpdateUnimartLogisticsInfo)
 */
$sTest_Subject = "ECPay 廠商修改物流資訊(統一超商 B2C)(UpdateUnimartLogisticsInfo)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"AllPayLogisticsID" => "15627",
		"ShipmentDate" => date("Y/m/d", strtotime("+1 day")),
		"ReceiverStoreID" => "991182",
		"PlatformID" => ""
	);
	// UpdateUnimartLogisticsInfo()
	$sTest_Result = $oA_L->UpdateUnimartLogisticsInfo();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_internal_type("array", gettype($sTest_Result), $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 更新門市(統一超商 C2C)(UpdateUnimartStore)
 */
$sTest_Subject = "ECPay 更新門市(統一超商 C2C)(UpdateUnimartStore)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestC2CMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestC2CMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestC2CMerchantInfo::MERCHANT_ID,
		"AllPayLogisticsID" => "11796",
		"CVSPaymentNo" => "F0015091",
		"CVSValidationNo" => "3207",
		"StoreType" => StoreType::RECIVE_STORE,
		"ReceiverStoreID" => "991183",
		"PlatformID" => ""
	);
	// UpdateUnimartStore()
	$sTest_Result = $oA_L->UpdateUnimartStore();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_internal_type("array", gettype($sTest_Result), $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 取消訂單(統一超商 C2C)(CancelUnimartLogisticsOrder)
 */
$sTest_Subject = "ECPay 取消訂單(統一超商 C2C)(CancelUnimartLogisticsOrder)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"AllPayLogisticsID" => "15474",
		"CVSPaymentNo" => "F0015091",
		"CVSValidationNo" => "3207",
		"PlatformID" => ""
	);
	// CancelUnimartLogisticsOrder()
	$sTest_Result = $oA_L->CancelUnimartLogisticsOrder();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_internal_type("array", gettype($sTest_Result), $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 物流訂單查詢(QueryLogisticsInfo)
 */
$sTest_Subject = "ECPay 物流訂單查詢(QueryLogisticsInfo)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"RtnMerchantTradeNo" => "1601121637065",
		"PlatformID" => ""
	);
	// CheckFamilyB2CLogistics()
	$sTest_Result = $oA_L->CheckFamilyB2CLogistics();
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_internal_type("array", gettype($sTest_Result), $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 產生托運單(宅配)/一段標(超商取貨)(PrintTradeDoc)
 */
$sTest_Subject = "ECPay 產生托運單(宅配)/一段標(超商取貨)(PrintTradeDoc)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestMerchantInfo::MERCHANT_ID,
		"AllPayLogisticsID" => "14559",
		"PlatformID" => ""
	);
	// PrintTradeDoc(Button 名稱, Form target)
	$sTest_Result = $oA_L->PrintTradeDoc("產生托運單/一段標");
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_have($sTest_Result, "ECPayForm", $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 列印統一繳款單(統一超商 C2C)(PrintUnimartC2CBill)
 */
$sTest_Subject = "ECPay 列印統一繳款單(統一超商 C2C)(PrintUnimartC2CBill)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestC2CMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestC2CMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestC2CMerchantInfo::MERCHANT_ID,
		"AllPayLogisticsID" => "11808",
		"CVSPaymentNo" => "F0015102",
		"CVSValidationNo" => "4130",
		"PlatformID" => ""
	);
	// PrintUnimartC2CBill(Button 名稱, Form target)
	$sTest_Result = $oA_L->PrintUnimartC2CBill("列印統一繳款單(統一超商 C2C)");
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_have($sTest_Result, "ECPayForm", $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * 列印全家小白單(全家超商 C2C)(PrintFamilyC2CBill)
 */
$sTest_Subject = "ECPay 列印全家小白單(全家超商 C2C)(PrintFamilyC2CBill)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestC2CMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestC2CMerchantInfo::HASH_IV;
	$oA_L->Send = array(
		"MerchantID" => TestC2CMerchantInfo::MERCHANT_ID,
		"AllPayLogisticsID" => "11810",
		"CVSPaymentNo" => "05902347158",
		"PlatformID" => ""
	);
	// PrintFamilyC2CBill(Button 名稱, Form target)
	$sTest_Result = $oA_L->PrintFamilyC2CBill("列印全家小白單(全家超商 C2C)");
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_have($sTest_Result, "ECPayForm", $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result);


/**
 * CheckMacValue 驗證功能(CheckOutFeedback)
 */
$sTest_Subject = "ECPay CheckMacValue 驗證功能(CheckOutFeedback)";
$sTest_Result = "";
$sHome_URL = "http://www.sample.com.tw";
$aTest_POST = array(
	"MerchantID" => TestMerchantInfo::MERCHANT_ID,
	"MerchantTradeNo" => "no20160816070914",
	"MerchantTradeDate" => "2016/08/16 07:09:14",
	"LogisticsType" => "Home",
	"LogisticsSubType" => "TCAT",
	"GoodsAmount" => "1500",
	"IsCollection" => "N",
	"GoodsName" => "測試商品",
	"SenderName" => "測試寄件者",
	"SenderPhone" => "0226550115",
	"SenderCellPhone" => "0911222333",
	"ReceiverName" => "測試收件者",
	"ReceiverPhone" => "0226550115",
	"ReceiverCellPhone" => "0933222111",
	"ReceiverEmail" => "test_emjhdAJr@test.com.tw",
	"TradeDesc" => "測試交易敘述",
	"ServerReplyURL" => $sHome_URL . "/ServerReplyURL.php",
	"ClientReplyURL" => $sHome_URL . "/ClientReplyURL.php",
	"LogisticsC2CReplyURL" => $sHome_URL . "/LogisticsC2CReplyURL.php",
	"Remark" => "測試備註",
	"PlatformID" => "",
	"SenderZipCode" => "11560",
	"SenderAddress" => "台北市南港區三重路 19-2 號 10 樓 D 棟",
	"ReceiverZipCode" => "11560",
	"ReceiverAddress" => "台北市南港區三重路 19-2 號 5 樓 D 棟",
	"Temperature" => "0003",
	"Distance" => "01",
	"Specification" => "0004",
	"ScheduledDeliveryTime" => "3",
	"ScheduledPickupTime" => "4",
	"CheckMacValue" => "BE972990BFE7BE986E33EE4673D37A45",
);
try {
	$oA_L = new ECPayLogistics();
	$oA_L->HashKey = TestMerchantInfo::HASH_KEY;
	$oA_L->HashIV = TestMerchantInfo::HASH_IV;
	// CheckOutFeedback(POST 參數)
	$oA_L->CheckOutFeedback($aTest_POST);
} catch (Exception $e) {
    $sTest_Result = $e->getMessage();
    unset($e);
}

assert_empty($sTest_Result, $sTest_Subject);
unset($sHome_URL, $oA_L, $sTest_Subject, $sTest_Result, $aTest_POST);
?>
