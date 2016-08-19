<?php
	/**
	 * Transfer allPay SDK to ECPay SDK
	 *
	 * @version 1.0
	 * @author  Shawn.Chang
	 */
	
	
	/**
	 *  印出資料
	 *
	 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
	 * @category   misc
	 * @param      String $sContent 資料內容
	 * @version    1.0
	 */
	function disp($sContent) {
		echo $sContent . PHP_EOL;
	}
	
	// SDK transfer
	$sSrc_File = __DIR__ . '/file/SDK/AllPay.Logistics.Integration.php';
	$sTrans_File_Name = 'ECPay.Logistics.Integration.php';
	$sTrans_File = __DIR__ . '/file/SDK/' . $sTrans_File_Name;
	$sDest_File = '/home/vagrant/Code/dev/shell/test_case/SDK/' . $sTrans_File_Name;
	$aSearch_List = array(
		'allPay Logistics integration',
		'AllpayTestMerchantID',
		'AllpayURL',
		'AllpayTestURL',
		'AllpayLogistics',
		'allpayForm',
		'allpay.com.tw',
	);
	$aReplace_List = array(
		'ECPay Logistics integration',
		'ECPayTestMerchantID',
		'ECPayURL',
		'ECPayTestURL',
		'ECPayLogistics',
		'ECPayForm',
		'ecpay.com.tw',
	);
	$sRead_Data = file_get_contents($sSrc_File);
	$sTrans_Data = str_replace($aSearch_List, $aReplace_List, $sRead_Data);
	$iFile_Bytes = file_put_contents($sTrans_File, $sTrans_Data);
	if ($iFile_Bytes > 0) {
		disp($sTrans_File . ' created.');
	}
	$bCopy_Result = copy($sTrans_File, $sDest_File);
	if ($bCopy_Result) {
		disp('copy ' . $sTrans_File . ' to ' . $sDest_File);
	} else {
		disp('fail to copy ' . $sTrans_File . ' to ' . $sDest_File);
	}
	
	unset($sSrc_File, $sTrans_File, $aSearch_List, $aReplace_List, $sRead_Data, $sTrans_Data, $iFile_Bytes, $sTrans_File, $sDest_File);
	
	// Test case transfer
	$sSrc_File = __DIR__ . '/file/test_case/AllPay.Logistics.Integration.Test.php';
	$sTrans_File_Name = 'ECPay.Logistics.Integration.Test.php';
	$sTrans_File = __DIR__ . '/file/test_case/' . $sTrans_File_Name;
	$sDest_File = '/home/vagrant/Code/dev/shell/test_case/' . $sTrans_File_Name;
	$aSearch_List = array(
		'allPay ',
		'AllPay.Logistics.Integration',
		'AllpayLogistics',
		'allpayForm',
	);
	$aReplace_List = array(
		'ECPay ',
		'ECPay.Logistics.Integration',
		'ECPayLogistics',
		'ECPayForm',
	);
	$sRead_Data = file_get_contents($sSrc_File);
	$sTrans_Data = str_replace($aSearch_List, $aReplace_List, $sRead_Data);
	$iFile_Bytes = file_put_contents($sTrans_File, $sTrans_Data);
	if ($iFile_Bytes > 0) {
		disp($sTrans_File . ' created.');
	}
	$bCopy_Result = copy($sTrans_File, $sDest_File);
	if ($bCopy_Result) {
		disp('copy ' . $sTrans_File . ' to ' . $sDest_File);
	} else {
		disp('fail to copy ' . $sTrans_File . ' to ' . $sDest_File);
	}
	
	unset($sSrc_File, $sTrans_File, $aSearch_List, $aReplace_List, $sRead_Data, $sTrans_Data, $iFile_Bytes, $bCopy_Result, $sTrans_File, $sDest_File);
	
	// Sample transfer
	$aSample_List = array(
		'sample_BGCvsCreateShippingOrder.php',
		'sample_BGHomeCreateShippingOrder.php',
		'sample_CancelUnimartLogisticsOrder.php',
		'sample_CheckFamilyB2CLogistics.php',
		'sample_CreateFamilyB2CReturnOrder.php',
		'sample_CreateHomeReturnOrder.php',
		'sample_CvsCreateShippingOrder.php',
		'sample_CvsMap.php',
		'sample_HomeCreateShippingOrder.php',
		'sample_PrintFamilyC2CBill.php',
		'sample_PrintTradeDoc.php',
		'sample_PrintUnimartC2CBill.php',
		'sample_QueryLogisticsInfo.php',
		'sample_UpdateUnimartLogisticsInfo.php',
		'sample_UpdateUnimartStore.php'
	);
	$sSrc_Dir = __DIR__ . '/file/sample/allpay/';
	$sTrans_Dir = __DIR__ . '/file/sample/ecpay/';
	$aSearch_List = array(
		'allPay ',
		'AllPay.Logistics.Integration',
		'AllpayLogistics',
		'allpayForm',
	);
	$aReplace_List = array(
		'ECPay ',
		'ECPay.Logistics.Integration',
		'ECPayLogistics',
		'ECPayForm',
	);
	foreach ($aSample_List as $sSample_Name) {
		$sSrc_File = $sSrc_Dir . $sSample_Name;
		$sTrans_File = $sTrans_Dir . $sSample_Name;
		$sRead_Data = file_get_contents($sSrc_File);
		$sTrans_Data = str_replace($aSearch_List, $aReplace_List, $sRead_Data);
		$iFile_Bytes = file_put_contents($sTrans_File, $sTrans_Data);
		if ($iFile_Bytes > 0) {
			disp($sTrans_File . ' created.');
		}
		unset($sSrc_File, $sTrans_File, $sRead_Data, $sTrans_Data, $iFile_Bytes);
	}
	
	unset($aSample_List, $sSrc_Dir, $sTrans_Dir, $aSearch_List, $aReplace_List);
	
?>