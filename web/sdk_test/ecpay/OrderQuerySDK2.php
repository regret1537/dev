<?php
header("Content-Type:text/html; charset=utf-8");
include_once('ECPay.Payment.Integration.php');
// include_once('AllPay.Payment.Integration.php');

try
{
$oPayment = new AllInOne();
/* 服務參數 */
 $oPayment->ServiceURL = "https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V2";
$oPayment->HashKey = "5294y06JbISpM5x9";
$oPayment->HashIV = "v77hoKGq4kWxNNIS";
$oPayment->MerchantID = "2000132"; 
//$oPayment->ServiceURL = "https://payment.ecpay.com.tw/Cashier/QueryTradeInfo/V2";
//$oPayment->HashKey = "";
//$oPayment->HashIV = "";
//$oPayment->MerchantID = ""; 

/* 基本參數 */
$oPayment->Query['MerchantTradeNo'] = "Test1472110976";

/* 查詢訂單 */
$arQueryFeedback = $oPayment ->QueryTradeInfo();

// 取回所有資料
if (sizeof($arQueryFeedback) > 0) {
foreach ($arQueryFeedback as $key => $value) {
switch ($key)
{
case "MerchantID": $szMerchantID = $value; break;
case "MerchantTradeNo": $szMerchantTradeNo = $value; break;
case "TradeNo": $szTradeNo = $value; break;
case "TradeAmt": $szTradeAmt = $value; break;
case "PayAmt": $szPayAmt = $value; break;
case "RedeemAmt": $szRedeemAmt = $value; break;
case "PaymentDate": $szPaymentDate = $value; break;
case "PaymentType": $szPaymentType = $value; break;
case "HandlingCharge": $szHandlingCharge = $value; break;
case "PaymentTypeChargeFee": $szPaymentTypeChargeFee = $value; break;
case "TradeDate": $szTradeDate = $value; break;
case "TradeStatus": $szTradeStatus = $value; break;
case "ItemName": $szItemName = $value; break;

//以下為額外回傳參數
/* 使用 WebATM 交易時回傳的參數 */
case "WebATMAccBank": $szWebATMAccBank = $value; break;
case "WebATMAccNo": $szWebATMAccNo = $value; break;
case "WebATMBankName": $szWebATMBankName = $value; break;

/* 使用 ATM 交易時回傳的參數 */
case "ATMAccBank": $szATMAccBank = $value; break;
case "ATMAccNo": $szATMAccNo = $value; break;

/* 使用 CVS 或 BARCODE 交易時回傳的參數 */
case "PaymentNo": $szPaymentNo = $value; break;
case "PayFrom": $szPayFrom = $value; break;

/* 使用 Alipay 交易時回傳的參數 */
case "AlipayID": $szAlipayID = $value; break;
case "AlipayTradeNo": $szAlipayTradeNo = $value; break;

/* 使用 Tenpay 交易時回傳的參數 */
case "TenpayTradeNo": $szTenpayTradeNo = $value; break;

/* 使用 Credit 交易時回傳的參數 */
case "gwsr": $szGwsr = $value; break;
case "process_date": $szProcessDate = $value; break;
case "auth_code": $szAuthCode = $value; break;
case "amount": $szAmount = $value; break;
case "stage": $szStage = $value; break;
case "stast": $szStast = $value; break;
case "staed": $szStaed = $value; break;
case "eci": $szECI = $value; break;
case "card4no": $szCard4No = $value; break;
case "card6no": $szCard6No = $value; break;
case "red_dan": $szRedDan = $value; break;
case "red_de_amt": $szRedDeAmt = $value; break;
case "red_ok_amt": $szRedOkAmt = $value; break;
case "red_yet": $szRedYet = $value; break;
case "PeriodType": $szPeriodType = $value; break;
case "Frequency": $szFreqquency = $value; break;
case "ExecTimes": $szExecTimes = $value; break;
case "PeriodAmount": $szPeriodAmount = $value; break;
case "TotalSuccessTimes": $szTotalSuccessTimes = $value; break;
case "TotalSuccessAmount": $szTotalSuccessAmount = $value; break;
default: break;
}
}
// 其他資料處理，以下為顯示回傳參數與參數值
foreach ($arQueryFeedback as $key => $val)
{
    echo $key ."=". $val ."<BR/>";
}
} else {
// 其他資料處理。
}
}
catch (Exception $e)
{// 例外錯誤處理。
	echo $e;
    throw $e;
}
?>