<?php
    include('mysql.inc'); 
    include('ch_timeout.inc'); //�O�_timeout
    include('trainMsg.inc');
	
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
    
    if(!$_SESSION[$sess_USER]['uaa']){
?>
    <script language="JavaScript">
        alert('01<?php echo $language['_Alert_txt_026']; ?>');
        location.href="pay_bk.php";
    </script>
<?php
        exit;
    }

    $aa = $_SESSION[$sess_USER]['uaa'];
    $bspay = $_SESSION[$sess_USER]['bspay'];	// �O���h������O
    $bamount = $_SESSION[$sess_USER]['bamount'];	// �O���h���`���B(���t����O�A�t�K��)

    //=============���q�����
    $link = mylink();
    $str = "SELECT uorder.*,pack2,pack3,AES_DECRYPT(goid,'$key_str_ch') as goid,AES_DECRYPT(bkid,'$key_str_ch') as bkid FROM uorder where aa='".mysql_real_escape_string($aa)."' limit 1 ";
    $qq = myquery($str, $link);
    $ax = @mysql_fetch_array($qq);

    $Cxc = explode('*', $ax['pack2']);

    if($Cxc[2]==2){
        $sanbb = substr($Cxc[3], 0,3).'xxx'.substr($Cxc[3], 6,4);
        $asa = $language['_payPHP_009'].' - '.$language['_payPHP_006'].': '.$sanbb.' , '.$language['_payPHP_005'].': '.$Cxc[4].'<br>';
        $sanxx = substr($Cxc[17], 0,3).'xxx'.substr($Cxc[17], 6,4);
        $asa .= $language['_payPHP_010'].' - '.$language['_payPHP_006'].': '.$sanxx.' , '.$language['_payPHP_005'].': '.$Cxc[18].'<br>';
    } else {
        $sanbb = substr($Cxc[3], 0,3).'xxx'.substr($Cxc[3], 6,4);
        $asa = $language['_payPHP_009'].' - '.$language['_payPHP_006'].': '.$sanbb.' , '.$language['_payPHP_005'].': '.$Cxc[4];
    }

    if($Cxc[3]==2){	//�Ӧ^�{
        $goUtimeYM = substr($Cxc[5], 0,6); 	$goUtimeD = substr($Cxc[5], 6,2);
        $bkUtimeYM = substr($Cxc[15], 0,6); 	$bkUtimeD = substr($Cxc[15], 6,2);
    } else {
        $goUtimeYM = substr($Cxc[5], 0,6); 	$goUtimeD = substr($Cxc[5], 6,2);
        $bkUtimeYM = ''; 	$bkUtimeD = '';
    }

    $Cxc[11] = $Cxc[11]+1-1;
    $Cxc[25] = $Cxc[25]+1-1;
    $gbalpp = $Cxc[11]+$Cxc[25];	//�X�p�i��


    //��X���b��
    $NecPay= $bamount;	// NEC���B
    $pptime =  date("Y"."/"."m"."/"."d"." "."H".":"."i"); //�ɶ�


    //=========�g�Jgwp�ƥ��h�ڸ��
    $strgwp = "insert into uorder_gwp ";
    $strgwp .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,bspay,eci) ";
    $strgwp .= "values(0,'".mysql_real_escape_string($ax[odnb])."','".mysql_real_escape_string($NTIME)."',AES_ENCRYPT('".mysql_real_escape_string($ax[goid])."','$key_str_ch'),'".mysql_real_escape_string($ax[gosn])."',AES_ENCRYPT('".mysql_real_escape_string($ax[bkid])."','$key_str_ch'),'".mysql_real_escape_string($ax[bksn])."','".mysql_real_escape_string($NecPay)."','".mysql_real_escape_string($pptime)."','".mysql_real_escape_string($ax[card8])."','".mysql_real_escape_string($ax[guolu])."','0','".mysql_real_escape_string($get_close)."','".mysql_real_escape_string($ax[gwpaysn])."','','".mysql_real_escape_string($ax[glwhere])."','".mysql_real_escape_string($ax[pack2])."','".mysql_real_escape_string($ax[pack3])."','".mysql_real_escape_string($ax[uppp])."','".mysql_real_escape_string($CaneclPm)."','".mysql_real_escape_string($bspay)."','".mysql_real_escape_string($ax[eci])."')";
    if (!TEST_MODE) {
        $resultgwp = myquery($strgwp, $link);
    }

    //=========NEC�T�{�h��
    function spas_idL($a){	//id�ɨ�10���ɪŮ�
        $idal = strlen($a);
        if($idal<10){
            for($u=$idal;$u<10;$u++){
                $a .= ' ';
            }
        }
        return($a);
    }
    $SendNEC = '005A*'.$ax['odnb'].'*'.spas_idL($ax[goid]).'*'.spas_nab($ax['gosn'],6).'*'.spas_idL($ax[bkid]).'*'.spas_nab($ax['bksn'],6).'*'.spas_nab($NecPay,5).'*'.spas_nab($bspay,4).'*'.$pptime.'#';
    $txtnb = strlen($SendNEC);

    // �O�� 005A �q��
    $sMasked_AAT_NEC_Stat = mask_nec_stat($SendNEC); // ���Ӹ�����X
    write_log('nec_trace_005', 'nec msg', array('SendNEC' => $sMasked_AAT_NEC_Stat));
    
    if (TEST_MODE and $sTest_BK_NEC != '') {
        // ���ո��
        $bkNEC = $sTest_BK_NEC;
    } else {
        $fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout); //���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
        if($fp){
            $goNEC = fwrite($fp, $SendNEC, $txtnb);
            $bkNEC = fread($fp, $FreadNB);
            fclose($fp);
        }
    }

    // �O�� 005B �q��
    $sMasked_AAT_NEC_Stat = mask_nec_stat($bkNEC); // ���Ӹ�����X
    write_log('nec_trace_005', 'nec msg', array('bkNEC' => $sMasked_AAT_NEC_Stat));

    //==========================================
    if(!$bkNEC){ //�䤣��
?>
    <script language="JavaScript">
        alert('02<?php echo $language['_Alert_txt_026']; ?>');
        location.href="pay_bk.php";
    </script>
<?php
        exit;
    }

    //===================  �ڳo�� connections ���F�ɷ|�^�ǵ��A 944X*#
    include('ch_944X.php');

    //=========================================�S#��Ƥ���
    $bctnb = strlen($bkNEC); $bctnb=$bctnb-1;
    $VCAS=substr($bkNEC, $bctnb,1);


    //�^�ǽd�� 005B*000*129070080350*A123456789*930161*xxxxxxxxxx*xxxxxx*0013*20160125235958#
    $bkNEC = str_replace('#','',$bkNEC);
    $CxcN = explode('*', $bkNEC);

    $PACK3_n = $ax[pack3].'*'.$bkNEC;		//�[�J�s���^�ǫʥ]�O��

    //=========================================�n������
    if($CxcN[1]!='000'){
        $echoERRO = $trainMsg[$CxcN[1]].'('.$Cxc[1].')';
?>
    <script language="JavaScript">
        alert('03<?php echo $language['_Alert_txt_027']; ?>:\n\n<?php echo htmlspecialchars($echoERRO); ?>!');
        location.href="pay_bk.php";
    </script>
<?php
        exit;
    }

    if(($VCAS!='#')||($CxcN[0]!='005B')){ 
?>
    <script language="JavaScript">
        alert('04<?php echo $language['_Alert_txt_026']; ?>');
        location.href="pay_bk.php";
    </script>
<?php
        exit;
    }

    //======�h�ڪ��B�j�������B,�������h��
    if($bspay>$bamount){
?>
    <script language="JavaScript">
        alert('05<?php echo $language['_Alert_txt_013']; ?>');
        location.href="pay_bk.php";
    </script>
<?php
        exit;
    }


    $Namount = $bamount-$CxcN[7];
    if(	$Namount <=0 ){
        $Namount = 0;
        $get_close = 1;
    } else {
        $Namount = 0-$Namount;	//��t��
    }
    $CaneclPm = $CxcN[7];

    //=========�g�J�ƥ��h�ڸ��
    $strbk = "insert into uorder_bk ";
    $strbk .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,bspay,eci) ";
    $strbk .= "values(0,'".mysql_real_escape_string($ax[odnb])."','".mysql_real_escape_string($NTIME)."',AES_ENCRYPT('".mysql_real_escape_string($ax[goid])."','$key_str_ch'),'".mysql_real_escape_string($ax[gosn])."',AES_ENCRYPT('".mysql_real_escape_string($ax[bkid])."','$key_str_ch'),'".mysql_real_escape_string($ax[bksn])."','".mysql_real_escape_string($Namount)."','".mysql_real_escape_string($pptime)."','".mysql_real_escape_string($ax[card8])."','".mysql_real_escape_string($ax[guolu])."','0','".mysql_real_escape_string($get_close)."','".mysql_real_escape_string($ax[gwpaysn])."','".mysql_real_escape_string($CxcN[2])."','".mysql_real_escape_string($ax[glwhere])."','".mysql_real_escape_string($ax[pack2])."','".mysql_real_escape_string($PACK3_n)."','".mysql_real_escape_string($ax[uppp])."','".mysql_real_escape_string($CaneclPm)."','".mysql_real_escape_string($CxcN[7])."','".mysql_real_escape_string($ax[eci])."')";
    if (!TEST_MODE) {
        $resultbk = myquery($strbk, $link);
    }

    //=========================//�^gwpay���b
    function URLopen($url){
          ini_set('user_agent','MSIE 4\.0b2;');
          $dh = fopen("$url",'r');
          $result = fread($dh,8192);
          return $result;
    }

    $gwsr = $ax['gwpaysn'];
    $VchOn = substr($gwsr, -4,4);	//�ˬd�X
    $CCamount = 0-$Namount;
    $VchOn = ($VchOn * $CCamount) % 3;


    if($CxcN[8]>1){	//�HNEC�^�Ǯɶ�����
        if(strlen($CxcN[8])==14){
            $pptime = substr($CxcN[8],0,4)."/".substr($CxcN[8],4,2)."/".substr($CxcN[8],6,2)." ".substr($CxcN[8],8,2).":".substr($CxcN[8],10,2);
            $NceCEHOtime=$CxcN[8];
            $Nt_YYYY=substr($CxcN[8],0,4);
            $Nt_MM=substr($CxcN[8],4,2);
            $Nt_DD=substr($CxcN[8],6,2);
            $Nt_HH=substr($CxcN[8],8,2);
            $Nt_II=substr($CxcN[8],10,2);
            $Nt_SS=substr($CxcN[8],12,2);
            $NTIME = mktime($Nt_HH,$Nt_II,$Nt_SS,$Nt_MM,$Nt_DD,$Nt_YYYY);
        }
    }

    if (!TEST_MODE) {
        if($Namount){		//�h�ڪ��B�j�������B,���eecpay�@�h��
            $SEND_DT="[$GWECpauUrl/tarin_close.php?s=$gwsr&a=$CCamount&t=minus&c=$VchOn&Ntime=$NceCEHOtime]";
            $fp = fsockopen($sockUrl, $sockPORTclose, $errno, $errstr, 20); //���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
            if($fp){
                $goClos = @fwrite($fp,$SEND_DT,200);
                $bkClos = @fread($fp,15);
                fclose($fp);
            }
        }
    } else {
        // ���ո��
        $bkClos = 'inok';
    }
    

    if(substr($bkClos, 0,4)=='inok'){	//���b���\
    } else {
		$sLine_Message = $sockMachineName . ' , �x�K�h�����b����, ��d�渹:' . $gwsr . ', �渹:' . $ax['odnb'] . ', ���B:' . $Namount . '��!';
		// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);
    }
    
    $get_close = 1;


    //=========�g�J�h�ڸ��
    $str = "insert into uorder ";
    $str .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,bspay,eci) ";
    $str .= "values(0,'".mysql_real_escape_string($ax[odnb])."','".mysql_real_escape_string($NTIME)."',AES_ENCRYPT('".mysql_real_escape_string($ax[goid])."','$key_str_ch'),'".mysql_real_escape_string($ax[gosn])."',AES_ENCRYPT('".mysql_real_escape_string($ax[bkid])."','$key_str_ch'),'".mysql_real_escape_string($ax[bksn])."','".mysql_real_escape_string($Namount)."','".mysql_real_escape_string($pptime)."','".mysql_real_escape_string($ax[card8])."','".mysql_real_escape_string($ax[guolu])."','0','".mysql_real_escape_string($get_close)."','".mysql_real_escape_string($ax[gwpaysn])."','".mysql_real_escape_string($CxcN[2])."','".mysql_real_escape_string($ax[glwhere])."','".mysql_real_escape_string($ax[pack2])."','".mysql_real_escape_string($PACK3_n)."','".mysql_real_escape_string($ax[uppp])."','".mysql_real_escape_string($CaneclPm)."','".mysql_real_escape_string($CxcN[7])."','".mysql_real_escape_string($ax[eci])."')";
    if (!TEST_MODE) {
        $result = myquery($str, $link);
    }

    //===��s timestamp ���
    $str = "update uorder_bk set tmstp='".mysql_real_escape_string($NTIME)."' where odnb='".mysql_real_escape_string($ax[odnb])."' and amount='".mysql_real_escape_string($Namount)."' and rtime='".mysql_real_escape_string($pptime)."' limit 1 ";
    if (!TEST_MODE) {
        $result = mysql_query($str, $link);
    }

    //===��s timestamp ���
    $str = "update uorder_gwp set tmstp='".mysql_real_escape_string($NTIME)."' where odnb='".mysql_real_escape_string($ax[odnb])."' and amount='".mysql_real_escape_string($Namount)."' and rtime='".mysql_real_escape_string($pptime)."' limit 1 ";
    if (!TEST_MODE) {
        $result = mysql_query($str, $link);
    }

    //===================================================
    include('top_allopen.inc'); 

?>
    <table width="991" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width=154 valign=top align=center class=font09h15>
                <?php include_once('menu.inc'); ?>
                <?php include_once('pop.inc'); ?>
            </td>
            <td width=837 bgcolor=#ffffff valign=top>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align=center background="img/center.gif">
                            <br>
                            <b>
                                <font color=#3B699A>
                                    <?php echo $language['_payING_086']; ?>
                                </font>
                            </b>
                            <br>
                            <br>
                            <table width=600 border="0" cellpadding="2" cellspacing="0" class=font09>
                                <tr>
                                    <td>
                                        <?php echo $language['_payING_099']; ?>
                                        <br>
                                        <br>
                                        <?php echo $language['_payING_100']; ?> <font color=#ff0000><b> <?php echo htmlspecialchars($Namount); ?></b></font>
                                        <?php echo $language['_payING_029']; ?>
                                        <br>
                                        <br>
                                        <?php echo $language['_payING_101']; ?>
                                        <br>
                                        <hr size=1>
                                        <?php echo $language['_payING_102']; ?>:
                                        <br>
                                        <?php echo $asa; ?>
                                    </td>
                                </tr>
                            </table>
                            <table width=600 border="0" cellpadding="4" cellspacing="0" class=font09>
                                <tr>
                                    <form action="index.php">
                                        <td align=center>
                                            <input type="submit" value="<?php echo $language['_payING_103']; ?>">
                                        </td>
                                    </form>
                                    <form action="javascript:void(window.print())">
                                        <td align=center>
                                            <input type="submit" value="<?php echo $language['_payING_104']; ?>">
                                        </td>
                                    </form>
                                </tr>
                            </table>
                            <img src="images/low.gif">
                            <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

<?php 
    $_SESSION[$sess_USER]['uaa']='';
    session_destroy(); 

    include_once('low.inc');
?>
