<?php

/**
* 產生檢查碼 2015-11-26 wesley
* 傳入	$arParameters	各參數
* 傳入	$sHash_Key	HashKey
* 傳入	$sHash_Iv	HashIV
* 傳出	$sMacValue	檢查碼
*/
function GenerateCheckMacValue($arParameters, $sHash_Key = '', $sHash_Iv = '' )
{ 
	$sMacValue 		= '' ;
	$bError			= false ;
	$sError_Code		= '' ;
	
	// 判斷資料欄位有沒有值
	if( !isset($arParameters) || count($arParameters) == 0 || $sHash_Key == '' || $sHash_Iv == '' )
	{
		$bError	= true ;
		$sError_Code = 'error(415)_CheckMacValue傳遞參數錯誤' ;
	}
	
	// 付款種類判斷
	if(isset($arParameters['payment_type']))
	{
		// 判斷是否允許付款種類
		if($arParameters['payment_type'] != 'paypal' && $arParameters['payment_type'] != 'cvs' && $arParameters['payment_type'] != 'web_atm' && $arParameters['payment_type'] != 'vacc' && $arParameters['payment_type'] != 'barcode' && $arParameters['payment_type'] != 'ibon' && $arParameters['payment_type'] != 'alipay' && $arParameters['payment_type'] != 'tenpay' && $arParameters['payment_type'] != 'allpay')
		{
			$bError	= true ;	// 填寫的payment_type 參數 沒有包含允許項目
			$sError_Code = 'error(411)_填寫的payment_type 參數沒有包含允許項目' ;
		}
	}
	else
	{
		$bError	= true ;		// 沒有填寫payment_type 參數
		$sError_Code = 'error(410)_沒有填寫payment_type參數' ;
	}
	
	// 判斷必填項目(1.PayPal)
	if(isset($arParameters['payment_type']) && $arParameters['payment_type'] == 'paypal')
	{
		// 判斷必填項目 ECBank商店代號(mer_id) 
		if( !isset($arParameters['mer_id']) || $arParameters['mer_id'] == '' )
		{
			$bError	= true ;	// mer_id_ECBank商店代號 沒有填寫
			$sError_Code = 'error(410)_mer_id_ECBank商店代號沒有填寫' ;
		}
		
		// 判斷必填項目 交易項目名稱(item_name)
		if( !isset($arParameters['item_name']) || $arParameters['item_name'] == '' )
		{
			$bError	= true ;	// item_name_交易項目名稱
			$sError_Code = 'error(410)_item_name_交易項目名稱沒有填寫' ;
		}
		
		// 判斷必填項目 貨幣類別(cur_type)
		if( !isset($arParameters['cur_type']) || $arParameters['cur_type'] == '' )
		{
			$bError	= true ;	// cur_type_貨幣類別
			$sError_Code = 'error(410)_cur_type_貨幣類別沒有填寫' ;
		}
		
		// 判斷必填項目 交易金額(amt)
		if( !isset($arParameters['amt']) || $arParameters['amt'] == '' || $arParameters['amt'] < 0 )
		{
			$bError	= true ;	// amt_交易金額
			$sError_Code = 'error(410)_amt_交易金額沒有填寫' ;
		}	
	}
	
	// 判斷必填項目(2.超商繳費代碼取號)
	if( isset($arParameters['payment_type']) && $arParameters['payment_type'] == 'cvs' )
	{
		// 判斷必填項目 ECBank商店代號(mer_id) 
		if( !isset($arParameters['mer_id']) || $arParameters['mer_id'] == '' )
		{
			$bError	= true ;	// mer_id_ECBank商店代號 沒有填寫
			$sError_Code = 'error(410)_mer_id_ECBank商店代號 沒有填寫' ;
		}
		
		// 判斷必填項目 交易加密私鑰(enc_key)
		if( !isset($arParameters['enc_key']) || $arParameters['enc_key'] == '' )
		{
			$bError	= true ;	// item_name_交易加密私鑰
			$sError_Code = 'error(410)_item_name_交易加密私鑰沒有填寫' ;
		}
		
		// 判斷必填項目 交易金額(amt)
		if( !isset($arParameters['amt']) || $arParameters['amt'] == '' || $arParameters['amt'] < 0 )
		{
			$bError	= true ;	// amt_交易金額
			$sError_Code = 'error(410)_amt_交易金額沒有填寫' ;
		}
		
		// 判斷必填項目 商品說明(prd_desc)
		if( !isset($arParameters['prd_desc']) || $arParameters['prd_desc'] == '' )
		{
			$bError	= true ;	// prd_desc_商品說明
			$sError_Code = 'error(410)_prd_desc_商品說明沒有填寫' ;
		}

	}
	
	// 判斷必填項目(3.WEB-ATM)
	if( isset($arParameters['payment_type']) && $arParameters['payment_type'] == 'web_atm' )
	{
		// 判斷必填項目 ECBank商店代號(mer_id) 
		if( !isset($arParameters['mer_id']) || $arParameters['mer_id'] == '' )
		{
			$bError	= true ;	// mer_id_ECBank商店代號 沒有填寫
			$sError_Code = 'error(410)_mer_id_ECBank商店代號沒有填寫' ;
		}
		
		// 判斷必填項目 交易金額(amt)
		if( !isset($arParameters['amt']) || $arParameters['amt'] == '' )
		{
			$bError	= true ;	// amt_交易金額
			$sError_Code = 'error(410)_amt_交易金額沒有填寫' ;
		}
	}
	
	// 判斷必填項目(4.虛擬帳號取號)
	if( isset($arParameters['payment_type']) && $arParameters['payment_type'] == 'vacc' )
	{
		// 判斷必填項目 ECBank商店代號(mer_id) 
		if( !isset($arParameters['mer_id']) || $arParameters['mer_id'] == '' )
		{
			$bError	= true ;	// mer_id_ECBank商店代號
			$sError_Code = 'error(410)_mer_id_ECBank商店代號沒有填寫' ;
		}
		
		// 判斷必填項目 收單銀行(setbank) 
		if( !isset($arParameters['setbank']) || $arParameters['setbank'] == '' )
		{
			$bError	= true ;	// setbank_收單銀行
			$sError_Code = 'error(410)_setbank_收單銀行沒有填寫' ;
		}
		
		// 判斷必填項目 交易加密私鑰(enc_key)
		if( !isset($arParameters['enc_key']) || $arParameters['enc_key'] == '')
		{
			$bError	= true ;	// enc_key_交易加密私鑰
			$sError_Code = 'error(410)_enc_key_交易加密私鑰沒有填寫' ;
		}
		
		// 判斷必填項目 交易金額(amt)
		if( !isset($arParameters['amt']) || $arParameters['amt'] == '')
		{
			$bError	= true ;	// amt_交易金額
			$sError_Code = 'error(410)_amt_交易金額沒有填寫' ;
		}
		
		// 判斷必填項目 允許繳費有效天數(expire_day) 
		if( !isset($arParameters['expire_day']) || $arParameters['expire_day'] == '' )
		{
			$bError	= true ;	// expire_day_允許繳費有效天數
			$sError_Code = 'error(410)_expire_day_允許繳費有效天數沒有填寫' ;
		}
	}
	
	// 判斷必填項目(5.超商條碼取號)
	if( isset($arParameters['payment_type']) && $arParameters['payment_type'] == 'barcode')
	{
		// 判斷必填項目 ECBank商店代號(mer_id) 
		if( !isset($arParameters['mer_id']) || $arParameters['mer_id'] == '' )
		{
			$bError	= true ;	// mer_id_ECBank商店代號
			$sError_Code = 'error(410)_mer_id_ECBank商店代號沒有填寫' ;
		}
		
		// 判斷必填項目 交易加密私鑰(enc_key)
		if( !isset($arParameters['enc_key']) || $arParameters['enc_key'] == '' )
		{
			$bError	= true ;	// enc_key_交易加密私鑰
			$sError_Code = 'error(410)_enc_key_交易加密私鑰沒有填寫' ;
		}
		
		// 判斷必填項目 交易金額(amt)
		if( !isset($arParameters['amt']) || $arParameters['amt'] == '' )
		{
			$bError	= true ;	// amt_交易金額
			$sError_Code = 'error(410)_amt_交易金額沒有填寫' ;
		}
		
		// 判斷必填項目 允許繳費有效天數(expire_day) 
		if( !isset($arParameters['expire_day']) || $arParameters['expire_day'] == '' )
		{
			$bError	= true ;	// expire_day_允許繳費有效天數
			$sError_Code = 'error(410)_expire_day_允許繳費有效天數沒有填寫' ;
		}
	}
	
	// 判斷必填項目(6.7-Eleven ibon代碼取號)
	if( isset($arParameters['payment_type']) && $arParameters['payment_type'] == 'ibon')
	{
		// 判斷必填項目 ECBank商店代號(mer_id) 
		if( !isset($arParameters['mer_id']) || $arParameters['mer_id'] == '' )
		{
			$bError	= true ;	// mer_id_ECBank商店代號
			$sError_Code = 'error(410)_mer_id_ECBank商店代號沒有填寫' ;
		}
		
		// 判斷必填項目 交易加密私鑰(enc_key)
		if( !isset($arParameters['enc_key']) || $arParameters['enc_key'] == '' )
		{
			$bError	= true ;	// enc_key_交易加密私鑰
			$sError_Code = 'error(410)_enc_key_交易加密私鑰沒有填寫' ;
		}
		
		// 判斷必填項目 交易金額(amt)
		if( !isset($arParameters['amt']) || $arParameters['amt'] == '' )
		{
			$bError	= true ;	// amt_交易金額
			$sError_Code = 'error(410)_amt_交易金額沒有填寫' ;
		}
		
		// 判斷必填項目 商品說明(prd_desc) 
		if( !isset($arParameters['prd_desc']) || $arParameters['prd_desc'] == '' )
		{
			$bError	= true ;	// prd_desc_商品說明
			$sError_Code = 'error(410)_prd_desc_商品說明沒有填寫' ;
		}
	}
	
	// 判斷必填項目(7.支付寶)
	if( isset($arParameters['payment_type']) && $arParameters['payment_type'] == 'alipay' )
	{
		// 判斷必填項目 ECBank商店代號(mer_id) 
		if( !isset($arParameters['mer_id']) || $arParameters['mer_id'] == '' )
		{
			$bError	= true ;	// mer_id_ECBank商店代號沒有填寫
			$sError_Code = 'error(410)_mer_id_ECBank商店代號沒有填寫' ;
		}

		// 判斷必填項目 交易金額(amt)
		if( !isset($arParameters['amt']) || $arParameters['amt'] == '' || $arParameters['amt'] < 0 )
		{
			$bError	= true ;	// amt交易金額
			$sError_Code = 'error(410)_amt交易金額沒有填寫' ;
		}
		
		// 判斷必填項目 商品編號(goods_name[])
		if( !isset($arParameters['goods_name']) || count($arParameters['goods_name']) == 0 )
		{
			$bError	= true ;	// goods_name[]商品編號
			$sError_Code = 'error(410)_goods_name[]商品編號沒有填寫' ;
		}
		
		// 判斷必填項目 商品數量(goods_amount[])
		if( !isset($arParameters['goods_amount']) || count($arParameters['goods_amount']) == 0 )
		{
			$bError	= true ;	// goods_name[]商品數量
			$sError_Code = 'error(410)_goods_name[]商品數量沒有填寫' ;
		}
		
		// 商品名稱數量與商品金額是否相等
		if( isset($arParameters['goods_amount']) && isset($arParameters['goods_name']) )
		{
			if( count($arParameters['goods_name']) != count($arParameters['goods_amount']) )
			{
				$bError	= true ;// 商品名稱數量與商品金額數量不相等
				$sError_Code = 'error(410)_商品名稱數量與商品金額數量不相等' ;
			}	
			else
			{
				//var_dump($arParameters['goods_amount']);
				
				// 解開商品二維陣列
				$sGoods_Name 	= '' ; 
				$sGoods_Amount 	= '' ;
				
				// 先排序
				ksort($arParameters['goods_name']) ;
				//uksort($arParameters['goods_name'], 'merchantSort');
				
				ksort($arParameters['goods_amount']) ;
				//uksort($arParameters['goods_amount'], 'merchantSort');
				
				// 拆成字串
				$sGoods_Name 	= implode('',$arParameters['goods_name']);
				$sGoods_Amount 	= implode('',$arParameters['goods_amount']);	
				
				// 寫回陣列
				$arParameters['goods_name'] 	= $sGoods_Name ;
				$arParameters['goods_amount'] 	= $sGoods_Amount ;
			}
		}	
	}
	
	// 判斷必填項目(8.財付通)
	if( isset($arParameters['payment_type']) && $arParameters['payment_type'] == 'tenpay' )
	{
		// 判斷必填項目 ECBank商店代號(mer_id) 
		if( !isset($arParameters['mer_id']) || $arParameters['mer_id'] == '' )
		{
			$bError	= true ;	// mer_id_ECBank商店代號沒有填寫
			$sError_Code = 'error(410)_mer_id_ECBank商店代號沒有填寫' ;
		}
		
		// 判斷必填項目 交易金額(amt)
		if( !isset($arParameters['amt']) || $arParameters['amt'] == '' || $arParameters['amt'] < 0 )
		{
			$bError	= true ;	// amt_交易金額
			$sError_Code = 'error(410)_amt_交易金額沒有填寫' ;
		}	
	}
	
	// 判斷必填項目(9.歐付寶第三方支付串接)
	if( isset($arParameters['payment_type']) && $arParameters['payment_type'] == 'allpay' )
	{
		// 判斷必填項目 ECBank商店代號(mer_id) 
		if( !isset($arParameters['mer_id']) || $arParameters['mer_id'] == '' )
		{
			$bError	= true ;	// mer_id_ECBank商店代號
			$sError_Code = 'error(410)_mer_id_ECBank商店代號沒有填寫' ;
		}

		// 判斷必填項目 交易金額(amt)
		if( !isset($arParameters['amt']) || $arParameters['amt'] == '' )
		{
			$bError	= true ;	// amt_交易金額
			$sError_Code = 'error(410)_amt_交易金額沒有填寫' ;
		}
		
		// 商品編號轉成字串
		if( isset($arParameters['item_id']) )
		{
			$sItem_Id = '';
			
			// 先排序
			ksort($arParameters['item_id']) ;
			//uksort($arParameters['item_id'], 'merchantSort');
			
			// 拆成字串
			$sItem_Id = implode('',$arParameters['item_id']);
			
			// 寫回陣列
			$arParameters['item_id'] = $sItem_Id ;	
		}
		
		// 商品名稱轉成字串
		if( isset($arParameters['item_name']) )
		{
			$sItem_Name = '';
			
			// 先排序
			ksort($arParameters['item_name']) ;
			//uksort($arParameters['item_name'], 'merchantSort');
			
			// 拆成字串
			$sItem_Name = implode('',$arParameters['item_name']);
			
			// 寫回陣列
			$arParameters['item_name'] = $sItem_Name ;	
		}
		
		// 商品單價轉成字串
		if( isset($arParameters['item_price']) )
		{
			$sItem_Price = '';
			
			// 先排序
			ksort($arParameters['item_price']) ;
			//uksort($arParameters['item_price'], 'merchantSort');
			
			// 拆成字串
			$sItem_Price = implode('',$arParameters['item_price']);
			
			// 寫回陣列
			$arParameters['item_price'] = $sItem_Price ;	
		}
		
		// 商品數量轉成字串
		if( isset($arParameters['item_amount']) )
		{
			$sItem_Amount = '';
			
			// 先排序
			ksort($arParameters['item_amount']) ;
			//uksort($arParameters['item_amount'], 'merchantSort');
			
			// 拆成字串
			$sItem_Amount = implode('',$arParameters['item_amount']);
			
			// 寫回陣列
			$arParameters['item_amount'] = $sItem_Amount ;	
		}
		
		// 商品猶豫期轉成字串
		if( isset($arParameters['item_consider_hour']) )
		{
			$sItem_Consider = '';
			
			// 先排序
			ksort($arParameters['item_consider_hour']) ;
			//uksort($arParameters['item_consider_hour'], 'merchantSort');
			
			// 拆成字串
			$sItem_Consider = implode('',$arParameters['item_consider_hour']);
			
			// 寫回陣列
			$arParameters['item_consider_hour'] = $sItem_Consider ;	
		}
		
		// 商品網址轉成字串
		if( isset($arParameters['item_url']) )
		{
			$sItem_Url = '';
			
			// 先排序
			ksort($arParameters['item_url']) ;
			//uksort($arParameters['item_url'], 'merchantSort');
			
			// 拆成字串
			$sItem_Url = implode('',$arParameters['item_url']);
			
			// 寫回陣列
			$arParameters['item_url'] = $sItem_Url ;	
		}
		
	}
	
	// 沒有錯才壓碼
	if(!$bError)
	{
		// 移除check_mac_value
		if(isset($arParameters['checkmacvalue']))
		{
			unset($arParameters['checkmacvalue']) ;
		}
		
		// 資料排序
		ksort($arParameters);
		//uksort($arParameters, 'merchantSort');

//var_dump($arParameters);
//echo '<br>';
		// 開始組合字串
		$sMacValue = 'hashkey=' . $sHash_Key ;
		foreach($arParameters as $key => $value)
		{
			$sMacValue .= '&' . $key . '=' . $value ;
		}
		$sMacValue .= '&hashiv=' . $sHash_Iv ;	
//echo '組合字串： ' . $sMacValue . '<br>' ;		
		// URL Encode編碼		
		$sMacValue = urlencode($sMacValue);	
//echo 'URL Encode編碼： ' . $sMacValue . '<br>' ;		
		// 轉成小寫
		$sMacValue = strtolower($sMacValue);		
//echo '轉成小寫： ' . $sMacValue . '<br>' ;		
		// 取代為與 dotNet 相符的字元
		$sMacValue = str_replace('%2d', '-', $sMacValue);
		$sMacValue = str_replace('%5f', '_', $sMacValue);
		$sMacValue = str_replace('%2e', '.', $sMacValue);
		$sMacValue = str_replace('%21', '!', $sMacValue);
		$sMacValue = str_replace('%2a', '*', $sMacValue);
		$sMacValue = str_replace('%28', '(', $sMacValue);
		$sMacValue = str_replace('%29', ')', $sMacValue);
//echo '取代為與dotNet相符的字元： ' . $sMacValue . '<br>' ;							
		// MD5編碼
		$sMacValue = md5($sMacValue);
//echo 'MD5編碼： ' . $sMacValue . '<br>' ;		
		// 轉大寫
		$sMacValue = strtoupper($sMacValue);
//echo '轉大寫： ' . $sMacValue . '<br>' ;		
		return $sMacValue ;
	}
	else
	{
		return $sError_Code ;	
	}
}

function merchantSort($a,$b)
{
	return strcasecmp($a, $b);
}





/**
* 產生檢查碼回傳廠商用 20151228-wesley
* 傳入	$arParameters	array	各參數
* 傳出	$sMacValue	string	檢查碼
*/
function GenerateCheckMacValueReturn($arParameters)
{
	global $db, $buf;
	
	$sMacValue 		= '' ;
	$sError_Code		= '' ;
	$aKey	 		= array() ;
	
	// 找出 hashKEY hashIV
	$aKey 		= GetHashKey($arParameters['mer_id']);
	$sHash_Key 	= isset($aKey['hashkey']) 	? $aKey['hashkey'] 	: '' ;
	$sHash_Iv 	= isset($aKey['hashiv']) 	? $aKey['hashiv'] 	: '' ;

	// 移除check_mac_value
	if(isset($arParameters['checkmacvalue']))
	{
		unset($arParameters['checkmacvalue']) ;
	}
	
	// 資料排序
	ksort($arParameters);

//var_dump($arParameters);
//echo '<br>';
	// 開始組合字串
	$sMacValue = 'hashkey=' . $sHash_Key ;
	foreach($arParameters as $key => $value)
	{
		$sMacValue .= '&' . $key . '=' . $value ;
	}
	$sMacValue .= '&hashiv=' . $sHash_Iv ;	
	
//echo '組合字串： ' . $sMacValue . '<br>' ;		
	// URL Encode編碼	
		
	$sMacValue = urlencode($sMacValue);
		
//echo 'URL Encode編碼： ' . $sMacValue . '<br>' ;		
	// 轉成小寫
	
	
	$sMacValue = strtolower($sMacValue);		
//echo '轉成小寫： ' . $sMacValue . '<br>' ;		
	// 取代為與 dotNet 相符的字元
	
	$sMacValue = str_replace('%2d', '-', $sMacValue);
	$sMacValue = str_replace('%5f', '_', $sMacValue);
	$sMacValue = str_replace('%2e', '.', $sMacValue);
	$sMacValue = str_replace('%21', '!', $sMacValue);
	$sMacValue = str_replace('%2a', '*', $sMacValue);
	$sMacValue = str_replace('%28', '(', $sMacValue);
	$sMacValue = str_replace('%29', ')', $sMacValue);
	
//echo '取代為與dotNet相符的字元： ' . $sMacValue . '<br>' ;							
	// MD5編碼
	
	$sMacValue = md5($sMacValue);
	
//echo 'MD5編碼： ' . $sMacValue . '<br>' ;		
	// 轉大寫
	
	$sMacValue = strtoupper($sMacValue);
	
//echo '轉大寫： ' . $sMacValue . '<br>' ;	

	return $sMacValue ;
	
}

/**
* 查詢Hashkey Hashiv 20151228-wesley
* 傳入	$mer_id		int	廠商編號
* 傳出	$aResult	array	KEY 資訊
*/

function GetHashKey($mer_id)
{
	global $db, $buf;
	
	$sSql = ' SELECT hashkey, hashiv 
		FROM tblMerchants_Key
		WHERE mer_id = ' . $mer_id ;
	
	$aResult = $db->fetch_first($sSql);
	
	return $aResult ;
}


/**
* 寫入暫存DB 2015-12-18 wesley
* 傳入	$nMer_id	廠商代號
* 傳入	$sOd_Sob	廠商訂單編號
* 傳入	$nMoney		金額
*/
function insert_tblTrans_Request_Session_tmp_table($nMer_id, $sOd_Sob, $nMoney)
{
	global $db ;
	
	$nNow_Time	= time() ;
	$sSql 		= '' ;
	
	$sSql = 'INSERT INTO tblTrans_Request_Session_tmp ';
	$sSql .= '(mer_id, sob, money, dt)';
	$sSql .= "VALUES('$nMer_id','$sOd_Sob','$nMoney','$nNow_Time')";
	$db->query($sSql);
		
}

/**
* 刪除暫存DB 2015-12-19 wesley
* 傳入	$nMer_id	廠商代號
* 傳入	$sOd_Sob	廠商訂單編號
*/
function delete_tblTrans_Request_Session_tmp_table($nMer_id, $sOd_Sob)
{
	global $db ;
	
	$nNow_Time	= time() ;
	$sSql 		= '' ;
	
	$sSql = " DELETE FROM tblTrans_Request_Session_tmp where mer_id = " . $nMer_id . " AND sob = '" . $sOd_Sob . "'";

	$result = $db->query($sSql);

	
}

/**
* 取得DB內的金額 2015-12-18 wesley
* 傳入	$nMer_id	廠商代號
* 傳入	$sOd_Sob	廠商訂單編號
* 傳出	$nMoney		金額
*/
function select_tblTrans_Request_Session_temp_table($nMer_id, $sOd_Sob)
{
	global $db ;
	
	$nNow_Time	= time() ;
	$sSql 		= '' ;
	$nMoney		= 0 ;
	
	$sSql = " SELECT money FROM tblTrans_Request_Session_tmp WHERE mer_id = " . $nMer_id . " AND sob = '" . $sOd_Sob . "'  limit 1 ";

	$aResult = $db->fetch_first($sSql);
	
	$nMoney	= isset($aResult['money']) ? $aResult['money'] : 0 ;	

	return $nMoney ;

}


/**
* 寫入LOG資料表 2015-12-21 wesley
* 傳入	$nMer_id	string		廠商代號
* 傳入	$sOd_Sob	string		廠商訂單編號
* 傳入	$aPost_Data	array		POST資料
* 傳入	$sTable		string		寫入哪張資料表
*/
function insert_tblTrans_Request_Session_log_table($nMer_id, $sOd_Sob, $aPost_Data, $sTable = 'tblTrans_Request_Session_Log' )
{
	global $db ;
	
	$nNow_Time	= time() ;
	
	$sPost_Data	= '' ;
	$sPost_Data_Pro	= '' ;
	$sSql 		= '' ;
	
	// POST轉成字串
	if(!empty($aPost_Data))
	{
		foreach($aPost_Data as $key => $value)
		{
			$sPost_Data_Pro = ($sPost_Data == '' ) ? '' : '|' ;
			$sPost_Data .= $sPost_Data_Pro . $key . '=>' . $value ;
		}
		
		// 資料加密
		//$sPost_Data = file_encrypt($sPost_Data) ;
	}
	
	
	
	// 取得使用者來源 IP
	$sIp = !empty($_SERVER['REMOTE_ADDR']) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '';
	
	// 執行哪隻程式
	$sLog_Block = $_SERVER['SCRIPT_NAME'];
	
	// SQL
	$sSql = 'INSERT INTO ' . $sTable ;
	$sSql .= '(mer_id, od_sob, post_data, createdate, ip, log_block)';
	$sSql .= " VALUES (" . mysql_real_escape_string($nMer_id).",'".mysql_real_escape_string($sOd_Sob)."','".mysql_real_escape_string($sPost_Data)."',".mysql_real_escape_string($nNow_Time).",'".mysql_real_escape_string($sIp)."','".mysql_real_escape_string($sLog_Block)."')";

	$db->query($sSql);
	
		
}



?>