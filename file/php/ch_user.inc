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
    
    
    // �d�߭q�����
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

    include('ch_timeout.inc'); //�O�_timeout
    include('trainMsg.inc');

    if($submit){
        function spas_idL($a){	//id�ɨ�10���ɪŮ�
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
        
        //=============���q�����PACKET NEC
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
        
        // �O�� 001A �q��
        $sMasked_AAT_NEC_Stat = mask_nec_stat($SendNEC); // ���Ӹ�����X
        write_log('nec_trace_001', 'nec msg', array('SendNEC' => $sMasked_AAT_NEC_Stat));

        //==========================================
        if (TEST_MODE and $sTest_BK_NEC != '') {
            // ���ո��
            $bkNEC = $sTest_BK_NEC;
        } else {
            $fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout); //���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
            if($fp){
                $goNEC = @fwrite($fp, $SendNEC, $txtnb);
                $bkNEC = @fread($fp, $FreadNB);
                fclose($fp);
            }
        }
        
        // �O�� 001B �q��
        $sMasked_AAT_NEC_Stat = mask_nec_stat($bkNEC); // ���Ӹ�����X
        write_log('nec_trace_001', 'nec msg', array('bkNEC' => $sMasked_AAT_NEC_Stat));
        
        if(!$bkNEC){ //�䤣��
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

        //===================  �ڳo�� connections ���F�ɷ|�^�ǵ��A 944X*#
        include('ch_944X.php');


        //=========================================�S#��Ƥ���
        $bctnb = strlen($bkNEC); $bctnb=$bctnb-1;
        $VCAS=substr($bkNEC, $bctnb,1);

        $bkNEC = str_replace('#','',$bkNEC);
        $Cxc = explode('*', $bkNEC);

        //=========================================�n������
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

        //�d�� 001B*000*1*D201203191*972233*20120325*1337*516  *6*185*149*02*1*          *      *        *    *     * *000*000*00* *D* #

        $_SESSION[$sess_USER]['pk1'] = $bkNEC;	//�O�����
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