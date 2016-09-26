<?php
    include('../../../tpl/header.php');
    include('../../../lib/html_common.inc');
    include('../../../lib/misc.inc');
	include('./old/AllPay.Payment.Integration.php');
	
	function generate($arParameters = array(),$HashKey = '' ,$HashIV = '',$encType = 0){
        $sMacValue = '' ;
        
        if(isset($arParameters))
        {   
            unset($arParameters['CheckMacValue']);
            // 資料排序 php 5.3以下不支援
            // uksort($arParameters, array('CheckMacValue','merchantSort'));
            ksort($arParameters);
               
            // 組合字串
            $sMacValue = 'HashKey=' . $HashKey ;
            foreach($arParameters as $key => $value)
            {
                $sMacValue .= '&' . $key . '=' . $value ;
            }
            
            $sMacValue .= '&HashIV=' . $HashIV ;    
            
            // URL Encode編碼     
            $sMacValue = urlencode($sMacValue); 
            
            // 轉成小寫
            $sMacValue = strtolower($sMacValue);        
            
            // 取代為與 dotNet 相符的字元
            $sMacValue = str_replace('%2d', '-', $sMacValue);
            $sMacValue = str_replace('%5f', '_', $sMacValue);
            $sMacValue = str_replace('%2e', '.', $sMacValue);
            $sMacValue = str_replace('%21', '!', $sMacValue);
            $sMacValue = str_replace('%2a', '*', $sMacValue);
            $sMacValue = str_replace('%28', '(', $sMacValue);
            $sMacValue = str_replace('%29', ')', $sMacValue);
                                
			$sMacValue = md5($sMacValue);

                $sMacValue = strtoupper($sMacValue);
        }  
        
        return $sMacValue ;
    }
    /**
    * 自訂排序使用
    */
    function merchantSort($a,$b)
    {
        return strcasecmp($a, $b);
    }
	
	function ServerPost($parameters ,$ServiceURL) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $ServiceURL);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);# test
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        $rs = curl_exec($ch);

        curl_close($ch);

        return $rs;
    }
	
	function disp_array($aData) {
		foreach ($aData as $sIdx => $sContent) {
			disp('\'' . $sIdx . '\' => \'' . $sContent . '\',');
		}
	}
	
	$sType = 'stage';
	// $sType = 'prod';
	// $sSDK_Type = 'old';
	$sSDK_Type = 'new';
	disp('Type: ' . $sType . ' - ' . $sSDK_Type);
	disp('<hr />');
	switch ($sType) {
		case 'stage':
			$sService_URL = 'https://payment-stage.allpay.com.tw/Cashier/QueryTradeInfo/v2';
			$sMid = '2000132';
			$sHash_Key = '5294y06JbISpM5x9';
			$sHash_IV = 'v77hoKGq4kWxNNIS';
			$sMerchant_Trade_No = '2016082306327';
			break;
		case 'prod':
			$sService_URL = 'https://payment.allpay.com.tw/Cashier/QueryTradeInfo/V2';
			$sMid = '1064068';
			$sHash_Key = 'kRCxusOEUr3PSF29';
			$sHash_IV = 'FQxU9TMsCkPCPOVV';
			$sMerchant_Trade_No = '2016082254513';
			break;
		default:
	}
	
	$aPost_Param = array(
		'MerchantTradeNo' => $sMerchant_Trade_No,
		'TimeStamp' => time(),
		'MerchantID' => $sMid,
	);
	ksort($aPost_Param);
	
	$aPost_Param['CheckMacValue'] = generate($aPost_Param, $sHash_Key, $sHash_IV);
	// echo 'aPost_Param<pre>'.print_r($aPost_Param, true).'</pre><br />';# test
	disp('aPost_Param');
	disp_array($aPost_Param);
	// echo genSimpleForm($sService_URL, $aPost_Param, '_blank');
	
	disp('<hr />');
	$sPost_Result = ServerPost($aPost_Param, $sService_URL);
	$sFeedback = array();
	parse_str($sPost_Result, $sFeedback);
	// echo 'sFeedback<pre>'.print_r($sFeedback, true).'</pre><br />';# test
	disp('sFeedback');
	disp_array($sFeedback);
	
    include('../../tpl/footer.php');
?>