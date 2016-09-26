<?php
header("Content-Type:text/html; charset=utf-8");
include_once('./new/AllPay.Payment.Integration.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    
    $oPayment = new AllInOne();
    /* 服務參數 */
    $oPayment->ServiceURL = "https://payment.allpay.com.tw/CreditDetail/DoAction";
	$oPayment->HashKey = "kRCxusOEUr3PSF29"; //需填入正式資料
	$oPayment->HashIV = "FQxU9TMsCkPCPOVV";
	$oPayment->MerchantID = "1064068";
	// $oPayment->HashKey = ""; //需填入正式資料
	// $oPayment->HashIV = "";
	// $oPayment->MerchantID = "";
	
    /* 基本參數 */
	$oPayment->Action['MerchantTradeNo'] = "Test1472448184";//需修改成尚未退刷之訂單編號
    $oPayment->Action['TradeNo'] = "1608291323259345";//需修改成尚未退刷之歐付寶訂單編號
    $oPayment->Action['Action'] = ActionType::N;
    $oPayment->Action['TotalAmount'] = "10";//需修改成尚未退刷的訂單金額
	// $oPayment->Action['MerchantTradeNo'] = "";//需修改成尚未退刷之訂單編號
    // $oPayment->Action['TradeNo'] = "";//需修改成尚未退刷之歐付寶訂單編號
    // $oPayment->Action['Action'] = ActionType::R;
    // $oPayment->Action['TotalAmount'] = "";//需修改成尚未退刷的訂單金額
    $arFeedback = $oPayment->DoAction();
    echo '<pre>arFeedback' . print_r($arFeedback, true) . '</pre>';
    exit;
    }
	catch (Exception $e) {
		echo $e->getMessage();
    }
?>