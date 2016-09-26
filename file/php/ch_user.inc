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
    
    
    // 查詢訂票資料
    $stopIDlock = array('N224584687','G120106742');
    $Stpcn = count($stopIDlock);
    for($i=0;$i<$Stpcn;$i++){
        if(($id1==$stopIDlock[$i])||($id2==$stopIDlock[$i])||($id3==$stopIDlock[$i])){
?>
    <script language="JavaScript">
        <!--
        alert('01<?=$language['_Alert_txt_031']?>');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
        }
    }

    include('ch_timeout.inc'); //是否timeout
    include('trainMsg.inc');

    if($submit){
        function spas_idL($a){	//id補足10位後補空格
            $idal = strlen($a);
            if($idal<10){
                for($u=$idal;$u<10;$u++){
                    $a .= ' ';
                }
            }
            return($a);
        }

        $go1sn = sprintf("%06s", $go1sn); 
        $id1 = spas_idL($id1);
        
        //=============取訂票資料PACKET NEC
        if($howgo==2){ 
            if(($id2)&&($go2sn)&&($id3)&&($go3sn)){
                $id2 = spas_idL($id2);
                $id3 = spas_idL($id3);
                $go2sn = sprintf("%06s", $go2sn); 
                $go3sn = sprintf("%06s", $go3sn); 
                $SendNEC = '001A*' . $id2 . '*' . $go2sn . '*' . $id3 . '*' . $go3sn . '#';
                $txtnb = strlen($SendNEC);
            } else {
?>
    <script language="JavaScript">
        <!--
        alert('02<?=$language['_Alert_txt_026']?>');
        location.href="pay.php";
        // -->
    </script>
<?php
                exit;
            }
        } else {
            $SendNEC = '001A*' . $id1 . '*' . $go1sn . '*          *      #';
            $txtnb = strlen($SendNEC);
        }
        
        // 記錄 001A 電文
        $sMasked_AAT_NEC_Stat = mask_nec_stat($SendNEC); // 機敏資料隱碼
        write_log('nec_trace_001', 'nec msg', array('SendNEC' => $sMasked_AAT_NEC_Stat));

        //==========================================
        if (TEST_MODE and $sTest_BK_NEC != '') {
            // 測試資料
            $bkNEC = $sTest_BK_NEC;
        } else {
            $fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout); //網址IP,PORT,回應代碼,回應訊息,等待時間
            if($fp){
                $goNEC = @fwrite($fp, $SendNEC, $txtnb);
                $bkNEC = @fread($fp, $FreadNB);
                fclose($fp);
            }
        }
        
        // 記錄 001B 電文
        $sMasked_AAT_NEC_Stat = mask_nec_stat($bkNEC); // 機敏資料隱碼
        write_log('nec_trace_001', 'nec msg', array('bkNEC' => $sMasked_AAT_NEC_Stat));
        
        if(!$bkNEC){ //找不到
?>
    <script language="JavaScript">
        <!--
        alert('03<?=$language['_Alert_txt_026']?>');
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

        //=========================================登錄失敗
        if($Cxc[1]!='000'){
            $echoERRO = $trainMsg[$Cxc[1]].'('.$Cxc[1].')';
?>
    <script language="JavaScript">
        <!--
        alert('04<?=$language['_Alert_txt_027']?>\n\n<?=$echoERRO?>!');
        location.href="pay.php";
        // -->
    </script>
<?php
            exit;
        }

        if(($VCAS!='#')||($Cxc[0]!='001B')){ 
?>
    <script language="JavaScript">
        <!--
        alert('05<?=$language['_Alert_txt_026']?>');
        location.href="pay.php";
        // -->
    </script>
<?php
            exit;
        }

        //範例 001B*000*1*D201203191*972233*20120325*1337*516  *6*185*149*02*1*          *      *        *    *     * *000*000*00* *D* #

        $_SESSION[$sess_USER]['pk1'] = $bkNEC;	//記錄資料
        //=========================================
    } else {
?>
    <script language="JavaScript">
        <!--
        alert('06<?=$language['_Alert_txt_026']?>');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
    }
?>