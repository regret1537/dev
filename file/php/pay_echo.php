<?php
    //$sen=0; //0����,1���q, �w��� mysql.inc ����
    include('mysql.inc');
    include('trainMsg.inc');
	
	// �]�w POST �Ѽ�
    function assign_post($aVariable_List) {
        foreach ($aVariable_List as $sTmp_Name) {
            ${$sTmp_Name} = $_POST[$sTmp_Name];
        }
    }
    
    // �]�w POST �Ѽ�
    $validate_post = array(
		'succ',
		'gwsr',
		'response_code',
		'process_date',
		'process_time',
		'auth_code',
		'amount',
		'email',
		'eci',
		'isGuolu',
		'glCity',
		'inspect',
		'spcheck',
		'rech_key',
		'card6no',
		'card4no',
		'expire_dt',
		'allsn',
		'od_sob',
		'od_hoho',
		'response_msg',
		'inv_error'
	);
    assign_post($validate_post);
	unset($validate_post);

	$sPHP_Name = basename(__FILE__, '.php'); // PHP �W��
	
    // ���J���ո��
    $sTest_BK_NEC = '';
    if (TEST_MODE) {
        include('test_config.php');
        $sTest_Key = basename(__FILE__);
        if (isset($aTest_Params[$sTest_Key]['bkNEC'])) {
            $sTest_BK_NEC = $aTest_Params[$sTest_Key]['bkNEC'];
        }
    }

    if (!$_SESSION[$sess_USER]['pk2']) { //���n��
?>
    <script language="JavaScript">
        <!--
        alert('01<?= $language['_Alert_txt_026'] ?>');
        location.href = "pay.php";
        // -->
    </script>
<?php
        exit;
    }

    $link = mylink();

	// ���ϥΰ϶� {
    if ($sen == 0) {
        //***************************
        $strt = "SELECT gwpaysn FROM uorder order by gwpaysn desc limit 1 ";
        $qqt = myquery($strt, $link);
        $axt = @mysql_fetch_array($qqt);

        $succ = 1;
        $gwsr = $axt['gwpaysn'] + 1;
        $aasIIOK = 1;
        $card6no = '123456';
        $card4no = '7890';
        $cd8ok[0] = $card6no . "******" . $card4no;
        $cd8ok_nec = $card6no . $card4no; //�d���e6�X+��4�X
        $tCxc = explode('*', $_SESSION[$sess_USER]['pk2']);
        $amount = $tCxc[36]+$tCxc[41]+$tCxc[46]+$tCxc[51]+$tCxc[56]+$tCxc[61]+$tCxc[66]+$tCxc[71]+$tCxc[72];	//nec���`��
        $pptime = $hohsnY . '/' . $hohsnM . '/' . $hohsnD . ' ' . spas_nab(date("H"), 2) . ':' . spas_nab(date("i"), 2); //���X�ɶ�
        $get_close = 1;
        //***************************
    }
	// }
	
    if ($succ == 1) { //����
        $link = mylink();

        if ($sen == 1) {// ���ϥΧP�_

            function isRightPacket($loginName, $s, $insp) { // �ݬݻ{�ҹ藍�� 
                $s1 = md5($s);
                $s2 = md5($loginName);
                $s3 = md5($s1 ^ $s2);
                if ($insp == $s3) { return true; }
                return false;
            }
            
            $chk_head_key = '7zvJ7w5EmckjBYKM4FhFqDp4';
            $chk_foot_key = 'VI7xhfOdI7wGXAI2czkQbnQf';
            $s = $chk_head_key;
            $s .= $gwsr;
            $s .= $response_code;
            $s .= $succ;
            $s .= $process_time;
            $s .= $amount;
            $s .= $process_date;
            $s .= $od_sob;
            $s .= $auth_code;
            $s .= $chk_foot_key;

            $insp = $inspect;

            $aasIIOK = isRightPacket($loginName, $s, $insp);
            
            
            $rech_time = $process_date . $process_time;
            $rech_sr = $gwsr;
            $serial = $rech_time . $rech_sr;
            
            //�Ϭd���v�O���A�^��valid=n
            $chk_res = file("$GWECpauUrl/g_recheck.php?key=$rech_key&serial=$serial&amt=$amount");
            parse_str($chk_res[0]);
            
            if ($valid != 1) { //���`����Asucc=1�A���v�o����
                $aasIIOK = false;
				
				$sLine_Message = $sockMachineName . ', �x�K���`����q���A�i��O�b�ȡI(���v�渹: ' . $gwsr . '���B: ' . $amount . '�ɶ�: ' . date('Y-m-d H:i:s') . ')';
				// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);
            }
        }
    }

    if ($aasIIOK) { // �T�{����F
        $pack2 = $_SESSION[$sess_USER]['pk2'];
        $uppp = $_SESSION[$sess_USER]['ppp'];

        $Cxc = explode('*', $pack2);
        
        $railway_type = mysql_real_escape_string(trim($Cxc[74]));
        if (empty($railway_type)) {
            $railway_type = '1';
        }
        
        $Cppp = explode('*', $uppp);

        // ��������X9�X
        $fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout); //���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
        if ($fp) {
            $goODnb = @fwrite($fp, '101A*#', 6);
            $bkODnb = @fread($fp, 15);
            fclose($fp);
        }
        $bkODnb = str_replace('#', '', $bkODnb);
        $Cxcp = explode('*', $bkODnb);

        $gwsr = $gwsr + 1 - 1;

        // ���T�w�O�_�w��gwsr���渹�s�b
        $stgwsr = "SELECT gwpaysn FROM uorder where gwpaysn='$gwsr' and aa>1330959 limit 1 ";
        $qqgwsr = myquery($stgwsr, $link);
        $axgwsr = @mysql_fetch_array($qqgwsr);
        if ($axgwsr['gwpaysn']) { //�w�s�b
?>
            <script language="JavaScript">
            <!--
                alert('02<?= $language['_Alert_txt_014'] ?>');
                location.href = "pay.php";
            // -->
            </script>
<?php
            exit;
        }
        // �P�_��ȥd
        if ($isGuolu == 'Y') {
            $isGuolu = 1;
        } else {
            $isGuolu = 0;
        }

        function spcDDs($a, $b) {
            $c = strlen($a);
            if ($c != $b) {
                $a = '';
            }
            return $a;
        }

        if ($sen == 1) {
            //card6no  �e6�X�Bcard4no  ��4�X
            $cd8ok[0] = $card6no . "******" . $card4no;
            $cd8ok_nec = $card6no . $card4no; // �d���e6�X+��4�X
            
            $pptime = substr($process_date, 0, 4) . '/' . substr($process_date, 4, 2) . '/' . substr($process_date, 6, 2) . ' ' . substr($process_time, 0, 2) . ':' . substr($process_time, 2, 2); //���X�ɶ�
        }

        $Cxc[3] = spcDDs($Cxc[3], 10);
        $Cxc[4] = spcDDs($Cxc[4], 6);
        $Cxc[17] = spcDDs($Cxc[17], 10);
        $Cxc[18] = spcDDs($Cxc[18], 6);
        
        // �g�Jgwp�ƥ����
        $strgwp = "insert into uorder_gwp ";
        $strgwp .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,eci,authnb,RailwayType) ";
        $strgwp .= "values(0,'$Cxcp[1]','$NTIME',AES_ENCRYPT('$Cxc[3]','$key_str_ch'),'$Cxc[4]',AES_ENCRYPT('$Cxc[17]','$key_str_ch'),'$Cxc[18]','$amount','$pptime','$cd8ok[0]','$isGuolu','$get_close','$get_cancel','$gwsr','$CxcN[2]','$glCity','$pack2','$bkNEC','$uppp','$Cxc[72]','$eci','$auth_code','$railway_type')";
        $resultgwp = myquery($strgwp, $link);
        
        // �O���K���T
        $go_sn = $Cxc[4];
        $go_nv_ben_num = $Cxc[76];
        $go_nv_ben_total = $Cxc[77];
        $go_v_ben_num = $Cxc[78];
        $go_v_ben_total = $Cxc[79];
        $bk_sn = $Cxc[18];
        $bk_nv_ben_num = $Cxc[80];
        $bk_nv_ben_total = $Cxc[81];
        $bk_v_ben_num = $Cxc[82];
        $bk_v_ben_total = $Cxc[83];
        $ins_sql = "INSERT INTO " . TB_BENTO . " ";
        $ins_sql .= "(gw_pay_sn,go_sn,go_nv_ben_num,go_nv_ben_total,go_v_ben_num,go_v_ben_total,bk_sn,bk_nv_ben_num,bk_nv_ben_total,bk_v_ben_num,bk_v_ben_total) ";
        $ins_sql .= "VALUE('$gwsr','$go_sn','$go_nv_ben_num','$go_nv_ben_total','$go_v_ben_num','$go_v_ben_total','$bk_sn','$bk_nv_ben_num','$bk_nv_ben_total','$bk_v_ben_num','$bk_v_ben_total')";
        $res_bento = myquery($ins_sql, $link);
        
        if (!$Cxcp[1]) { // �S����渹
			$sLine_Message = $sockMachineName . ', �x�K����������ѳq���I(�渹: ' . $Cxcp[1] . '���B: ' . $amount . '�ɶ�: ' . $NTIME . ')';
			// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);
?>
            <script language="JavaScript">
                <!--
                alert('03<?= $language['_Alert_txt_014'] ?>');
                location.href = "pay.php";
                // -->
            </script>
<?php
            exit;
        }
        
        // �K���`���B
        $bento_total = $Cxc[77] + $Cxc[79] + $Cxc[81] + $Cxc[83];
        
        // �e��NEC������
        $necpay = $amount - $Cxc[72] - $bento_total;// �`�����ݦ����K���`���B
        // $SendNEC = '003A*' . $Cxcp[1] . '*' . spas_nab($Cxc[3], 10) . '*' . spas_nab($Cxc[4], 6) . '*' . spas_nab($Cxc[17], 10) . '*' . spas_nab($Cxc[18], 6) . '*' . spas_nab($Cppp[0], 2) . '*' . spas_nab($Cppp[1], 2) . '*' . spas_nab($Cppp[2], 2) . '*' . spas_nab($Cppp[3], 2) . '*' . spas_nab($Cppp[4], 2) . '*' . spas_nab($Cppp[5], 2) . '*' . spas_nab($Cppp[6], 2) . '*' . spas_nab($Cppp[7], 2) . '*' . sprintf("%05s", $necpay) . '*' . $pptime . '*' . spas_nab($cd8ok_nec, 10) . '#';
        
        $SendNECData = array(
            '003A' => '003A',
            '������X(9)' => $Cxcp[1],
            '�h�{�����Ҧr��(10)' => spas_nab($Cxc[3], 10),
            '�h�{�w����(6)' => spas_nab($Cxc[4], 6),
            '�^�{�����Ҧr��(10)' => spas_nab($Cxc[17], 10),
            '�^�{�w����(6)' => spas_nab($Cxc[18], 6),
            '�h�{���H���i��(2)' => spas_nab($Cppp[0], 2),
            '�h�{�ĵ����i��(2)' => spas_nab($Cppp[1], 2),
            '�h�{�q�Ѳ��i��(2)' => spas_nab($Cppp[2], 2),
            '�h�{�ݻٲ��i��(2)' => spas_nab($Cppp[3], 2),
            '�^�{���H���i��(2)' => spas_nab($Cppp[4], 2),
            '�^�{�ĵ����i��(2)' => spas_nab($Cppp[5], 2),
            '�^�{�q�Ѳ��i��(2)' => spas_nab($Cppp[6], 2),
            '�^�{�ݻٲ��i��(2)' => spas_nab($Cppp[7], 2),
            '�`����(5)' => sprintf("%05s", $necpay),
            // '���vRRN(12)' => '', // ���ζ�, AAT �۱a�Ѽ�
            '����ɶ�yyyy/mm/dd hh:mm(16)' => $pptime,
            '��d�d���e����|�X(10)' => spas_nab($cd8ok_nec, 10),
            // '�y�쫬�A(1)' => '', // ���ζ�, AAT �۱a�Ѽ�
            '�h�{���K��ƶq(1)' => spas_nab($go_nv_ben_num, 1),
            '�h�{���K����B(3)' => spas_nab($go_nv_ben_total, 3),
            '�h�{���K��ƶq(1)' => spas_nab($go_v_ben_num, 1),
            '�h�{���K����B(3)' => spas_nab($go_v_ben_total, 3),
            '�^�{���K��ƶq(1)' => spas_nab($bk_nv_ben_num, 1),
            '�^�{���K����B(3)' => spas_nab($bk_nv_ben_total, 3),
            '�^�{���K��ƶq(1)' => spas_nab($bk_v_ben_num, 1),
            '�^�{���K����B(3)' => spas_nab($bk_v_ben_total, 3),
        );
        $SendNEC = CreateSendNEC($SendNECData);
        $txtnb = strlen($SendNEC);
        
        // �O�� 003A �q��
        $sMasked_AAT_NEC_Stat = mask_nec_stat($SendNEC); // ���Ӹ�����X
        write_log('nec_trace_003', 'nec msg', array('SendNEC' => $sMasked_AAT_NEC_Stat));
        
        if (TEST_MODE and $sTest_BK_NEC != '') {
            // ���ո��
            $bkNEC = $sTest_BK_NEC;
        } else {
            $fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout); //���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
            if ($fp) {
                $goNEC = @fwrite($fp, $SendNEC, $txtnb);
                $bkNEC = @fread($fp, $FreadNB);
                fclose($fp);
            }
        }

        // �O�� 003B �q��
        $sMasked_AAT_NEC_Stat = mask_nec_stat($bkNEC); // ���Ӹ�����X
        write_log('nec_trace_003', 'nec msg', array('bkNEC' => $sMasked_AAT_NEC_Stat));

        if (!$bkNEC) { //�䤣��
			$sLine_Message = $sockMachineName . ', �x�K�eNEC���楢�ѡI(�渹: ' . $Cxcp[1] . '���B: ' . $amount . '�ɶ�: ' . $NTIME . ')';
			// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);
?>
            <script language="JavaScript">
                <!--
                alert('04<?= $language['_Alert_txt_014'] ?>');
                location.href = "pay.php";
                // -->
            </script>
<?php
            exit;
        }
        // �ڳo�� connections ���F�ɷ|�^�ǵ��A 944X*#
        include('ch_944X.php');
        // �S#��Ƥ���
        $bctnb = strlen($bkNEC);
        $bctnb = $bctnb - 1;
        $VCAS = substr($bkNEC, $bctnb, 1);

        $bkNEC = str_replace('#', '', $bkNEC);
        $CxcN = explode('*', $bkNEC);

        if ($CxcN[1] != '000') { //����
            // ��Ʈw���~!�^����
            if ($CxcN[1] == '999') {
                $CHnecTM = date("YmdH"); //��ƮɽX
                $Necstp = file("set_nec_error.inc");
                $CokYY = explode(',', $Necstp[0]);
                $NeCutM = $CokYY[1];  //���~�ɶ�
                $NeCuMB = $CokYY[0];  //���~����
                if ($NeCutM == $CHnecTM) {
                    $CHnecST = $NeCuMB + 1;
                } else {
                    $CHnecST = 0;
                }
                $upDDDT = $CHnecST . ',' . $CHnecTM;
                $fp = fopen("set_nec_error.inc", "w");
                fputs($fp, $upDDDT);
                fclose($fp);

                if ($CHnecST > 7) {
					$sLine_Message = $sockMachineName . ', NEC��Ʈw���~�I(�渹: ' . $Cxcp[1] . '���B: ' . $amount . '�ɶ�: ' . $NTIME . ')';
					// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);
                }
            }

            $echoERRO = $trainMsg[$CxcN[1]];
?>
            <script language="JavaScript">
                <!--
                alert('05<?= $language['_Alert_txt_015'] ?> <?= $echoERRO ?>!');
                location.href = "pay.php";
                // -->
            </script>
<?php
            exit;
        }

        if (($VCAS != '#') || ($CxcN[0] != '003B')) {
?>
            <script language="JavaScript">
                <!--
                alert('06<?= $language['_Alert_txt_026'] ?>');
                location.href = "pay.php";
                // -->
            </script>
<?php
            exit;
        }
        // �g�J�ƥ����
        $strbk = "insert into uorder_bk ";
        $strbk .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,eci,authnb,SendNEC,RailwayType) ";
        $strbk .= "values(0,'$Cxcp[1]','$NTIME',AES_ENCRYPT('$Cxc[3]','$key_str_ch'),'$Cxc[4]',AES_ENCRYPT('$Cxc[17]','$key_str_ch'),'$Cxc[18]','$amount','$pptime','$cd8ok[0]','$isGuolu','$get_close','$get_cancel','$gwsr','$CxcN[2]','$glCity','$pack2','$bkNEC','$uppp','$Cxc[72]','$eci','$auth_code','$SendNEC','$railway_type')";
        $resultbk = myquery($strbk, $link);

        if ($sen == 1) {
            $VchOn = substr($gwsr, -4, 4); //�ˬd�X
            $VchOn = ($VchOn * $amount) % 3;

            if ($CxcN[47] > 1) { //�HNEC�^�Ǯɶ�����
                if (strlen($CxcN[47]) == 14) {
                    $pptime = substr($CxcN[47], 0, 4) . "/" . substr($CxcN[47], 4, 2) . "/" . substr($CxcN[47], 6, 2) . " " . substr($CxcN[47], 8, 2) . ":" . substr($CxcN[47], 10, 2);
                    $NceCEHOtime = $CxcN[47];
                    $Nt_YYYY = substr($CxcN[47], 0, 4);
                    $Nt_MM = substr($CxcN[47], 4, 2);
                    $Nt_DD = substr($CxcN[47], 6, 2);
                    $Nt_HH = substr($CxcN[47], 8, 2);
                    $Nt_II = substr($CxcN[47], 10, 2);
                    $Nt_SS = substr($CxcN[47], 12, 2);
                    $NTIME = mktime($Nt_HH, $Nt_II, $Nt_SS, $Nt_MM, $Nt_DD, $Nt_YYYY);
                }
            }
            if (TEST_MODE) {
                // ���ռҦ����e���b
                $bkClos = 'inok';
            } else {
                $SEND_DT = "[$GWECpauUrl/tarin_close.php?s=$gwsr&a=$amount&t=close&c=$VchOn&Ntime=$NceCEHOtime]";
                $fp = fsockopen($sockUrl, $sockPORTclose, $errno, $errstr, 20); //���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
                if ($fp) {
                    $goClos = @fwrite($fp, $SEND_DT, 200);
                    $bkClos = @fread($fp, 15);
                    fclose($fp);
                }
            }

            if (substr($bkClos, 0, 4) == 'inok') { //���b���\
                $get_close = 1;
            } else {   ////���b����
                $get_close = 1; //�Ȯɥ�0�令1,�[�
				$sLine_Message = $sockMachineName . ', �x�K������b���ѡI(���v�渹: ' . $gwsr . '���B: ' . $amount . '�ɶ�: ' . $NceCEHOtime . ')';
				// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);				
            }
        }

        // �g�J������
        $str = "insert into uorder ";
        $str .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,eci,authnb,SendNEC,RailwayType) ";
        $str .= "values(0,'$Cxcp[1]','$NTIME',AES_ENCRYPT('$Cxc[3]','$key_str_ch'),'$Cxc[4]',AES_ENCRYPT('$Cxc[17]','$key_str_ch'),'$Cxc[18]','$amount','$pptime','$cd8ok[0]','$isGuolu','$get_close','$get_cancel','$gwsr','$CxcN[2]','$glCity','$pack2','$bkNEC','$uppp','$Cxc[72]','$eci','$auth_code','$SendNEC','$railway_type')";
        $result = myquery($str, $link);
        // ��s timestamp ���
        $str = "update uorder_bk set tmstp='$NTIME' where odnb='$Cxcp[1]' and amount='$amount' and rtime='$pptime' limit 1 ";
        $result = mysql_query($str, $link);
        // ��s timestamp ���
        $str = "update uorder_gwp set tmstp='$NTIME' where odnb='$Cxcp[1]' and amount='$amount' and rtime='$pptime' limit 1 ";
        $result = mysql_query($str, $link);
        // ����^������
        $_SESSION[$sess_USER]['sr'] = $gwsr;
        $_SESSION[$sess_USER]['msger'] = $response_code . ' : ' . $response_msg;
        $_SESSION[$sess_USER]['C'] = '';
        $_SESSION[$sess_USER]['ppp'] = '';
        $_SESSION[$sess_USER]['pk2'] = '';
        $_SESSION[$sess_USER]['auth_code'] = $auth_code;

        Header("Location: pay_ing2ok.php");
    } else {
        // ���Ѧ^������
        Header("Location: pay_ing2bad.php");
        exit;
    }
?>