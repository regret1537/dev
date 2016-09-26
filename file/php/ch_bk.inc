<?php
    include('ch_timeout.inc'); //�O�_timeout
    include('trainMsg.inc');

    // ���J���ո��
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

        function spas_idL($a){	//id�ɨ�10���ɪŮ�
            $idal = strlen($a);
            if($idal<10){
                for($u=$idal;$u<10;$u++){
                    $a .= ' ';
                }
            }
            return($a);
        }

        //=========================//�g�J�d�M�O��
        $isday = date("Ymd");
        $istime = date("His");
        $goid = ($id1) ? spas_idL($id1) : spas_idL($id2) ;
        $gosn = ($go1sn) ? sprintf("%06s",$go1sn) : sprintf("%06s",$go2sn) ;
        $strgwp = "insert into return_search_log (aa,goid,gosn,isday,istime) values(0,AES_ENCRYPT('$goid','$key_str_ch'),'$gosn','$isday','$istime')";
        myquery($strgwp, $link);
        $aa = mysql_insert_id();
        //=============================//�g�J�d�M�O��end

        $go1sn = sprintf("%06s", $go1sn); 
        $id1 = spas_idL($id1);
        //=============���q�����PACKET NEC
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
                //=========================//�g�J�d�M�O��log
                $Error_log_TXT = $language['_Alert_txt_031'];
                $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
                myquery($strgwp, $link);
                //=============================//�g�J�d�M�O��end
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

        // �u�j�M2�Ӥ뤺������A�������Ҥ������
        if (!TEST_MODE) {
            $str .= " and tmstp>'$tmstpN'";
        }
        $chk_sql = $str;
        $str .= " and rclose!=0 limit 1 ";

        $qq = myquery($str, $link);
        $ax = @mysql_fetch_array($qq);
        $Cxc = explode('*', $ax['pack2']);

        // �ˬd�ӵ�����O�_�h�L��
        $chk_sql .= " and rcancel=1 limit 1 ";
        $chk_qry = myquery($chk_sql, $link);
        $chk_row_num = mysql_num_rows($chk_qry);

        $necpay = 0;// �h�ڤ��ݰe���B��NEC�A�N���B���M��
        if($ax['odnb'] and $chk_row_num < 1){
            if($howgo==2){ 
                $SendNEC='004A*'.$ax['odnb'].'*'.$id2.'*'.$go2sn.'*'.$id3.'*'.$go3sn.'*'.sprintf("%05s", $necpay).'*'.$ax['rtime'].'#'; 
            } else {
                $SendNEC='004A*'.$ax['odnb'].'*'.$id1.'*'.$go1sn.'*          *      '.'*'.sprintf("%05s", $necpay).'*'.$ax['rtime'].'#'; 
            }
            $txtnb = strlen($SendNEC);
        } else {
            //=========================//�g�J�d�M�O��log
            $Error_log_TXT = $language['_Alert_txt_028'];
            $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
            myquery($strgwp, $link);
            //=============================//�g�J�d�M�O��end
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
        
        // �O�� 004A �q��
        $sMasked_AAT_NEC_Stat = mask_nec_stat($SendNEC); // ���Ӹ�����X
        write_log('nec_trace_004', 'nec msg', array('SendNEC' => $sMasked_AAT_NEC_Stat));

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

        // �O�� 004B �q��
        $sMasked_AAT_NEC_Stat = mask_nec_stat($bkNEC); // ���Ӹ�����X
        write_log('nec_trace_004', 'nec msg', array('bkNEC' => $sMasked_AAT_NEC_Stat));

        //==========================================
        if(!$bkNEC){ //�䤣��
            //=========================//�g�J�d�M�O��log
            $Error_log_TXT = $language['_Alert_txt_031'];
            $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
            myquery($strgwp, $link);
            //=============================//�g�J�d�M�O��end
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

        //===================  �ڳo�� connections ���F�ɷ|�^�ǵ��A 944X*#
        include('ch_944X.php');

        //=========================================�S#��Ƥ���
        $bctnb = strlen($bkNEC); $bctnb=$bctnb-1;
        $VCAS=substr($bkNEC, $bctnb,1);

        $bkNEC = str_replace('#','',$bkNEC);
        $CxcN = explode('*', $bkNEC);

        //=========================================�n������
        if($CxcN[1]!='000'){
            $echoERRO = $trainMsg[$CxcN[1]];
            //=========================//�g�J�d�M�O��log
            $Error_log_TXT = $language['_Alert_txt_027'];
            $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
            myquery($strgwp, $link);
            //=============================//�g�J�d�M�O��end
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
            //=========================//�g�J�d�M�O��log
            $Error_log_TXT = $language['_Alert_txt_031'];
            $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
            myquery($strgwp, $link);
            //=============================//�g�J�d�M�O��end
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

        $_SESSION[$sess_USER]['uaa'] = $ax['aa'];	// �O����ƧǸ�
        $_SESSION[$sess_USER]['bspay'] = $CxcN[6];	// �O���h������O
        $_SESSION[$sess_USER]['bamount'] = $CxcN[7];	// �O���h���`���B(���t����O�A�t�K��)
        //=========================================
    } else {
        //=========================//�g�J�d�M�O��log
        $Error_log_TXT = $language['_Alert_txt_031'];
        $strgwp = "update return_search_log set memo='$Error_log_TXT' where aa='$aa' limit 1 "; 
        myquery($strgwp, $link);
        //=============================//�g�J�d�M�O��end
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