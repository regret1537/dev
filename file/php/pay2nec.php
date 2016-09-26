<?php
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    # �C�� 01:00 �����業�b
    include('mysql.inc');
	
	$sPHP_Name = basename(__FILE__, '.php'); // PHP �W��

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

    # ��������T�{
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
        if($CPS!='jmwang'){	//�K�X����
?>
    <script language="JavaScript">
        <!--
        alert('�K�X���~�ɰe���b����');
        location.href="bksand.php";
        // -->
    </script>
<?php
        exit;
        }
	
        if(!$Tsn) $Tsn=1;
        $lodday = date($sy."/".$sm."/".$sd); //���w���
        $stime_tt = '0000'.$Tsn.'0';	//���Y��
    } else {
        $MKtime= mktime(0,0,0,date("m"),date("d")-1,date("Y")); 
        $lodday= date("Y/m/d",$MKtime); //�e�@�Ѫ����
        $stime_tt = '000000';	//���Y��
    }
    $stime = '000000';	
    $etime = '235959';

    //=====�R�����Ъ� ���v�X & eci ���Ū����
    $strck = "SELECT aa,odnb,amount FROM uorder where authnb='' and eci='' and rtime like '$lodday%' and rclose!='0' ";
    $qqck = myquery($strck, $link);
    if (mysql)
    while($ly = @mysql_fetch_array($qqck)){
        //====��X ���v�X & eci ���u���T���, �p�G���N�R�����Ъ� ���v�X & eci ���Ū����
        $strckA = "SELECT aa,odnb,amount FROM uorder where authnb!='' and eci!='' and rtime like '$lodday%' and rclose!='0' and odnb='$ly[odnb]' and amount='$ly[amount]' ";
        $qqckA = myquery($strckA, $link);
        while($lyA = @mysql_fetch_array($qqckA)){
            if($lyA[aa]){
                $aoo++;
                $del_data .= "($aoo) �渹:".$ly[odnb]."���B:".$ly[amount].', ';
                $strpm_del = "delete FROM uorder where aa='$ly[aa]' and eci='' and authnb='' and odnb='$ly[odnb]' and amount='$ly[amount]' limit 1 ";
                myquery($strpm_del, $link);
            }
        } 
    }

    if($del_data){
		$sLine_Message = $sockMachineName . ' , �x�K���ƽվ�q�� ' . $del_data;
		// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);
    }
    if (TEST_MODE) {
        $lodday = $argv[1];
    }
    
    # ���o���v�渹
    $sel_sql = "SELECT gwpaysn FROM uorder WHERE LEFT(rtime, 10) = '$lodday' ORDER BY aa ";
    $sel_qry = myquery($sel_sql, $link);
    $pay_sn_ary = array();
    while ($sel_ary = @mysql_fetch_array($sel_qry)) {
        array_push($pay_sn_ary, $sel_ary['gwpaysn']);
    }
    mysql_free_result($sel_qry);

    if (empty($pay_sn_ary)) {
        # �O�����`���浲�G
        $log_value['msg'] = $lodday . ' no trade.';
        DBLog($log_table, $log_subject, $log_value);
        die('fail');
    }
    
    # ���o���ȸ��
    $pay_sn_list = implode(',', $pay_sn_ary);
    $sel_sql = "SELECT uorder.*,pack2,pack3,AES_DECRYPT(goid,'$key_str_ch') as goid,AES_DECRYPT(bkid,'$key_str_ch') as bkid FROM uorder WHERE gwpaysn IN ($pay_sn_list) AND LEFT(rtime, 10) = '$lodday' ORDER BY aa ";
    $sel_qry = myquery($sel_sql, $link);
    $tck_info = array();
    while ($sel_ary = @mysql_fetch_array($sel_qry)) {
        array_push($tck_info, $sel_ary);
    }
    mysql_free_result($sel_qry);

    # ���o�K����
    $bento_info = array();
    $sel_sql = "SELECT gw_pay_sn,go_nv_ben_num, go_nv_ben_total, go_v_ben_num, go_v_ben_total, bk_nv_ben_num, bk_nv_ben_total, bk_v_ben_num, bk_v_ben_total FROM " . TB_BENTO . " WHERE gw_pay_sn IN ($pay_sn_list) ORDER BY sn ";
    $sel_qry = myquery($sel_sql, $link);
    while ($sel_ary = @mysql_fetch_array($sel_qry)) {
        $bento_info[$sel_ary['gw_pay_sn']] = $sel_ary;
    }
    mysql_free_result($sel_qry);
    
    # ���oBIN��
    $sel_sql = "SELECT cardno,cardtype FROM " . TB_BIN . " ORDER BY aa ";
    $sel_qry = myquery($sel_sql, $link);
    $bin_list = array();
    while ($sel_ary = @mysql_fetch_array($sel_qry)) {
        $bin_list[$sel_ary['cardno']] = $sel_ary['cardtype'];
    }
    mysql_free_result($sel_qry);

    $outTxt = '';
    $counp=0;   # �I�ڥ������(6)
    $counpc=0;	# �����������(6)
    $amountc=0;	# ������d�`���B(9)
    $spay = 0;	# �y������O(7)
    $bspay = 0; # ��d�h������O(7)

    # �x/���K�дڲb�B(1:�@��x�K/2:�����s���K)
    $railway_total = array(
        's' => array('1' => '', '2' => ''),
        'e' => array('1' => '', '2' => ''),
    );

    # �x/���K����O�o�����B(1:�@��x�K/2:�����s���K)
    $railway_spay_total = array(
        's' => array('1' => '', '2' => ''),
        'e' => array('1' => '', '2' => ''),
    );

    # �K��дڲb�B
    $bento_total = array('s' => '', 'e' => '');

    # �K�����O�o�����B(�K��L����O�A�ť�)
    $bento_spay_total = array('s' => '', 'e' => '');

    # �H�Υd���O(s:����/e:�L��)
    $c_t_code = array('s', 'e');# �d�O�N�X
    $amount = 0;
    $trd_num = 0;
    foreach ($tck_info as $ax) {
        $t_pay_sn = $ax['gwpaysn'];# ���v�渹
        $t_b_info = $bento_info[$t_pay_sn];# �����K���T
        $t_card_6 = substr($ax['card8'], 0, 6);# �d���e6�X
        $t_card_type = $bin_list[$t_card_6];# �H�Υd���O        
        if (empty($t_card_type)) {
            $t_card_type = '0';
        }
        $t_type_code = $c_t_code[$t_card_type];# ���O�N�X
        $t_spay = nbok($Cxc[72]);# ����O
        
        # �������O(1:�@��x�K/2:�����s���K)
        $t_railway_type = $ax['RailwayType'];
        
        # �K����B�p�p
        $t_bento_total = $t_b_info['go_nv_ben_total'] + $t_b_info['go_v_ben_total'];
        if (!empty($ax['bksn'])) {
            # �h�^���u�h��{�ɻP��{�����[�^�{�K����B
            $t_bento_total += $t_b_info['bk_nv_ben_total'] + $t_b_info['bk_v_ben_total'];
        }
        
        # �q���`���B
        $t_amt = $ax['amount'];
        
        $Cxc = explode('*', $ax['pack2']);
        $WhatP = array($Cxc[32],$Cxc[37],$Cxc[42],$Cxc[47],$Cxc[52],$Cxc[57],$Cxc[62],$Cxc[67]);	//��{
        $WhatPJ = array($Cxc[33],$Cxc[38],$Cxc[43],$Cxc[48],$Cxc[53],$Cxc[58],$Cxc[63],$Cxc[68]);	//����
        $WhatPNN = array($Cxc[35],$Cxc[40],$Cxc[45],$Cxc[50],$Cxc[55],$Cxc[60],$Cxc[65],$Cxc[70]);	//�i��
        
        # ���B��Ʀr
        $railway_total[$t_type_code][$t_railway_type] = nbok($railway_total[$t_type_code][$t_railway_type]);
        $bento_total[$t_type_code] = nbok($bento_total[$t_type_code]);
        
        if($ax['rclose']){ # �I��
            # ������Ʋέp
            $trd_num++;
            
            # �έp�I�ڨ�d�`����
            $amount += $ax['amount'];
            
            # �έp�x�K/���K�дڲb�B
            $railway_total[$t_type_code][$t_railway_type] += ($t_amt - $t_bento_total);
            
            # �I�ڤ���O�έp
            $spay += $Cxc[72];
            
            $tkind=1;
            # �I�ڲ��Ʋέp
            for($i=0;$i<8;$i++){
                if($WhatPNN[$i]>0){
                    # �h�^�{ + �q�Ѳ� || �h�^�{ + �ݻٲ�
                    if(($WhatP[$i]==3)&&($WhatPJ[$i]==3)||($WhatP[$i]==3)&&($WhatPJ[$i]==4)){
                        $counp += $WhatPNN[$i]*2; 
                    }else {
                        # ��{
                        $counp += $WhatPNN[$i]; 
                    }
                }
            }
            
            # �έp�K��дڲb�B
            $bento_total[$t_type_code] += $t_bento_total;
        } else if($ax['rcancel']){ # ����
            # ������Ʋέp
            $trd_num++;
            
            # �έp�x�K/���K�дڲb�B
            $railway_total[$t_type_code][$t_railway_type] += ($t_amt + $t_bento_total);
        
            $bspay += $ax['bspay']; # �h�ڤ���O�έp
            $ax['amount']=0-$ax['amount']; # �h�ڪ��B�ॿ��
            $amountc += $ax['amount']; # �h�ڪ��B�έp
            $tkind=5;
            # �h�ڲ��Ʋέp
            for($i=0;$i<8;$i++){
                if($WhatPNN[$i]>0){
                    # �h�^�{ + �q�Ѳ� || �h�^�{ + �ݻٲ�
                    if(($WhatP[$i]==3)&&($WhatPJ[$i]==3)||($WhatP[$i]==3)&&($WhatPJ[$i]==4)){
                        $counpc += $WhatPNN[$i]*2; 
                    }else {
                        # ��{
                        $counpc += $WhatPNN[$i]; 
                    }
                }
            }
            
            # �έp�K��дڲb�B
            $bento_total[$t_type_code] -= $t_bento_total;
        }
        
        # �w����
        if(($ax['goid'])&&($ax['bkid'])){
            $resNO=2;
            $goPN=1;
            $bkPN=2;
        } else {
            $resNO=1;
            $goPN=1;
            $bkPN=0;
        }
        
        $outTxt .= spas_nabD($ax['necrrn'],12);# ���vRRN(12)
        $outTxt .= spas_nabD($ax['odnb'],9);# ������X(9)
        $outTxt .= $tkind;# ����O(1)
        $outTxt .= $resNO;# �w������(1)
        $outTxt .= spas_nabD($ax['goid'],10);# �����Ҧr��1(10)
        $outTxt .= spas_nabD($ax['gosn'],6);# �w����1(6)
        $outTxt .= $goPN;# �h�^�Ÿ�1(1)
        $outTxt .= spas_nabD($ax['bkid'],10);# �����Ҧr��2(10)
        $outTxt .= spas_nabD($ax['bksn'],6);# �w����2(6)
        $outTxt .= $bkPN;# �h�^�Ÿ�2(1)
        $outTxt .= spas_nabD($Cxc[31],1);# ��������(1)
        $outTxt .= spas_nabD($Cxc[32],1);# ��{1(1)
        $outTxt .= spas_nabD($Cxc[33],1);# ����1(1)
        $outTxt .= spas_nabD($Cxc[34],5);# ����1(5)
        $outTxt .= spas_nabD($Cxc[35],2);# �i��1(2)
        $outTxt .= spas_nabD($Cxc[36],5);# �`��1(5)
        $outTxt .= spas_nabD($Cxc[37],1);# ��{2(1)
        $outTxt .= spas_nabD($Cxc[38],1);# ����2(1)
        $outTxt .= spas_nabD($Cxc[39],5);# ����2(5)
        $outTxt .= spas_nabD($Cxc[40],2);# �i��2(2)
        $outTxt .= spas_nabD($Cxc[41],5);# �`��2(5)
        $outTxt .= spas_nabD($Cxc[42],1);# ��{3(1)
        $outTxt .= spas_nabD($Cxc[43],1);# ����3(1)
        $outTxt .= spas_nabD($Cxc[44],5);# ����3(5)
        $outTxt .= spas_nabD($Cxc[45],2);# �i��3(2)
        $outTxt .= spas_nabD($Cxc[46],5);# �`��3(5)
        $outTxt .= spas_nabD($Cxc[47],1);# ��{4(1)
        $outTxt .= spas_nabD($Cxc[48],1);# ����4(1)
        $outTxt .= spas_nabD($Cxc[49],5);# ����4(5)
        $outTxt .= spas_nabD($Cxc[50],2);# �i��4(2)
        $outTxt .= spas_nabD($Cxc[51],5);# �`��4(5)
        $outTxt .= spas_nabD($Cxc[52],1);# ��{5(1)
        $outTxt .= spas_nabD($Cxc[53],1);# ����5(1)
        $outTxt .= spas_nabD($Cxc[54],5);# ����5(5)
        $outTxt .= spas_nabD($Cxc[55],2);# �i��5(2)
        $outTxt .= spas_nabD($Cxc[56],5);# �`��5(5)
        $outTxt .= spas_nabD($Cxc[57],1);# ��{6(1)
        $outTxt .= spas_nabD($Cxc[58],1);# ����6(1)
        $outTxt .= spas_nabD($Cxc[59],5);# ����6(5)
        $outTxt .= spas_nabD($Cxc[60],2);# �i��6(2)
        $outTxt .= spas_nabD($Cxc[61],5);# �`��6(5)
        $outTxt .= spas_nabD($Cxc[62],1);# ��{7(1)
        $outTxt .= spas_nabD($Cxc[63],1);# ����7(1)
        $outTxt .= spas_nabD($Cxc[64],5);# ����7(5)
        $outTxt .= spas_nabD($Cxc[65],2);# �i��7(2)
        $outTxt .= spas_nabD($Cxc[66],5);# �`��7(5)
        $outTxt .= spas_nabD($Cxc[67],1);# ��{8(1)
        $outTxt .= spas_nabD($Cxc[68],1);# ����8(1)
        $outTxt .= spas_nabD($Cxc[69],5);# ����8(5)
        $outTxt .= spas_nabD($Cxc[70],2);# �i��8(2)
        $outTxt .= spas_nabD($Cxc[71],5);# �`��8(5)
        $outTxt .= $ax['rtime']; #����ɶ�yyyy/mm/dd hh:mm(16)
        $outTxt .= spas_nabD($t_b_info['go_nv_ben_num'],1); # �h�{���K��ƶq(1)
        $outTxt .= spas_nabD($t_b_info['go_nv_ben_total'],3); # �h�{���K����B(3)
        $outTxt .= spas_nabD($t_b_info['go_v_ben_num'],1); # �h�{���K��ƶq(1)
        $outTxt .= spas_nabD($t_b_info['go_v_ben_total'],3);# �h�{���K����B(3)
        $outTxt .= spas_nabD($t_b_info['bk_nv_ben_num'],1);# �^�{���K��ƶq(1)
        $outTxt .= spas_nabD($t_b_info['bk_nv_ben_total'],3);# �^�{���K����B(3)
        $outTxt .= spas_nabD($t_b_info['bk_v_ben_num'],1);# �^�{���K��ƶq(1)
        $outTxt .= spas_nabD($t_b_info['bk_v_ben_total'],3);# �^�{���K����B(3)
        $outTxt .= "\r\n";# ����Ÿ�(2)
    }
    
    # �έp�x�K����O
    $railway_spay_total['s'][1] = round($railway_total['s'][1] * (1.818 / 100));
    $railway_spay_total['e'][1] = round($railway_total['e'][1] * ((1.818 / 100) - (1.55 / 100)));
    
    # �έp���K����O
    $railway_spay_total['s'][2] = round($railway_total['s'][2] * (1.818 / 100));
    $railway_spay_total['e'][2] = round($railway_total['e'][2] * ((1.818 / 100) - (1.55 / 100)));
    
    # �έp�K�����O
    $bento_spay_total['s'] = round($bento_total['s'] * (1.818 / 100));
    $bento_spay_total['e'] = round($bento_total['e'] * ((1.818 / 100) - (1.55 / 100)));

    $outPOP = $lodday;# ��b��(8)
    $outPOP .= $lodday;# ����}�l���(8)
    $outPOP .= $stime_tt;# ����}�l�ɶ�(6)
    $outPOP .= $lodday;# ����������(8)
    $outPOP .= $etime;# ��������ɶ�(6)
    $outPOP .= spas_nabD($counp,6);# �I�ڥ������(6)
    $outPOP .= spas_nabD($amount,9);# �I�ڨ�d�`����(9)
    $outPOP .= spas_nabD($spay,7);# �y������O(7)
    $outPOP .= spas_nabD($counpc,6);# �����������(6)
    $outPOP .= spas_nabD($amountc,9);# ������d�`���B(9)
    $outPOP .= spas_nabD($bspay,7);# ��d�h������O(7)
    $outPOP .= spas_nabD($trd_num,6);# �Բӹ�b���(6)
    $outPOP .= spas_nabD(abs($railway_total['s'][1]),9);# �x�K�дڲb�B(����,��~)(9)
    $outPOP .= spas_nabD(abs($railway_total['e'][1]),9);# �x�K�дڲb�B(�L��)(9)
    $outPOP .= spas_nabD(abs($railway_spay_total['s'][1]),9);# �x�K����O�o�����B(����,��~)(9)
    $outPOP .= spas_nabD(abs($railway_spay_total['e'][1]),9);# �x�K����O�o�����B(�L��)(9)
    $outPOP .= spas_nabD(abs($railway_total['s'][2]),9);# ���K�дڲb�B(����,��~)(9)
    $outPOP .= spas_nabD(abs($railway_total['e'][2]),9);# ���K�дڲb�B(�L��)(9)
    $outPOP .= spas_nabD(abs($railway_spay_total['s'][2]),9);# ���K����O�o�����B(����,��~)(9)
    $outPOP .= spas_nabD(abs($railway_spay_total['e'][2]),9);# ���K����O�o�����B(�L��)(9)
    $outPOP .= spas_nabD(abs($bento_total['s']),9);# �K��дڲb�B(����,��~)(9)
    $outPOP .= spas_nabD(abs($bento_total['e']),9);# �K��дڲb�B(�L��)(9)
    $outPOP .= spas_nabD(abs($bento_spay_total['s']),9);# �K�����O�o�����B(����,��~)(9)
    $outPOP .= spas_nabD(abs($bento_spay_total['e']),9);# �K�����O�o�����B(�L��)(9)   
    $outPOP .= "\r\n";#����Ÿ�(2)

    $outPOP = str_replace('/','',$outPOP);

    $outTxt = $outPOP.$outTxt;

    # �O���n�e�� NEC ���q��(HEADER + BODY)
    $fp=fopen("/vhost/close_file/".$hohsn."_do.txt","w");
    fputs($fp,$outTxt);
    fclose($fp);

    # �}�l�e��
    $FSIze = strlen($outTxt);
    $SendNEC='901A*'.$FSIze.'#'; 
    $txtnb = strlen($SendNEC);

    # ���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
    $fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout);
    if($fp){
        $goNEC = @fwrite($fp, $SendNEC, $txtnb);
        $bkNEC = @fread($fp, $FreadNB);

        
        # �O���n�e�� NEC ��b���� 1 �q�q��
        $log_value = array(
            'file_name' => $self_name,
            'send_nec' => $SendNEC,# �e NEC
            'bk_nec' => $bkNEC,# NEC �^�ǵ��G
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
                'bk_nec' => $bkNEC2,# NEC �^�ǵ��G
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
            
            # �O���n�e�� NEC ��b���� 2 �q�q��
            $log_value['msg'] = '901A-2-' . $isOk; # ��b���G
            DBLog($log_table, $log_subject, $log_value);
        }
    }

    echo $isOk;
    exit;
?>
