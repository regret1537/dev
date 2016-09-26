<?php
    include('../../../tpl/header.php');
    include('../../../lib/html_common.inc');
    include('../../../lib/misc.inc');
	
	$sType = 'stage';
	// $sType = 'prod';
	include('./ECPay.Payment.Integration.php');
	disp('Type: ' . $sType);
	switch ($sType) {
		case 'stage':
			$sService_URL = 'https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/v2';
			$sMid = '2000132';
			$sHash_Key = '5294y06JbISpM5x9';
			$sHash_IV = 'v77hoKGq4kWxNNIS';
			$sMerchant_Trade_No = 'Test1472190804';
			break;
		case 'prod':
			$sService_URL = 'https://payment.ecpay.com.tw/Cashier/QueryTradeInfo/V2';
			$sMid = '1064068';
			$sHash_Key = 'kRCxusOEUr3PSF29';
			$sHash_IV = 'FQxU9TMsCkPCPOVV';
			$sMerchant_Trade_No = 'Test1472190804';
			break;
		default:
	}
	try{
		$oPayment = new AllInOne();
		$oPayment->ServiceURL = $sService_URL;
		$oPayment->MerchantID = $sMid;
		$oPayment->HashKey = $sHash_Key;
		$oPayment->HashIV = $sHash_IV;
		$oPayment->Query['MerchantTradeNo'] = $sMerchant_Trade_No;
		$arQueryFeedback = $oPayment ->QueryTradeInfo();
		disp(print_r($arQueryFeedback, true), true);
	} catch (Exception $e) {
		disp('ex:'.$e->getMessage());
	}
    include('../../../tpl/footer.php');
?>