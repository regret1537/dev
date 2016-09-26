<?php
/**
 * ECPay 合併新 ECPay 請款檔
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @filesource ./g_common.inc
 * @version    1.0.3
 */
 
	include('g_common.inc');
	
	function disp($cont) {
		if (gettype($cont) != 'array') {
			echo $cont . PHP_EOL;
		} else {
			echo print_r($cont, true) . PHP_EOL;
		}
	}
	
	$sFile_Date = $hohsnYMD; // 檔案日期
	
	// $sRoot_Dir_Path = '/vhost/bk-file'; // 根目錄路徑
	$sRoot_Dir_Path = '/home/vagrant/Code/dev/shell/test'; // 根目錄路徑# test
	$sOut_Dir_Path = $sRoot_Dir_Path . '/ctcb'; // 請款檔目錄路徑
	$sOut_Zip_Name = 'grni_n.zip'; // 請款檔(zip)檔名
	$sOut_Zip_Path = $sOut_Dir_Path . '/' . $sOut_Zip_Name; // 請款檔(zip)路徑
	$sOut_Zip_Pwd = 'ctcbgw88'; // 請款檔(zip)密碼
	$sOut_Txt_Name = 'grni_n.txt'; // 請款檔(txt)檔名
	$sOut_Txt_Path = $sOut_Dir_Path . '/' . $sOut_Txt_Name; // 請款檔(txt)路徑
	$sBK_Dir_Path = $sRoot_Dir_Path . '/ctcb_back'; // 備份目錄路徑
	$sBK_Zip_Path = $sBK_Dir_Path . '/grni_n_original_' . $sFile_Date . '.zip'; // 備份原始請款檔(zip)路徑
	$sBK_Txt_Path = $sBK_Dir_Path . '/grni_n_combined_' . $sFile_Date . '.txt'; // 備份新請款檔(txt)路徑
	$sDotnet_Dir_Path = $sRoot_Dir_Path . '/ctcb_dotnet'; // .net請款檔目錄路徑
	$sDotnet_Txt_Name = 'dotnet_ctcb_' . $sFile_Date . '.txt'; // .net請款檔檔名
	$sDotnet_Txt_Path = $sDotnet_Dir_Path . '/' . $sDotnet_Txt_Name; // .net請款檔路徑
	
	// 原始請款檔空檔HEADER
	$sEmpty_Header = '80711130130004079000000010000000001000000000000000000 GWECPAY                                                                                                   ' . "\r\n";
	$sDotnet_Empty_Content = str_repeat(',', 10); // .net空檔內容
	$sNew_Out_Txt_Content = ''; // 新請款檔內容
	$iNew_Out_Rows_Num = 0; // 新請款檔行數
	$iOrg_Out_Rows_Num = 0; // 原始請款檔行數(扣除TRAILER)
	$iDotnet_Out_Rows_Num = 0; // .net請款檔行數
	$aTmp_Dotnet_Data = array(); // .net請款檔暫存內容
	$sDotnet_Out_Txt_Content	= ''; // .net請款檔內容
	$iHeader_Trigger = 30; // 同家廠商超過N筆數則做小計
	$sResult_Log = ''; // 合併結果(Log)
	$sResult_Line_Message = ''; // 合併結果(Line訊息)
	$sPHP_Name = basename(__FILE__);
	$sLine_Group_Code = '1'; // Line群組代碼
	$sLine_Message = ''; // Line訊息
	$sFull_Line_Message = ''; // 完整Line訊息
	
	// Log訊息列表
	$aLog_Message = array(
		'0' => $sOut_Zip_Path . ' created',
		'1' => $sOut_Zip_Path . ' does not exist',
		'2' => 'Unzip ' . $sOut_Zip_Path . ' to ' . $sOut_Dir_Path . ' failed',
		'3' => 'Move ' . $sOut_Zip_Path . ' to ' . $sBK_Zip_Path . ' failed',
		'4' => $sOut_Txt_Path . ' is empty',
		'5' => 'Remove ' . $sOut_Txt_Path . ' failed',
		'6' => 'HEADER format error',
		'7' => 'TRAILER format error',
		'8' => $sDotnet_Txt_Path . ' does not exist',
		'9' => $sDotnet_Txt_Path . ' is empty',
		'10' => $sOut_Txt_Path . ' create failed',
		'11' => 'Zip ' . $sOut_Txt_Path . ' to ' . $sOut_Zip_Path . ' failed',
		'12' => 'Move ' . $sOut_Txt_Path . ' to ' . $sBK_Txt_Path . ' failed',
		'13' => $sBK_Txt_Path . ' encrypt failed',
		'14' => 'Remove ' . $sDotnet_Txt_Path . ' failed',
	);
	
	// Line訊息列表
	$aLine_Message = array(
		'0' => '合併請款檔建立完成',
		'1' => '原始請款檔不存在',
		'2' => '原始請款檔解壓縮失敗',
		'3' => '原始請款檔搬移失敗',
		'4' => '原始請款檔無內容',
		'5' => '原始請款檔刪除失敗',
		'6' => '原始請款檔格式錯誤(HEADER)',
		'7' => '原始請款檔格式錯誤(TRAILER)',
		'8' => '新綠界請款檔不存在',
		'9' => '新綠界請款檔無內容',
		'10' => '合併請款檔產生失敗',
		'11' => '合併請款檔壓縮失敗',
		'12' => '合併請款檔備份失敗',
		'13' => '備份合併請款檔加密失敗',
		'14' => '新綠界請款檔刪除失敗',
	);
	$sGW_Mail_Group = 'sys-error@greenworld.com.tw'; // 新綠界告警群組mail
	
	try {
		// 檢查原始請款檔(zip)是否存在
		if (!file_exists($sOut_Zip_Path)) {
			throw new Exception('1');
		}
		
		// 將原始請款檔(zip)解壓縮
		exec('/usr/bin/unzip -o -P ' . $sOut_Zip_Pwd . ' -j ' . $sOut_Zip_Path . ' -d ' . $sOut_Dir_Path, $aTmp_Output, $iTmp_Code);
		if ($iTmp_Code != 0) {
			disp('Execution return: ' . $iTmp_Code);
			disp('Execution output:');
			disp($aTmp_Output);
			throw new Exception('2');
		}
		unset($aTmp_Output, $iTmp_Code);
		
		// 讀取原始請款檔(txt)
		$aRead_Rows = file($sOut_Txt_Path);
		if ($aRead_Rows === false) {
			throw new Exception('4');
		}
		
		// 刪除原始請款檔(txt)
		$bResult = unlink($sOut_Txt_Path);
		if ($bResult === false) {
			throw new Exception('5');
		}
		unset($bResult);
		
		// 簡易HEADER格式檢查
		$sHeader_Mark = substr($aRead_Rows[0], 0, 1);
		if ($sHeader_Mark != '8') {
			throw new Exception('6');
		}
		unset($sHeader_Mark);
		
		// 簡易TRAILER格式檢查
		$sTrailer_Mark = substr($aRead_Rows[count($aRead_Rows) - 1], 0, 1);
		if ($sTrailer_Mark != '9') {
			throw new Exception('7');
		}
		unset($sTrailer_Mark);
		
		// 檢查原始請款檔(txt)是否為空檔(無效請款內容)
		if ($aRead_Rows[0] != $sEmpty_Header) {
			$sDeprecate_Trailer = array_pop($aRead_Rows); // 原始TRAILER(未使用)
			$iOrg_Out_Rows_Num = count($aRead_Rows); // 原始請款檔行數
			$sNew_Out_Txt_Content = implode('', $aRead_Rows); // 新請款檔
		}
		unset($sDeprecate_Trailer, $aRead_Rows);
		
		// 檢查.net請款檔是否存在
		if (!file_exists($sDotnet_Txt_Path)) {
			throw new Exception('8');
		}
		
		// 讀取.net請款檔
		$aRead_Rows = file($sDotnet_Txt_Path);
		if ($aRead_Rows === false) {
			throw new Exception('9');
		}
		
		// 刪除.net請款檔
		$bResult = unlink($sDotnet_Txt_Path);
		if ($bResult === false) {
			throw new Exception('14');
		}
		unset($bResult);
		
		// 若.net請款檔不為空檔才組合.net請款檔
		if (trim($aRead_Rows[0]) != $sDotnet_Empty_Content) {
			// 取得.net請款檔暫存內容
			foreach ($aRead_Rows as $sTmp_Row) {
				list($sFull_MID, $sWebname_01, $sWebname_02, $sProcdt, $sAuthsr, $sCdno, $sAuth, $sAmount, $sClient, $sECI, $sTID) = explode(',', trim($sTmp_Row));
				
				$sMID = substr($sFull_MID, 3, 10); // 商店代號
				$sBill_Bame = ($sWebname_01 === '') ? 'gw-merchant' : $sWebname_01 ; // 商店名稱，沒有名稱則帶入預設值
				
				// 交易代碼(正數請款 40，負數退款 41)
				$sTrade_Code = '40'; // 交易代碼
				if (intval($sAmount) > 0) {
					$sTrade_Code = '40';
					$nAmount = $sAmount * 1; // 轉數值
				} else {
					$sTrade_Code = '41';
					$nAmount = $sAmount * -1; // 轉數值，負數轉正
				}
				
				// 卡號合併與整理 過濾掉空格
				$sCdno = str_replace(' ', '', $sCdno);
				$sCdno = str_pad($sCdno, 16, '0', STR_PAD_LEFT); //補足16位數 左區補0
				
				// ECI整理
				$sECI = (int) $sECI;
				$sECI = (empty($sECI)) ? 7 : $sECI;
				
				if($sAuth != '') {
					$aTmp_Dotnet_Data[$sClient][] = array(
						'mid' => $sMID,
						'procdt' => $sProcdt,
						'cdno' => $sCdno,
						'neg' => $sTrade_Code,
						'auth' => $sAuth,
						'amount' => $nAmount,
						'bill_name' => $sBill_Bame,
						'eci' => $sECI,
						'tid' => $sTID,
						'client' => $sClient
					);
				}
			}
			
			// 組合.net請款檔
			foreach($aTmp_Dotnet_Data as $sTmp_Client => $aTmp_Client_Data) {
				$sTmp_Out_Detail = '';
				$iTmp_Trade_Count = 0; // 交易筆數
				$iTmp_Total_Trade_Count = 0; // 交易總數
				$iTmp_Client_Trade_Num = count($aTmp_Client_Data);
				
				$iPay_Count = 0; // 請款筆數
				$iPay_Back_Count = 0; // 退款筆數 
				$iPay_Amount = 0; // 請款金額
				$iPay_Back_Amount = 0; // 退款金額
				
				foreach ($aTmp_Client_Data as $aTmp_Client_Row) {
					$iTmp_Trade_Count++; // 交易筆數累計
					$iTmp_Total_Trade_Count++; // 交易總數累計
					
					// DETAIL
					$aDetail_Data = array(
						'procdt' => $aTmp_Client_Row['procdt'],
						'cdno' => $aTmp_Client_Row['cdno'],
						'neg' => $aTmp_Client_Row['neg'],
						'auth' => $aTmp_Client_Row['auth'],
						'amount' => $aTmp_Client_Row['amount'],
						'bill_name' => $aTmp_Client_Row['bill_name'],
						'eci' => $aTmp_Client_Row['eci'],
						'tid' => '',// 暫時不帶，與ECPay統一，dotnet是帶tid
						'sub_mid' => ''// 暫時不帶，與ECPay統一，dotnet是帶client
					);
					$sTmp_Detail = gen_detail($aDetail_Data);
					$sTmp_Out_Detail .= $sTmp_Detail;
					$iDotnet_Out_Rows_Num++;
					unset($sTmp_Detail);
					
					// 金額小計加總
					if($aTmp_Client_Row['neg'] === '40') {
						// 請款
						$iPay_Count++;
						$iPay_Amount = $iPay_Amount + $aTmp_Client_Row['amount'];
					} else  {
						// 退款
						$iPay_Back_Count++;
						$iPay_Back_Amount = $iPay_Back_Amount + $aTmp_Client_Row['amount'];
					}
					
					// 30筆為一個小計加總 或小於30筆以內跑完
					if($iTmp_Trade_Count === $iHeader_Trigger or $iTmp_Total_Trade_Count === $iTmp_Client_Trade_Num) {
						// HEADER
						$aHeader_Data = array(
							'trd_date' => date('ymd'),
							'mid' => $aTmp_Client_Row['mid'],
							'pay_count' => $iPay_Count,
							'pay_amount' => $iPay_Amount,
							'pay_back_count' => $iPay_Back_Count,
							'pay_back_amount' => $iPay_Back_Amount,
							'bill_name' => $aTmp_Client_Row['bill_name'],
						);
						$sTmp_Out_Header = gen_header($aHeader_Data);
						$sDotnet_Out_Txt_Content .= $sTmp_Out_Header . $sTmp_Out_Detail;
						$iDotnet_Out_Rows_Num++;
						
						// 變數重置
						$iPay_Count = 0; // 請款筆數歸0
						$iPay_Back_Count = 0; // 退款筆數歸0
						$iPay_Amount = 0; // 請款金額歸0
						$iPay_Back_Amount = 0; // 退款金額歸0
						$iTmp_Trade_Count = 0; // 30筆交易歸0
						
						$sTmp_Out_Header = ''; // 清空暫存HEADER
						$sTmp_Out_Detail = ''; // 清空暫存DETAIL
						
					}
				}
			}
			unset($aTmp_Dotnet_Data);
		}
		
		// 總行數統計(需包含TRAILER那一行，所以先加1)
		$iNew_Out_Rows_Num = $iOrg_Out_Rows_Num + $iDotnet_Out_Rows_Num + 1;
		
		if ($iNew_Out_Rows_Num === 1) {
			// 若總行數為1表示ECPay與.net都無交易，產生空檔上傳
			$sNew_Out_Txt_Content  = gen_header();
			$sNew_Out_Txt_Content .= gen_detail();
			$sNew_Out_Txt_Content .= gen_trailer();
		} else {
			// 產生TRAILER
			$sTmp_Trailer = gen_trailer($iNew_Out_Rows_Num);
			
			// 組合新請款檔
			$sNew_Out_Txt_Content .= $sDotnet_Out_Txt_Content . $sTmp_Trailer;
			unset($sTmp_Trailer);
		}
		
		// 產生新請款檔
		$iResult = file_put_contents($sOut_Txt_Path, $sNew_Out_Txt_Content);
		if ($iResult === false) {
			throw new Exception('10');
		}
		
		// 移動原始請款檔(zip)
		$bResult = rename($sOut_Zip_Path, $sBK_Zip_Path);
		if ($bResult === false) {
			throw new Exception('3');
		}
		unset($bResult);
		
		// 壓縮新請款檔
		exec('/usr/bin/zip -P ' . $sOut_Zip_Pwd . ' -j ' . $sOut_Zip_Path . ' ' . $sOut_Txt_Path, $aTmp_Output, $iTmp_Code);
		if ($iTmp_Code != 0) {
			disp('Execution return: ' . $iTmp_Code);
			disp('Execution output:');
			disp($aTmp_Output);
			throw new Exception('11');
		}
		unset($aTmp_Output, $iTmp_Code);
		$sResult_Log = $aLog_Message['0'];
		$sLine_Message = $aLine_Message['0'];
		
		// 移動新請款檔(txt)
		$bResult = rename($sOut_Txt_Path, $sBK_Txt_Path);
		if ($bResult === false) {
			throw new Exception('12');
		}
		unset($bResult);
		
		// 備份新請款檔(txt)加密
        $iResult = FileEncrypt($sBK_Txt_Path, 'encrypt');
		if ($iResult === false) {
			throw new Exception('13');
		}
		unset($iResult);
	} catch (Exception $e) {
		$sException_Code = $e->getMessage();
		$sResult_Log = 'Exception:' . $aLog_Message[$sException_Code];
		$sLine_Message = $aLine_Message[$sException_Code];
		unset($sException_Code);
	}
	disp($sResult_Log);
	
	// Line 通知
	$sFull_Line_Message = gen_line_msg($sLine_Message, $sPHP_Name);
	$bLine_Result = exec_line($sLine_Group_Code, $sFull_Line_Message, $sPHP_Name);
	if ($bLine_Result === false) {
		disp('Send line failed');
	} else {
		disp('Send line succeed');
	}
	
	// Mail 通知
	$sMail_Group = $Rootmail . ',' . $sGW_Mail_Group;
	$sMail_Subject = 'ECPay-新綠界中信一般請款檔合併';
	$sMail_Body = $sResult_Log;
	$sMail_Header = "From:ecpay@sunup.net\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit";
	$bMail_Result = mail($sMail_Group, $sMail_Subject, $sMail_Body, $sMail_Header);
	if ($bMail_Result === false) {
		disp('Send mail failed');
	} else {
		disp('Send mail succeed');
	}
?>