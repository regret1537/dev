<?php
/**
 * Line 相關功能 class
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @version    1.0
 */
    class cls_line {
		const LINE_SERVER_IP = '10.5.0.131';
		const LINE_SERVICE_PORT = '17879';
		
		private $aLine_Group = array('1' => '台鐵訊息', '4' => 'ALLPAY ALERT');
		
        function __construct() {}
		
		/**
		 *  產生 Line 用訊息
		 *      格式: [message] by [PHP name]
		 *
		 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
		 * @category   line
		 * @param      String $sMessage 原始訊息
		 * @param      String $sPHP PHP 名稱
		 * @return     String
		 * @version    1.0
		 */
		private function gen_line_msg($sMessage, $sPHP_Name) {
			$sLine_Message = $sMessage . ' by ' . $sPHP_Name;
			return $sLine_Message;
		}
		
		/**
		 *  發送 Line 訊息
		 *
		 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
		 * @category   line
		 * @param      String $sGroup_Code Line 群組代碼
		 * @param      String $sMessage 原始訊息
		 * @param      String $sPHP PHP 名稱
		 * @return     Boolean
		 * @version    1.0
		 */
		function send_line($sGroup_Code, $sMessage, $sPHP_Name) {
			$sLine_Group = '';
			$sLine_Message = '';
			
			// Line 群組設定
			if (isset($this->aLine_Group[$sGroup_Code])) {
				$sLine_Group = $this->aLine_Group[$sGroup_Code];
			} else {
				$sLine_Group = $this->aLine_Group['4'];
			}
			
			// 產生 Line 訊息
			$sLine_Message = $this->gen_line_msg($sMessage, $sPHP_Name);
			
			if($sLine_Group != '' and $sLine_Message != '' and $sPHP_Name != '') {
				echo $sLine_Message . PHP_EOL;
				       
				$iLine_Server_Timeout = 120; // Line Server 等待時間
				$iWrite_Len_Limit = 300; // 傳送長度限制
				$iRead_Len_Limit = 100; // 接收長度限制
				$sHostname = gethostname(); // 本機 Server 名稱
				
				// 發送 Line 訊息
				$sSendStat = '[MSN=*LINE*' . $sLine_Group . ', ]' . $sHostname . ', ' . $sLine_Message . ' ('.date('Y-m-d').') ' . "\r\n";
				$rFile_Pointer = fsockopen(self::LINE_SERVER_IP, self::LINE_SERVER_PORT, $iError_NO, $sError_Str, $iLine_Server_Timeout);
				if($rFile_Pointer){
					$iWrite_Res = @fwrite($rFile_Pointer, mb_convert_encoding($sSendStat, 'Big5', 'UTF-8'), $iWrite_Len_Limit);
					$sRead_Res = @fread($rFile_Pointer, $iRead_Len_Limit);
					fclose($rFile_Pointer);
				} else {
					return false;
				}
				return true;
			} else {
				return false;
			}
		}
    }
?>