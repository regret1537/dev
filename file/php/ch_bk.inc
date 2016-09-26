<?php
    include('ch_timeout.inc'); //是否timeout
    include('trainMsg.inc');

    // 載入測試資料
    $sTest_BK_NEC = '';
    if (TEST_MODE) {
        include('test_config.php');
        $sTest_Key = basename(__FILE__);
        if (isset($aTest_Params[$sTest_Key]['bkNEC'])) {
            $sTest_BK_NEC = $aTest_Params[$sTest_Key]['bkNEC'];
        }
    }

    if($submit){

        $link = mylink();

        function spas_idL($a){	//id補足10位後補空格
            $idal = strlen($a);
            if($idal<10){
                for($u=$idal;$u<10;$u++){
                    $a .= ' ';
                }
            }
            return($a);
        }

        //=========================//寫入查尋記錄
        $isday = date("Ymd");
        $istime = date("His");
        $goid = ($id1) ? spas_idL($id1) : spas_idL($id2) ;
        $gosn = ($go1sn) ? sprintf("%06s",$go1sn) : sprintf("%06s",$go2sn) ;
        $strgwp = "insert into return_search_log (aa,goid,gosn,isday,istime) values(0,AES_ENCRYPT('$goid','$key_str_ch'),'$gosn','$isday','$istime')";
        myquery($strgwp, $link);
        $aa = mysql_insert_id();
        //=============================//寫入查尋記錄end

        $go1sn = sprintf("%06s", $go1sn); 
        $id1 = spas_idL($id1);
        //=============取訂票資料PACKET NEC
        $str = "SELECT uorder.*,pack2,pack3,AES_DECRYPT(goid,'$key_str_ch') as goid,AES_DECRYPT(bkid,'$key_str_ch') as bkid FROM uorder where ";
        if($howgo==2){ 
            if(($id2)&&($go2sn)&&($id3)&&($go3sn)){
                $id2 = spas_idL($id2);
                $id3 = spas_idL($id3);
                $go2sn = sprintf("%06s", $go2sn); 
                $go3sn = sprintf("%06s", $go3sn); 
                $str .= " goid=AES_ENCRYPT('$id2','$key_str_ch') and bkid=AES_ENCRYPT('$id3','$key_str_ch') and gosn='$go2sn' and bksn='$go3sn' ";
                $txtnb = strlen($SendNEC);
            } else {
                //=========================//寫入查尋記錄log
                $Error_log_TXT = $language['_Alert_txt_031'];
                $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
                myquery($strgwp, $link);
                //=============================//寫入查尋記錄end
        ?>
            <script language="JavaScript">
                <!--
                alert('01<?php echo $language['_Alert_txt_031']; ?>');
                location.href="pay.php";
                // -->
            </script>
        <?php
                exit;
            }
        } else {
            $str .= " goid=AES_ENCRYPT('$id1','$key_str_ch') and gosn='$go1sn' ";
        }

        // 只搜尋2個月內的交易，測試環境不限日期
        if (!TEST_MODE) {
            $str .= " and tmstp>'$tmstpN'";
        }
        $chk_sql = $str;
        $str .= " and rclose!=0 limit 1 ";

        $qq = myquery($str, $link);
        $ax = @mysql_fetch_array($qq);
        $Cxc = explode('*', $ax['pack2']);

        // 檢查該筆交易是否退過款
        $chk_sql .= " and rcancel=1 limit 1 ";
        $chk_qry = myquery($chk_sql, $link);
        $chk_row_num = mysql_num_rows($chk_qry);

        $necpay = 0;// 退款不需送金額給NEC，將金額欄位清空
        if($ax['odnb'] and $chk_row_num < 1){
            if($howgo==2){ 
                $SendNEC='004A*'.$ax['odnb'].'*'.$id2.'*'.$go2sn.'*'.$id3.'*'.$go3sn.'*'.sprintf("%05s", $necpay).'*'.$ax['rtime'].'#'; 
            } else {
                $SendNEC='004A*'.$ax['odnb'].'*'.$id1.'*'.$go1sn.'*          *      '.'*'.sprintf("%05s", $necpay).'*'.$ax['rtime'].'#'; 
            }
            $txtnb = strlen($SendNEC);
        } else {
            //=========================//寫入查尋記錄log
            $Error_log_TXT = $language['_Alert_txt_028'];
            $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
            myquery($strgwp, $link);
            //=============================//寫入查尋記錄end
        ?>
            <script language="JavaScript">
                <!--
                alert('02<?php echo $language['_Alert_txt_028']; ?>');
                location.href="pay_bk.php";
                // -->
            </script>
        <?php
            exit;
        }
        
        // 記錄 004A 電文
        $sMasked_AAT_NEC_Stat = mask_nec_stat($SendNEC); // 機敏資料隱碼
        write_log('nec_trace_004', 'nec msg', array('SendNEC' => $sMasked_AAT_NEC_Stat));

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

        // 記錄 004B 電文
        $sMasked_AAT_NEC_Stat = mask_nec_stat($bkNEC); // 機敏資料隱碼
        write_log('nec_trace_004', 'nec msg', array('bkNEC' => $sMasked_AAT_NEC_Stat));

        //==========================================
        if(!$bkNEC){ //找不到
            //=========================//寫入查尋記錄log
            $Error_log_TXT = $language['_Alert_txt_031'];
            $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
            myquery($strgwp, $link);
            //=============================//寫入查尋記錄end
        ?>
            <script language="JavaScript">
                <!--
                alert('03<?php echo $language['_Alert_txt_031']; ?>');
                location.href="pay_bk.php";
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
        $CxcN = explode('*', $bkNEC);

        //=========================================登錄失敗
        if($CxcN[1]!='000'){
            $echoERRO = $trainMsg[$CxcN[1]];
            //=========================//寫入查尋記錄log
            $Error_log_TXT = $language['_Alert_txt_027'];
            $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
            myquery($strgwp, $link);
            //=============================//寫入查尋記錄end
        ?>
            <script language="JavaScript">
                <!--
                alert('04<?php echo $language['_Alert_txt_027']; ?>\n\n<?php echo $echoERRO; ?>!');
                location.href="pay_bk.php";
                // -->
            </script>
        <?php
            exit;
        }

        if(($VCAS!='#')||($CxcN[0]!='004B')){
            //=========================//寫入查尋記錄log
            $Error_log_TXT = $language['_Alert_txt_031'];
            $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
            myquery($strgwp, $link);
            //=============================//寫入查尋記錄end
        ?>
            <script language="JavaScript">
                <!--
                alert('05<?php echo $language['_Alert_txt_031']; ?>');
                location.href="pay_bk.php";
                // -->
            </script>
        <?php
            exit;
        }

        $_SESSION[$sess_USER]['uaa'] = $ax['aa'];	// 記錄資料序號
        $_SESSION[$sess_USER]['bspay'] = $CxcN[6];	// 記錄退票手續費
        $_SESSION[$sess_USER]['bamount'] = $CxcN[7];	// 記錄退票總金額(不含手續費，含便當)
        //=========================================
    } else {
        //=========================//寫入查尋記錄log
        $Error_log_TXT = $language['_Alert_txt_031'];
        $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
        myquery($strgwp, $link);
        //=============================//寫入查尋記錄end
?>
        <script language="JavaScript">
            <!--
            alert('06<?php echo $language['_Alert_txt_031']; ?>');
            location.href="pay_bk.php";
            // -->
        </script>
<?php
        exit;
    }
?>