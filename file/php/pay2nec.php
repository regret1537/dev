<?php
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    # 每天 01:00 分執行平帳
    include('mysql.inc');
	
	$sPHP_Name = basename(__FILE__, '.php'); // PHP 名稱

    $log_table = 'nec_trace_log';
    $log_subject = 'daily_balance';
    $self_name = basename(__FILE__);
    $log_value = array(
        'file_name' => $self_name,
        'send_nec' => '',
        'bk_nec' => '',
        'msg' => ''
    );
    
    function nbok($a){
        $a = $a + 1 - 1;
        return($a);
    }

    $link = mylink();

    # 補關日期確認
    if(($sy)&&($sm)&&($sd)){
        $bksand=1;
    } else {
        $bksand='';
    }

    function spas_nabD($a,$b){
        $aa = "%0".$b."s";
        $a = sprintf($aa, $a); 
        return($a);
    }

    if($bksand){
        if($CPS!='jmwang'){	//密碼不對
?>
    <script language="JavaScript">
        <!--
        alert('密碼錯誤補送平帳失敗');
        location.href="bksand.php";
        // -->
    </script>
<?php
        exit;
        }
	
        if(!$Tsn) $Tsn=1;
        $lodday = date($sy."/".$sm."/".$sd); //指定日期
        $stime_tt = '0000'.$Tsn.'0';	//檔頭用
    } else {
        $MKtime= mktime(0,0,0,date("m"),date("d")-1,date("Y")); 
        $lodday= date("Y/m/d",$MKtime); //前一天的日期
        $stime_tt = '000000';	//檔頭用
    }
    $stime = '000000';	
    $etime = '235959';

    //=====刪除重覆的 授權碼 & eci 為空的資料
    $strck = "SELECT aa,odnb,amount FROM uorder where authnb='' and eci='' and rtime like '$lodday%' and rclose!='0' ";
    $qqck = myquery($strck, $link);
    if (mysql)
    while($ly = @mysql_fetch_array($qqck)){
        //====找出 授權碼 & eci 為真正確資料, 如果有就刪除重覆的 授權碼 & eci 為空的資料
        $strckA = "SELECT aa,odnb,amount FROM uorder where authnb!='' and eci!='' and rtime like '$lodday%' and rclose!='0' and odnb='$ly[odnb]' and amount='$ly[amount]' ";
        $qqckA = myquery($strckA, $link);
        while($lyA = @mysql_fetch_array($qqckA)){
            if($lyA[aa]){
                $aoo++;
                $del_data .= "($aoo) 單號:".$ly[odnb]."金額:".$ly[amount].', ';
                $strpm_del = "delete FROM uorder where aa='$ly[aa]' and eci='' and authnb='' and odnb='$ly[odnb]' and amount='$ly[amount]' limit 1 ";
                myquery($strpm_del, $link);
            }
        } 
    }

    if($del_data){
		$sLine_Message = $sockMachineName . ' , 台鐵重複調整通知 ' . $del_data;
		// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);
    }
    if (TEST_MODE) {
        $lodday = $argv[1];
    }
    
    # 取得授權單號
    $sel_sql = "SELECT gwpaysn FROM uorder WHERE LEFT(rtime, 10) = '$lodday' ORDER BY aa ";
    $sel_qry = myquery($sel_sql, $link);
    $pay_sn_ary = array();
    while ($sel_ary = @mysql_fetch_array($sel_qry)) {
        array_push($pay_sn_ary, $sel_ary['gwpaysn']);
    }
    mysql_free_result($sel_qry);

    if (empty($pay_sn_ary)) {
        # 記錄異常執行結果
        $log_value['msg'] = $lodday . ' no trade.';
        DBLog($log_table, $log_subject, $log_value);
        die('fail');
    }
    
    # 取得票務資料
    $pay_sn_list = implode(',', $pay_sn_ary);
    $sel_sql = "SELECT uorder.*,pack2,pack3,AES_DECRYPT(goid,'$key_str_ch') as goid,AES_DECRYPT(bkid,'$key_str_ch') as bkid FROM uorder WHERE gwpaysn IN ($pay_sn_list) AND LEFT(rtime, 10) = '$lodday' ORDER BY aa ";
    $sel_qry = myquery($sel_sql, $link);
    $tck_info = array();
    while ($sel_ary = @mysql_fetch_array($sel_qry)) {
        array_push($tck_info, $sel_ary);
    }
    mysql_free_result($sel_qry);

    # 取得便當資料
    $bento_info = array();
    $sel_sql = "SELECT gw_pay_sn,go_nv_ben_num, go_nv_ben_total, go_v_ben_num, go_v_ben_total, bk_nv_ben_num, bk_nv_ben_total, bk_v_ben_num, bk_v_ben_total FROM " . TB_BENTO . " WHERE gw_pay_sn IN ($pay_sn_list) ORDER BY sn ";
    $sel_qry = myquery($sel_sql, $link);
    while ($sel_ary = @mysql_fetch_array($sel_qry)) {
        $bento_info[$sel_ary['gw_pay_sn']] = $sel_ary;
    }
    mysql_free_result($sel_qry);
    
    # 取得BIN表
    $sel_sql = "SELECT cardno,cardtype FROM " . TB_BIN . " ORDER BY aa ";
    $sel_qry = myquery($sel_sql, $link);
    $bin_list = array();
    while ($sel_ary = @mysql_fetch_array($sel_qry)) {
        $bin_list[$sel_ary['cardno']] = $sel_ary['cardtype'];
    }
    mysql_free_result($sel_qry);

    $outTxt = '';
    $counp=0;   # 付款交易筆數(6)
    $counpc=0;	# 取消交易筆數(6)
    $amountc=0;	# 取消刷卡總金額(9)
    $spay = 0;	# 語音手續費(7)
    $bspay = 0; # 刷卡退票手續費(7)

    # 台/森鐵請款淨額(1:一般台鐵/2:阿里山森鐵)
    $railway_total = array(
        's' => array('1' => '', '2' => ''),
        'e' => array('1' => '', '2' => ''),
    );

    # 台/森鐵手續費發票金額(1:一般台鐵/2:阿里山森鐵)
    $railway_spay_total = array(
        's' => array('1' => '', '2' => ''),
        'e' => array('1' => '', '2' => ''),
    );

    # 便當請款淨額
    $bento_total = array('s' => '', 'e' => '');

    # 便當手續費發票金額(便當無手續費，空白)
    $bento_spay_total = array('s' => '', 'e' => '');

    # 信用卡類別(s:本行/e:他行)
    $c_t_code = array('s', 'e');# 卡別代碼
    $amount = 0;
    $trd_num = 0;
    foreach ($tck_info as $ax) {
        $t_pay_sn = $ax['gwpaysn'];# 授權單號
        $t_b_info = $bento_info[$t_pay_sn];# 對應便當資訊
        $t_card_6 = substr($ax['card8'], 0, 6);# 卡號前6碼
        $t_card_type = $bin_list[$t_card_6];# 信用卡類別        
        if (empty($t_card_type)) {
            $t_card_type = '0';
        }
        $t_type_code = $c_t_code[$t_card_type];# 類別代碼
        $t_spay = nbok($Cxc[72]);# 手續費
        
        # 火車類別(1:一般台鐵/2:阿里山森鐵)
        $t_railway_type = $ax['RailwayType'];
        
        # 便當金額小計
        $t_bento_total = $t_b_info['go_nv_ben_total'] + $t_b_info['go_v_ben_total'];
        if (!empty($ax['bksn'])) {
            # 去回票只退單程時與單程票不加回程便當金額
            $t_bento_total += $t_b_info['bk_nv_ben_total'] + $t_b_info['bk_v_ben_total'];
        }
        
        # 訂票總金額
        $t_amt = $ax['amount'];
        
        $Cxc = explode('*', $ax['pack2']);
        $WhatP = array($Cxc[32],$Cxc[37],$Cxc[42],$Cxc[47],$Cxc[52],$Cxc[57],$Cxc[62],$Cxc[67]);	//行程
        $WhatPJ = array($Cxc[33],$Cxc[38],$Cxc[43],$Cxc[48],$Cxc[53],$Cxc[58],$Cxc[63],$Cxc[68]);	//票種
        $WhatPNN = array($Cxc[35],$Cxc[40],$Cxc[45],$Cxc[50],$Cxc[55],$Cxc[60],$Cxc[65],$Cxc[70]);	//張數
        
        # 金額轉數字
        $railway_total[$t_type_code][$t_railway_type] = nbok($railway_total[$t_type_code][$t_railway_type]);
        $bento_total[$t_type_code] = nbok($bento_total[$t_type_code]);
        
        if($ax['rclose']){ # 付款
            # 交易筆數統計
            $trd_num++;
            
            # 統計付款刷卡總票價
            $amount += $ax['amount'];
            
            # 統計台鐵/森鐵請款淨額
            $railway_total[$t_type_code][$t_railway_type] += ($t_amt - $t_bento_total);
            
            # 付款手續費統計
            $spay += $Cxc[72];
            
            $tkind=1;
            # 付款票數統計
            for($i=0;$i<8;$i++){
                if($WhatPNN[$i]>0){
                    # 去回程 + 敬老票 || 去回程 + 殘障票
                    if(($WhatP[$i]==3)&&($WhatPJ[$i]==3)||($WhatP[$i]==3)&&($WhatPJ[$i]==4)){
                        $counp += $WhatPNN[$i]*2; 
                    }else {
                        # 單程
                        $counp += $WhatPNN[$i]; 
                    }
                }
            }
            
            # 統計便當請款淨額
            $bento_total[$t_type_code] += $t_bento_total;
        } else if($ax['rcancel']){ # 取消
            # 交易筆數統計
            $trd_num++;
            
            # 統計台鐵/森鐵請款淨額
            $railway_total[$t_type_code][$t_railway_type] += ($t_amt + $t_bento_total);
        
            $bspay += $ax['bspay']; # 退款手續費統計
            $ax['amount']=0-$ax['amount']; # 退款金額轉正數
            $amountc += $ax['amount']; # 退款金額統計
            $tkind=5;
            # 退款票數統計
            for($i=0;$i<8;$i++){
                if($WhatPNN[$i]>0){
                    # 去回程 + 敬老票 || 去回程 + 殘障票
                    if(($WhatP[$i]==3)&&($WhatPJ[$i]==3)||($WhatP[$i]==3)&&($WhatPJ[$i]==4)){
                        $counpc += $WhatPNN[$i]*2; 
                    }else {
                        # 單程
                        $counpc += $WhatPNN[$i]; 
                    }
                }
            }
            
            # 統計便當請款淨額
            $bento_total[$t_type_code] -= $t_bento_total;
        }
        
        # 預約數
        if(($ax['goid'])&&($ax['bkid'])){
            $resNO=2;
            $goPN=1;
            $bkPN=2;
        } else {
            $resNO=1;
            $goPN=1;
            $bkPN=0;
        }
        
        $outTxt .= spas_nabD($ax['necrrn'],12);# 授權RRN(12)
        $outTxt .= spas_nabD($ax['odnb'],9);# 交易號碼(9)
        $outTxt .= $tkind;# 交易別(1)
        $outTxt .= $resNO;# 預約筆數(1)
        $outTxt .= spas_nabD($ax['goid'],10);# 身份證字號1(10)
        $outTxt .= spas_nabD($ax['gosn'],6);# 預約號1(6)
        $outTxt .= $goPN;# 去回符號1(1)
        $outTxt .= spas_nabD($ax['bkid'],10);# 身份證字號2(10)
        $outTxt .= spas_nabD($ax['bksn'],6);# 預約號2(6)
        $outTxt .= $bkPN;# 去回符號2(1)
        $outTxt .= spas_nabD($Cxc[31],1);# 票價筆數(1)
        $outTxt .= spas_nabD($Cxc[32],1);# 行程1(1)
        $outTxt .= spas_nabD($Cxc[33],1);# 票種1(1)
        $outTxt .= spas_nabD($Cxc[34],5);# 票價1(5)
        $outTxt .= spas_nabD($Cxc[35],2);# 張數1(2)
        $outTxt .= spas_nabD($Cxc[36],5);# 總價1(5)
        $outTxt .= spas_nabD($Cxc[37],1);# 行程2(1)
        $outTxt .= spas_nabD($Cxc[38],1);# 票種2(1)
        $outTxt .= spas_nabD($Cxc[39],5);# 票價2(5)
        $outTxt .= spas_nabD($Cxc[40],2);# 張數2(2)
        $outTxt .= spas_nabD($Cxc[41],5);# 總價2(5)
        $outTxt .= spas_nabD($Cxc[42],1);# 行程3(1)
        $outTxt .= spas_nabD($Cxc[43],1);# 票種3(1)
        $outTxt .= spas_nabD($Cxc[44],5);# 票價3(5)
        $outTxt .= spas_nabD($Cxc[45],2);# 張數3(2)
        $outTxt .= spas_nabD($Cxc[46],5);# 總價3(5)
        $outTxt .= spas_nabD($Cxc[47],1);# 行程4(1)
        $outTxt .= spas_nabD($Cxc[48],1);# 票種4(1)
        $outTxt .= spas_nabD($Cxc[49],5);# 票價4(5)
        $outTxt .= spas_nabD($Cxc[50],2);# 張數4(2)
        $outTxt .= spas_nabD($Cxc[51],5);# 總價4(5)
        $outTxt .= spas_nabD($Cxc[52],1);# 行程5(1)
        $outTxt .= spas_nabD($Cxc[53],1);# 票種5(1)
        $outTxt .= spas_nabD($Cxc[54],5);# 票價5(5)
        $outTxt .= spas_nabD($Cxc[55],2);# 張數5(2)
        $outTxt .= spas_nabD($Cxc[56],5);# 總價5(5)
        $outTxt .= spas_nabD($Cxc[57],1);# 行程6(1)
        $outTxt .= spas_nabD($Cxc[58],1);# 票種6(1)
        $outTxt .= spas_nabD($Cxc[59],5);# 票價6(5)
        $outTxt .= spas_nabD($Cxc[60],2);# 張數6(2)
        $outTxt .= spas_nabD($Cxc[61],5);# 總價6(5)
        $outTxt .= spas_nabD($Cxc[62],1);# 行程7(1)
        $outTxt .= spas_nabD($Cxc[63],1);# 票種7(1)
        $outTxt .= spas_nabD($Cxc[64],5);# 票價7(5)
        $outTxt .= spas_nabD($Cxc[65],2);# 張數7(2)
        $outTxt .= spas_nabD($Cxc[66],5);# 總價7(5)
        $outTxt .= spas_nabD($Cxc[67],1);# 行程8(1)
        $outTxt .= spas_nabD($Cxc[68],1);# 票種8(1)
        $outTxt .= spas_nabD($Cxc[69],5);# 票價8(5)
        $outTxt .= spas_nabD($Cxc[70],2);# 張數8(2)
        $outTxt .= spas_nabD($Cxc[71],5);# 總價8(5)
        $outTxt .= $ax['rtime']; #交易時間yyyy/mm/dd hh:mm(16)
        $outTxt .= spas_nabD($t_b_info['go_nv_ben_num'],1); # 去程葷便當數量(1)
        $outTxt .= spas_nabD($t_b_info['go_nv_ben_total'],3); # 去程葷便當金額(3)
        $outTxt .= spas_nabD($t_b_info['go_v_ben_num'],1); # 去程葷便當數量(1)
        $outTxt .= spas_nabD($t_b_info['go_v_ben_total'],3);# 去程葷便當金額(3)
        $outTxt .= spas_nabD($t_b_info['bk_nv_ben_num'],1);# 回程葷便當數量(1)
        $outTxt .= spas_nabD($t_b_info['bk_nv_ben_total'],3);# 回程葷便當金額(3)
        $outTxt .= spas_nabD($t_b_info['bk_v_ben_num'],1);# 回程素便當數量(1)
        $outTxt .= spas_nabD($t_b_info['bk_v_ben_total'],3);# 回程素便當金額(3)
        $outTxt .= "\r\n";# 換行符號(2)
    }
    
    # 統計台鐵手續費
    $railway_spay_total['s'][1] = round($railway_total['s'][1] * (1.818 / 100));
    $railway_spay_total['e'][1] = round($railway_total['e'][1] * ((1.818 / 100) - (1.55 / 100)));
    
    # 統計森鐵手續費
    $railway_spay_total['s'][2] = round($railway_total['s'][2] * (1.818 / 100));
    $railway_spay_total['e'][2] = round($railway_total['e'][2] * ((1.818 / 100) - (1.55 / 100)));
    
    # 統計便當手續費
    $bento_spay_total['s'] = round($bento_total['s'] * (1.818 / 100));
    $bento_spay_total['e'] = round($bento_total['e'] * ((1.818 / 100) - (1.55 / 100)));

    $outPOP = $lodday;# 對帳日(8)
    $outPOP .= $lodday;# 交易開始日期(8)
    $outPOP .= $stime_tt;# 交易開始時間(6)
    $outPOP .= $lodday;# 交易結束日期(8)
    $outPOP .= $etime;# 交易結束時間(6)
    $outPOP .= spas_nabD($counp,6);# 付款交易筆數(6)
    $outPOP .= spas_nabD($amount,9);# 付款刷卡總票價(9)
    $outPOP .= spas_nabD($spay,7);# 語音手續費(7)
    $outPOP .= spas_nabD($counpc,6);# 取消交易筆數(6)
    $outPOP .= spas_nabD($amountc,9);# 取消刷卡總金額(9)
    $outPOP .= spas_nabD($bspay,7);# 刷卡退票手續費(7)
    $outPOP .= spas_nabD($trd_num,6);# 詳細對帳比數(6)
    $outPOP .= spas_nabD(abs($railway_total['s'][1]),9);# 台鐵請款淨額(本行,國外)(9)
    $outPOP .= spas_nabD(abs($railway_total['e'][1]),9);# 台鐵請款淨額(他行)(9)
    $outPOP .= spas_nabD(abs($railway_spay_total['s'][1]),9);# 台鐵手續費發票金額(本行,國外)(9)
    $outPOP .= spas_nabD(abs($railway_spay_total['e'][1]),9);# 台鐵手續費發票金額(他行)(9)
    $outPOP .= spas_nabD(abs($railway_total['s'][2]),9);# 森鐵請款淨額(本行,國外)(9)
    $outPOP .= spas_nabD(abs($railway_total['e'][2]),9);# 森鐵請款淨額(他行)(9)
    $outPOP .= spas_nabD(abs($railway_spay_total['s'][2]),9);# 森鐵手續費發票金額(本行,國外)(9)
    $outPOP .= spas_nabD(abs($railway_spay_total['e'][2]),9);# 森鐵手續費發票金額(他行)(9)
    $outPOP .= spas_nabD(abs($bento_total['s']),9);# 便當請款淨額(本行,國外)(9)
    $outPOP .= spas_nabD(abs($bento_total['e']),9);# 便當請款淨額(他行)(9)
    $outPOP .= spas_nabD(abs($bento_spay_total['s']),9);# 便當手續費發票金額(本行,國外)(9)
    $outPOP .= spas_nabD(abs($bento_spay_total['e']),9);# 便當手續費發票金額(他行)(9)   
    $outPOP .= "\r\n";#換行符號(2)

    $outPOP = str_replace('/','',$outPOP);

    $outTxt = $outPOP.$outTxt;

    # 記錄要送給 NEC 的電文(HEADER + BODY)
    $fp=fopen("/vhost/close_file/".$hohsn."_do.txt","w");
    fputs($fp,$outTxt);
    fclose($fp);

    # 開始送檔
    $FSIze = strlen($outTxt);
    $SendNEC='901A*'.$FSIze.'#'; 
    $txtnb = strlen($SendNEC);

    # 網址IP,PORT,回應代碼,回應訊息,等待時間
    $fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout);
    if($fp){
        $goNEC = @fwrite($fp, $SendNEC, $txtnb);
        $bkNEC = @fread($fp, $FreadNB);

        
        # 記錄要送給 NEC 對帳的第 1 段電文
        $log_value = array(
            'file_name' => $self_name,
            'send_nec' => $SendNEC,# 送 NEC
            'bk_nec' => $bkNEC,# NEC 回傳結果
            'msg' => '901A-1'
        );
        DBLog($log_table, $log_subject, $log_value);
        
        $bctnb = strlen($bkNEC);
        $bctnb = $bctnb-1;
        $VCAS = substr($bkNEC, $bctnb, 1);

        $bkNEC = str_replace('#','',$bkNEC);
        $CxcN = explode('*', $bkNEC);

        if(($bkNEC)&&($CxcN[0]=='901B')&&($VCAS=='#')){

            $goNEC2 = @fwrite($fp, $outTxt, $FSIze);
            $bkNEC2 = @fread($fp, $FreadNB);
            fclose($fp);
            $log_value = array(
                'file_name' => $self_name,
                'send_nec' => $outPOP,# HEADER
                'bk_nec' => $bkNEC2,# NEC 回傳結果
            );

            $bctnb2 = strlen($bkNEC2);
            $bctnb2 = $bctnb2 - 1;
            $VCAS2 = substr($bkNEC2, $bctnb2, 1);

            $bkNEC2 = str_replace('#','',$bkNEC2);
            $CxcN2 = explode('*', $bkNEC2);

            if(($bkNEC2)&&($CxcN2[0]=='901C')&&($VCAS2=='#')&&($CxcN2[1]==$FSIze)){
                $isOk='ok';
                $fp=fopen("/vhost/close_file/".$hohsn."_ok.txt","w");
            } else {
                $isOk='bad';
                $fp=fopen("/vhost/close_file/".$hohsn."_bad.txt","w");
            }
            fputs($fp,$outTxt);
            fclose($fp);
            
            # 記錄要送給 NEC 對帳的第 2 段電文
            $log_value['msg'] = '901A-2-' . $isOk; # 對帳結果
            DBLog($log_table, $log_subject, $log_value);
        }
    }

    echo $isOk;
    exit;
?>
