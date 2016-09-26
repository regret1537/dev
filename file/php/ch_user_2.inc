<?php
    // 載入測試資料
    $sTest_BK_NEC = '';
    if (TEST_MODE) {
        include('test_config.php');
        $sTest_Key = basename(__FILE__);
        if (isset($aTest_Params[$sTest_Key]['bkNEC'])) {
            $sTest_BK_NEC = $aTest_Params[$sTest_Key]['bkNEC'];
        }
    }

    include('ch_timeout.inc'); //是否timeout
    include('trainMsg.inc');
	
	$sPHP_Name = basename(__FILE__, '.php'); // PHP 名稱
	
    //==========================================
    if(!$_SESSION[$sess_USER]['pk1']){ //未登錄
?>
    <script language="JavaScript">
        <!--
        alert('01資料存取錯誤!!請關閉所有流覽器視窗!重新來過!!');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
    }
    //==========================================

    $USER_arr = explode('*',$_SESSION[$sess_USER]['pk1']); //取臺資料
    $bento_info = array(
        array('num' => $USER_arr[27]),// 去程葷便當
        array('num' => $USER_arr[28]),// 去程素便當
        array('num' => $USER_arr[29]),// 回程葷便當
        array('num' => $USER_arr[30]),// 回程素便當
    );
    

    //==========================================
    function nb2ttt($a){
        if($a==''){
            $a='  ';
        } else {
            $a = sprintf("%02d", $a); 
        }
        return($a);
    }
    function idspch($a){
        if($a==''){ $a='          '; }
        return($a);
    }
    function compnbch($a){
        if($a==''){ $a='      '; }
        return($a);
    }
    function bentoNum($num) {
        if (empty($num)) {
            return ' ';
        } else {
            return $num;
        }
    }
    //=============取訂票資料PACKET NEC
    $SendNEC='002A*'.idspch($USER_arr[3]).'*'.compnbch($USER_arr[4]).'*'.idspch($USER_arr[13]).'*'.compnbch($USER_arr[14]).'*'.nb2ttt($goA).'*'.nb2ttt($goB).'*'.nb2ttt($goC).'*'.nb2ttt($goD).'*'.nb2ttt($bkA).'*'.nb2ttt($bkB).'*'.nb2ttt($bkC).'*'.nb2ttt($bkD).'*'.bentoNum($bento_info[0]['num']).'*'.bentoNum($bento_info[1]['num']).'*'.bentoNum($bento_info[2]['num']).'*'.bentoNum($bento_info[3]['num']).'#'; 
    $txtnb = strlen($SendNEC);

    // 記錄 002A 電文
    $sMasked_AAT_NEC_Stat = mask_nec_stat($SendNEC); // 機敏資料隱碼
    write_log('nec_trace_002', 'nec msg', array('SendNEC' => $sMasked_AAT_NEC_Stat));
    
    if (TEST_MODE and $sTest_BK_NEC != '') {
        // 測試資料
        $bkNEC = $sTest_BK_NEC;
    } else {
        $fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout); //網址IP,PORT,回應代碼,回應訊息,等待時間
        if($fp){
            $goNEC = @fwrite($fp, $SendNEC, $txtnb);
            $bkNEC = @fread($fp, $FreadNB);
            fclose($fp);
        } else {
			$sLine_Message = $sockMachineName . ' , 台鐵 AAT 無法連線';
			// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);
        }
    }
    
    // 記錄 002B 電文
    $sMasked_AAT_NEC_Stat = mask_nec_stat($bkNEC); // 機敏資料隱碼
    write_log('nec_trace_002', 'nec msg', array('bkNEC' => $sMasked_AAT_NEC_Stat));
    
    $hohsnYMD = date("Ymd");

    //==========================================
    if(!$bkNEC){ //找不到
        $isok = 0;
        //=========================//先記錄批價查票資料
        $str = "insert into askdt (aa,sendnec,rtnec,yyyymmdd,isok) values(0,'$SendNEC','$bkNEC','$hohsnYMD','$isok')";
        myquery($str, $link);
?>
    <script language="JavaScript">
        <!--
        alert('02資料存取錯誤!!請關閉所有流覽器視窗!重新來過!!');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
    }

    //===================  我這邊 connections 滿了時會回傳給你 944X*#
    include('ch_944X.php');


    //=========================================沒#資料不全
    $bctnb = strlen($bkNEC); $bctnb=$bctnb-1;
    $VCAS=substr($bkNEC, $bctnb,1);

    $bkNEC = str_replace('#','',$bkNEC);
    $Cxc = explode('*', $bkNEC);

    $echoERRO = $trainMsg[$Cxc[1]];

    if(($VCAS!='#')||($Cxc[0]!='002B')){ 
        $isok = 0;
        //=========================//先記錄批價查票資料
        $str = "insert into askdt (aa,sendnec,rtnec,yyyymmdd,isok) values(0,'$SendNEC','$bkNEC','$hohsnYMD','$isok')";
        myquery($str, $link);
?>
    <script language="JavaScript">
        <!--
        alert('03資料存取錯誤!!請關閉所有流覽器視窗!重新來過!!\n\n可能原因:\n\n<?php echo $echoERRO; ?>!');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
    }

    //=========================================登錄失敗
    if($Cxc[1]!='000'){
        $isok = 0;
        //=========================//先記錄批價查票資料
        $str = "insert into askdt (aa,sendnec,rtnec,yyyymmdd,isok) values(0,'$SendNEC','$bkNEC','$hohsnYMD','$isok')";
        myquery($str, $link);
?>
    <script language="JavaScript">
        <!--
        alert('04登入失敗!請重新登入!\n\n可能原因:\n\n<?php echo $echoERRO; ?>!');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
    }

    $isok = 1;
    //=========================//先記錄批價查票資料
    $str = "insert into askdt (aa,sendnec,rtnec,yyyymmdd,isok) values(0,'$SendNEC','$bkNEC','$hohsnYMD','$isok')";
    myquery($str, $link);

    $_SESSION[$sess_USER]['pk2'] = $bkNEC;	//記錄資料
    $_SESSION[$sess_USER]['ppp'] = $goA.'*'.$goB.'*'.$goC.'*'.$goD.'*'.$bkA.'*'.$bkB.'*'.$bkC.'*'.$bkD;	//定票張數記錄資料
?>