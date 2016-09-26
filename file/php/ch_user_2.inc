<?php
    // ���J���ո��
    $sTest_BK_NEC = '';
    if (TEST_MODE) {
        include('test_config.php');
        $sTest_Key = basename(__FILE__);
        if (isset($aTest_Params[$sTest_Key]['bkNEC'])) {
            $sTest_BK_NEC = $aTest_Params[$sTest_Key]['bkNEC'];
        }
    }

    include('ch_timeout.inc'); //�O�_timeout
    include('trainMsg.inc');
	
	$sPHP_Name = basename(__FILE__, '.php'); // PHP �W��
	
    //==========================================
    if(!$_SESSION[$sess_USER]['pk1']){ //���n��
?>
    <script language="JavaScript">
        <!--
        alert('01��Ʀs�����~!!�������Ҧ��y��������!���s�ӹL!!');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
    }
    //==========================================

    $USER_arr = explode('*',$_SESSION[$sess_USER]['pk1']); //���O���
    $bento_info = array(
        array('num' => $USER_arr[27]),// �h�{���K��
        array('num' => $USER_arr[28]),// �h�{���K��
        array('num' => $USER_arr[29]),// �^�{���K��
        array('num' => $USER_arr[30]),// �^�{���K��
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
    //=============���q�����PACKET NEC
    $SendNEC='002A*'.idspch($USER_arr[3]).'*'.compnbch($USER_arr[4]).'*'.idspch($USER_arr[13]).'*'.compnbch($USER_arr[14]).'*'.nb2ttt($goA).'*'.nb2ttt($goB).'*'.nb2ttt($goC).'*'.nb2ttt($goD).'*'.nb2ttt($bkA).'*'.nb2ttt($bkB).'*'.nb2ttt($bkC).'*'.nb2ttt($bkD).'*'.bentoNum($bento_info[0]['num']).'*'.bentoNum($bento_info[1]['num']).'*'.bentoNum($bento_info[2]['num']).'*'.bentoNum($bento_info[3]['num']).'#'; 
    $txtnb = strlen($SendNEC);

    // �O�� 002A �q��
    $sMasked_AAT_NEC_Stat = mask_nec_stat($SendNEC); // ���Ӹ�����X
    write_log('nec_trace_002', 'nec msg', array('SendNEC' => $sMasked_AAT_NEC_Stat));
    
    if (TEST_MODE and $sTest_BK_NEC != '') {
        // ���ո��
        $bkNEC = $sTest_BK_NEC;
    } else {
        $fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout); //���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
        if($fp){
            $goNEC = @fwrite($fp, $SendNEC, $txtnb);
            $bkNEC = @fread($fp, $FreadNB);
            fclose($fp);
        } else {
			$sLine_Message = $sockMachineName . ' , �x�K AAT �L�k�s�u';
			// $oLine->send_line(GROUP_CODE, $sLine_Message, $sPHP_Name);
        }
    }
    
    // �O�� 002B �q��
    $sMasked_AAT_NEC_Stat = mask_nec_stat($bkNEC); // ���Ӹ�����X
    write_log('nec_trace_002', 'nec msg', array('bkNEC' => $sMasked_AAT_NEC_Stat));
    
    $hohsnYMD = date("Ymd");

    //==========================================
    if(!$bkNEC){ //�䤣��
        $isok = 0;
        //=========================//���O������d�����
        $str = "insert into askdt (aa,sendnec,rtnec,yyyymmdd,isok) values(0,'$SendNEC','$bkNEC','$hohsnYMD','$isok')";
        myquery($str, $link);
?>
    <script language="JavaScript">
        <!--
        alert('02��Ʀs�����~!!�������Ҧ��y��������!���s�ӹL!!');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
    }

    //===================  �ڳo�� connections ���F�ɷ|�^�ǵ��A 944X*#
    include('ch_944X.php');


    //=========================================�S#��Ƥ���
    $bctnb = strlen($bkNEC); $bctnb=$bctnb-1;
    $VCAS=substr($bkNEC, $bctnb,1);

    $bkNEC = str_replace('#','',$bkNEC);
    $Cxc = explode('*', $bkNEC);

    $echoERRO = $trainMsg[$Cxc[1]];

    if(($VCAS!='#')||($Cxc[0]!='002B')){ 
        $isok = 0;
        //=========================//���O������d�����
        $str = "insert into askdt (aa,sendnec,rtnec,yyyymmdd,isok) values(0,'$SendNEC','$bkNEC','$hohsnYMD','$isok')";
        myquery($str, $link);
?>
    <script language="JavaScript">
        <!--
        alert('03��Ʀs�����~!!�������Ҧ��y��������!���s�ӹL!!\n\n�i���]:\n\n<?php echo $echoERRO; ?>!');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
    }

    //=========================================�n������
    if($Cxc[1]!='000'){
        $isok = 0;
        //=========================//���O������d�����
        $str = "insert into askdt (aa,sendnec,rtnec,yyyymmdd,isok) values(0,'$SendNEC','$bkNEC','$hohsnYMD','$isok')";
        myquery($str, $link);
?>
    <script language="JavaScript">
        <!--
        alert('04�n�J����!�Э��s�n�J!\n\n�i���]:\n\n<?php echo $echoERRO; ?>!');
        location.href="pay.php";
        // -->
    </script>
<?php
        exit;
    }

    $isok = 1;
    //=========================//���O������d�����
    $str = "insert into askdt (aa,sendnec,rtnec,yyyymmdd,isok) values(0,'$SendNEC','$bkNEC','$hohsnYMD','$isok')";
    myquery($str, $link);

    $_SESSION[$sess_USER]['pk2'] = $bkNEC;	//�O�����
    $_SESSION[$sess_USER]['ppp'] = $goA.'*'.$goB.'*'.$goC.'*'.$goD.'*'.$bkA.'*'.$bkB.'*'.$bkC.'*'.$bkD;	//�w���i�ưO�����
?>