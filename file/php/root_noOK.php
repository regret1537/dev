<?php
    include_once('mysql.inc'); 

    $link = mylink();

    include('ch_root.inc'); 

    $DBtabel='uorder_gwp';
    $sOrder_Table = 'uorder';

    //===================================================
    if( $D=='ask' && $aa ){
        include('trainMsg.inc');
        
        $str = "SELECT uorder_gwp.*,pack2,pack3,AES_DECRYPT(goid,'$key_str_ch') as goid,AES_DECRYPT(bkid,'$key_str_ch') as bkid FROM uorder_gwp where odnb='".mysql_real_escape_string($aa)."' limit 1 ";
        $qq = myquery($str, $link);
        $ax = @mysql_fetch_array($qq);

        if( $N=='C' ){
            $strC = "SELECT uorder.*,pack2,pack3,AES_DECRYPT(goid,'$key_str_ch') as goid,AES_DECRYPT(bkid,'$key_str_ch') as bkid FROM uorder where 1 ";
            if( $ax[goid] ) $strC .= "and goid=AES_ENCRYPT('".mysql_real_escape_string($ax[goid])."','$key_str_ch') ";
            if( $ax[gosn] ) $strC .= "and gosn=AES_ENCRYPT('".mysql_real_escape_string($ax[gosn])."','$key_str_ch') ";
            if( $ax[bkid] ) $strC .= "and bkid='".mysql_real_escape_string($ax[bkid])."' ";
            if( $ax[bksn] ) $strC .= "and bksn='".mysql_real_escape_string($ax[bksn])."' ";
            $strC .= " limit 1 ";
            $qqC = myquery($strC, $link);
            $axC = @mysql_fetch_array($qqC);
            
            echo htmlspecialchars($ax[rtime])." , ���v�渹: ".htmlspecialchars($ax[gwpaysn])." , ���B: ".htmlspecialchars($ax[amount]).'<br>';
            echo htmlspecialchars($axC[rtime])." , ���v�渹: ".htmlspecialchars($axC[gwpaysn])." , ���B: ".htmlspecialchars($axC[amount]).'<br>';
            
            $isASK = '<form action="root_noOK.php?D=ask&N=y&aa='.htmlspecialchars($aa).'" method="post" onClick="return confirm('."'�T�w�n��NEC��?'".');"><input type="submit" value="��NEC" class=font09></form>';
            echo $isASK;
            exit;
        }
	
        if( $N=='y' ){
            //=============���q�����PACKET NEC
            if( $ax[goid] && $ax[gosn] && $ax[bkid] && $ax[bksn] ){
                $SendNEC='001A*'.$ax[goid].'*'.$ax[gosn].'*'.$ax[bkid].'*'.$ax[bksn].'#'; 
            } else {
                $SendNEC='001A*'.$ax[goid].'*'.$ax[gosn].'*          *      #'; 
            }
            $txtnb = strlen($SendNEC);

			$fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout); //���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
            if($fp){
                $goNEC = @fwrite($fp, $SendNEC, $txtnb);
                $bkNEC = @fread($fp, $FreadNB);
                fclose($fp);
            }
            //==========================================
            if(!$bkNEC){ //�䤣��
                echo '�L�^��';
                exit;
            }
            echo $bkNEC.'<br>';
            //=========================================�S#��Ƥ���
            $bctnb = strlen($bkNEC); $bctnb=$bctnb-1;
            $VCAS=substr($bkNEC, $bctnb,1);

            $bkNEC = str_replace('#','',$bkNEC);
            $Cxc = explode('*', $bkNEC);
	
            echo $echoERRO = $trainMsg[$Cxc[1]].'<p>';
            $isASK = '<form action="root_noOK.php?D=ask&N=y2&aa='.$aa.'" method="post" onClick="return confirm('."'�T�w�n�����?'".');"><input type="submit" value="�n����" class=font09></form>';
            echo $isASK;
            exit;
        } 

        //===����
        if( $N=='y2' ){
        
            //=========================//�e��NEC������
            $sCard8 = $ax['card8'];
            $card_L6_R4 = str_replace('*', '', $sCard8); // �d���e6��4�X
            //----------------------------------------------------------------------------------------------
            $sUPPP = $ax['uppp'];
            $aCPPP = explode('*', $sUPPP);
            
            $sPack_2 = $ax['pack2'];
            $a002B = explode('*', $sPack_2);
            
            $iAmount = $ax['amount']; // ���v���\���B
            
            $iFee = $a002B[72]; // ����O
            
            // �K���`���B
            $iBento_Total = gen_003A_bento_total($a002B);
            
            $iNEC_003A_Amount = $iAmount - $iFee - $iBento_Total; // �e��NEC���沼���A�ݦ����K���`���B
            
            $sTrade_Time = $ax['rtime']; // ����ɶ�
            
            $iGo_NV_Bento_Num = $a002B[76] + 0; // �h�{���K��ƶq
            $iGo_NV_Bento_Total = $a002B[77] + 0; // �h�{���K����B
            $iGo_V_Bento_Num = $a002B[78] + 0; // �h�{���K��ƶq
            $iGo_V_Bento_Total = $a002B[79] + 0; // �h�{���K����B
            
            $iBK_NV_Bento_Num = $a002B[80] + 0; // �^�{���K��ƶq
            $iBK_NV_Bento_Total = $a002B[81] + 0; // �^�{���K����B
            $iBK_V_Bento_Num = $a002B[82] + 0; // �^�{���K��ƶq
            $iBK_V_Bento_Total = $a002B[83] + 0; // �^�{���K����B
            
            $SendNECData = array(
                '003A' => '003A',
                '������X(9)' => $ax[odnb],
                '�h�{�����Ҧr��(10)' => spas_nab($ax[goid], 10),
                '�h�{�w����(6)' => spas_nab($ax[gosn], 6),
                '�^�{�����Ҧr��(10)' => spas_nab($ax[bkid], 10),
                '�^�{�w����(6)' => spas_nab($ax[bksn], 6),
                '�h�{���H���i��(2)' => spas_nab($aCPPP[0], 2),
                '�h�{�ĵ����i��(2)' => spas_nab($aCPPP[1], 2),
                '�h�{�q�Ѳ��i��(2)' => spas_nab($aCPPP[2], 2),
                '�h�{�ݻٲ��i��(2)' => spas_nab($aCPPP[3], 2),
                '�^�{���H���i��(2)' => spas_nab($aCPPP[4], 2),
                '�^�{�ĵ����i��(2)' => spas_nab($aCPPP[5], 2),
                '�^�{�q�Ѳ��i��(2)' => spas_nab($aCPPP[6], 2),
                '�^�{�ݻٲ��i��(2)' => spas_nab($aCPPP[7], 2),
                // '�`����(5)' => '     ',
                '�`����(5)' => sprintf('%05s', $iNEC_003A_Amount),
                // '���vRRN(12)' => '', # ���ζ�, AAT �۱a�Ѽ�
                '����ɶ�yyyy/mm/dd hh:mm(16)' => $sTrade_Time,
                '��d�d���e����|�X(10)' => $card_L6_R4,
                // '�y�쫬�A(1)' => '', # ���ζ�, AAT �۱a�Ѽ�
                '�h�{���K��ƶq(1)' => spas_nab($iGo_NV_Bento_Num, 1),
                '�h�{���K����B(3)' => spas_nab($iGo_NV_Bento_Total, 3),
                '�h�{���K��ƶq(1)' => spas_nab($iGo_V_Bento_Num, 1),
                '�h�{���K����B(3)' => spas_nab($iGo_V_Bento_Total, 3),
                '�^�{���K��ƶq(1)' => spas_nab($iBK_NV_Bento_Num, 1),
                '�^�{���K����B(3)' => spas_nab($iBK_NV_Bento_Total, 3),
                '�^�{���K��ƶq(1)' => spas_nab($iBK_V_Bento_Num, 1),
                '�^�{���K����B(3)' => spas_nab($iBK_V_Bento_Total, 3),
            );
            
            $SendNEC = CreateSendNEC($SendNECData);
            $txtnb = strlen($SendNEC);
			$fp = fsockopen($sockUrl, $sockPORT, $errno, $errstr, $iAAT_Timeout); //���}IP,PORT,�^���N�X,�^���T��,���ݮɶ�
            if($fp){
                $goNEC = @fwrite($fp, $SendNEC, $txtnb);
                $bkNEC = @fread($fp, $FreadNB);
                fclose($fp);
            }
            echo $bkNEC.'<br>';
            
            $Sn = $bkNEC;
            $bkNEC = str_replace('#','',$bkNEC);
            $Cxc = explode('*', $bkNEC);
            echo $echoERRO = $trainMsg[$Cxc[1]].'<p>';
            $isASK = '<form action="root_noOK.php?D=ask&N=y3&aa='.htmlspecialchars($aa).'" method="post" onClick="return confirm('."'�T�w�n�ɵn��ƶ�?'".');"><input type=hidden name=Sn value="'.htmlspecialchars($Sn).'"><input type=hidden name=SendNEC value="'.htmlspecialchars($SendNEC).'"><input type="submit" value="�n�ɵn" class=font09></form>';
            echo $isASK;
            exit;
        }

        //===�ɵn
        if( $N=='y3' ){
        
            //=========================//�g�J������
            $str = "insert into uorder ";
            $str .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,eci,authnb,SendNEC) ";
            $str .= "values(0,'".mysql_real_escape_string($ax[odnb])."','".mysql_real_escape_string($ax[tmstp])."',AES_ENCRYPT('".mysql_real_escape_string($ax[goid])."','$key_str_ch'),'".mysql_real_escape_string($ax[gosn])."',AES_ENCRYPT('".mysql_real_escape_string($ax[bkid])."','$key_str_ch'),'".mysql_real_escape_string($ax[bksnd])."','".mysql_real_escape_string($ax[amount])."','".mysql_real_escape_string($ax[rtime])."','".mysql_real_escape_string($ax[card8])."','".mysql_real_escape_string($ax[guolu])."','".mysql_real_escape_string($ax[rclose])."','".mysql_real_escape_string($ax[rcarcel])."','".mysql_real_escape_string($ax[gwpaysn])."','over','".mysql_real_escape_string($ax[glwhere])."','".mysql_real_escape_string($ax[pack2])."','".mysql_real_escape_string($Sn)."','".mysql_real_escape_string($ax[uppp])."','".mysql_real_escape_string($ax[spay])."','".mysql_real_escape_string($ax[eci])."','".mysql_real_escape_string($ax[authnb])."','".mysql_real_escape_string($SendNEC)."')";
            $result = myquery($str, $link);
            echo 'OK';
            exit;
        }
    }


    //==========================================================================================
    if($eci==5){ $eci5='selected'; } else { $eci5=''; }
    if($eci==6){ $eci6='selected'; } else { $eci6=''; }
    if($eci==7){ $eci7='selected'; } else { $eci7=''; }


    function ChecKchA($a,$b){
        if($a==$b){ 
            $c='checked';
        } else {
            $c='';
        }
        return $c;
    }


    if($USER_root){
        include_once('root_top.inc');
?>
    <script language="JavaScript">
        <!--
        function ymdSelect(src, dst) {
            var si = src.selectedIndex;
            for(var i = 0; i < dst.options.length; i++) {
                if(dst.options[i].value == src.options[si].value) {
                    dst.options[i].selected = true;
                    break;
                }
            }
        }

        function isReadyCard (form) {   
            if (form.cd6.value != ""  && form.cd4.value == ""){
                alert ("�d�ߤ覡���~�A�п�J�d���e6��4�X�d��");
                return false;
            }
            if (form.cd6.value == ""  && form.cd4.value != ""){
                alert ("�d�ߤ覡���~�A�п�J�d���e6��4�X�d��");
                return false;
            }
            return true;
        }
        // -->
    </script>
    <BR>
    <b>����b�ة��Ӻ޲z��</b>
    <table border="0" cellpadding="4" cellspacing="0" class=font09>
        <form method="post" action="<?=$CGI?>" name="addfrm" onSubmit="return isReadyCard(this)">
            <tr>
                <td>
                    <input type="checkbox" name="askdt">������\���ѵ��� , 
                    <input type="checkbox" name="Oneday" value=1>�@�ѨC�p�ɪ��q , 
                    <input type="checkbox" name="Onemonth" value=1>����C�Ѫ��q , 
                    <br>
                    <input type="checkbox" name="go" value=1 <?=htmlspecialchars(ChecKchA($go,1))?>>�ʲ� , 
                    <input type="checkbox" name="bk" value=1 <?=htmlspecialchars(ChecKchA($bk,1))?>>�h�� , 
                    <input type="checkbox" name="glu" value=1 <?=htmlspecialchars(ChecKchA($glu,1))?>>��� , 
                    <input type="checkbox" name="over" value=1 <?=htmlspecialchars(ChecKchA($over,1))?>>�ɵn , 
                    <input type="checkbox" name="noclose" value=1 <?=htmlspecialchars(ChecKchA($noclose,1))?>>�����b , 
                    ��J�϶�: �}�l
                    <select name="sy" onChange="ymdSelect(this, this.form.ey);">
                    <?php
                        if(!$sy){
                            $sy=date("Y");
                        }
                        for($y=2004;$y<=$hohsnY;$y++){
                            if($sy==$y){
                                echo '<option value="'.htmlspecialchars($y).'" selected>'.htmlspecialchars($y);
                            } else {
                                echo '<option value="'.htmlspecialchars($y).'">'.htmlspecialchars($y);
                            }
                        }
                    ?>
                    </select>�~
                    <select name="sm" onChange="ymdSelect(this, this.form.em);">
                    <?php
                        if(!$sm){
                            $sm=date("m");
                        }
                        for($m=1;$m<=12;$m++){
                            $m = sprintf("%02d", $m);
                            if($sm==$m){
                                echo '<option value="'.htmlspecialchars($m).'" selected>'.htmlspecialchars($m);
                            } else {
                                echo '<option value="'.htmlspecialchars($m).'">'.htmlspecialchars($m);
                            }
                        }
                    ?>
                    </select>��
                    <select name="sd" onChange="ymdSelect(this, this.form.ed);">
                    <?php
                        if(!$sd){ $sd=date("d"); }
                        for($m=1;$m<=31;$m++){
                            $m = sprintf("%02d", $m);
                            if($sd==$m){
                                echo '<option value="'.htmlspecialchars($m).'" selected>'.htmlspecialchars($m);
                            } else {
                                echo '<option value="'.htmlspecialchars($m).'">'.htmlspecialchars($m);
                            }
                        }
                    ?>
                    </select>��
                    <input type=text name=Shour size=3>�� 
                    ����
                    <select name="ey">
                    <?php
                        if(!$ey){ $ey=date("Y"); }
                        for($y=2004;$y<=$hohsnY;$y++){
                            if($ey==$y){
                                echo '<option value="'.htmlspecialchars($y).'" selected>'.htmlspecialchars($y);
                            } else {
                                echo '<option value="'.htmlspecialchars($y).'">'.htmlspecialchars($y);
                            }
                        }
                    ?>
                    </select>�~
                    <select name="em">
                    <?php
                        if(!$em){ $em=date("m"); }
                        for($m=1;$m<=12;$m++){
                            $m = sprintf("%02d", $m);
                            if($em==$m){
                                echo '<option value="'.htmlspecialchars($m).'" selected>'.htmlspecialchars($m);
                            } else {
                                echo '<option value="'.htmlspecialchars($m).'">'.htmlspecialchars($m);
                            }
                        }
                    ?>
                    </select>��
                    <select name="ed">
                    <?php
                        if(!$ed){ $ed=date("d"); }
                        for($m=1;$m<=31;$m++){
                            $m = sprintf("%02d", $m);
                            if($ed==$m){
                                echo '<option value="'.htmlspecialchars($m).'" selected>'.htmlspecialchars($m);
                            } else {
                                echo '<option value="'.htmlspecialchars($m).'">'.htmlspecialchars($m);
                            }
                        }
                    ?>
                    </select>�� 
                    <input type=text name=Ehour size=3>��  , eci<select name="eci">
                            <option value="">
                            <option value="5" <?=htmlspecialchars($eci5)?>>5</option>
                            <option value="6" <?=htmlspecialchars($eci6)?>>6</option>
                            <option value="7" <?=htmlspecialchars($eci7)?>>7</option>
                        </select>
                    �渹:<input type="text" name="odnb" size="6" value="<?=htmlspecialchars($odnb)?>"> 
                    ��d�渹:<input type="text" name="gwsn" size="6" value="<?=htmlspecialchars($gwsn)?>"> 
                    ���B:<input type="text" name="amot" size="6" value="<?=htmlspecialchars($amot)?>"> 
                </td>
            </tr>
            <tr>
                <td>
                    ����j�M: 
                    �h�{ID:<input type="text" name="goid" size="16" value="<?=htmlspecialchars($goid)?>">
                    �h�{�q���N�X:<input type="text" name="gosn" size="6" value="<?=htmlspecialchars($gosn)?>">
                    �^�{ID:<input type="text" name="bkid" size="16" value="<?=htmlspecialchars($bkid)?>">
                    �^�{�q���N�X:<input type="text" name="bksn" size="6" value="<?=htmlspecialchars($bksn)?>">
                    <?php
                        if (date("Ymd")<"20150716"){
                    ?>
                    �d��8�X<input type="text" name="cd8" value="<?=htmlspecialchars($cd8)?>" size=8 maxlength=8>
                    <?php
                        }
                    ?>
                    �d���e6��4�X:<input type="text" name="cd6" value="<?=htmlspecialchars($cd6)?>" size=6 maxlength=6> &nbsp
                    <input type="text" name="cd4" value="<?=htmlspecialchars($cd4)?>" size=4 maxlength=4>
                    <input type="submit" name="submit" value="�j�M">
                </td>
            </tr>
            <tr>
                <td style="color:red">
                    <b>�j�M�`�N�ƶ��G1. �j�M�e6��4�ɡA�ݭn�e6�B��4����J�A���j�M����~�|�ͮġC
                    <?php
                        if (date("Ymd")<"20150716"){
                    ?>
                    &nbsp 2. �p�G�n�j�M�ª���8�X�A�Цb�d��8�X�d�ߧY�i�C
                    <?php
                        }
                    ?></b>
                </td>
            </tr>
        </form>
    </table>


<?php
        //�@��user�u��d�ߤT�Ӥ�
        $USER_root_arr = split(',',$USER_root);
        $query_limit = ($USER_root_arr[1]=='1' ? 6 : 3);
        $star_mk = mktime(00,00,00,date('m')-$query_limit,date('d'),date('Y'));
        $end_mk = mktime(23,59,59,date('m'),date('d'),date('Y'));
          
        //===================================================������\���ѵ���
        if( $askdt ){
            $starYYYYMMDD = $sy.$sm.$sd;
            $endYYYYMMDD = $ey.$em.$ed;
            
            //�@��user�u��d�ߤT�Ӥ�
            $starSearch_date = date("Ymd",$star_mk);
            $endSearch_date = date("Ymd",$end_mk);
          
          
            $strp = "SELECT count(aa) FROM askdt where yyyymmdd >= '".mysql_real_escape_string($starYYYYMMDD)."' and yyyymmdd <= '".mysql_real_escape_string($endYYYYMMDD)."' and (yyyymmdd >= '".mysql_real_escape_string($starSearch_date)."' and yyyymmdd <= '".mysql_real_escape_string($endSearch_date)."') and isok=0 ";
            $qqp = myquery($strp, $link);
            $conpay = mysql_fetch_row($qqp);
            $counNO=$conpay[0];	//-------------�`����

            $strp = "SELECT count(aa) FROM askdt where yyyymmdd >= '".mysql_real_escape_string($starYYYYMMDD)."' and yyyymmdd <= '".mysql_real_escape_string($endYYYYMMDD)."' and (yyyymmdd >= '".mysql_real_escape_string($starSearch_date)."' and yyyymmdd <= '".mysql_real_escape_string($endSearch_date)."') and isok=1 ";
            $qqp = myquery($strp, $link);
            $conpay = mysql_fetch_row($qqp);
            $counOK=$conpay[0];	//-------------�`����

            echo "<p>������\���ѵ���<p>�϶�: ".htmlspecialchars($sy)."/".htmlspecialchars($sm)."/".htmlspecialchars($sd)." - ".htmlspecialchars($ey)."/".htmlspecialchars($em)."/".htmlspecialchars($ed)." <br><br> ������\: ".htmlspecialchars($counOK)." �� / �������: ".htmlspecialchars($counNO)." �� ";
            exit;
        }

        //=================�@�ѨC�p�ɪ��q
        if( $Oneday ){
            include('Oneday.php'); 
            exit;
        }

        //=================����C�Ѫ��q
        if( $Onemonth ){
            include('Onemonth.php'); 
            exit;
        }

        if(($Shour)&&($Ehour)){
            if($Shour=='a') $Shour=0;
            if($Ehour=='a') $Ehour=0;
            $starDAY = mktime($Shour,00,00,$sm,$sd,$sy);
            $endDAY = mktime($Ehour,59,59,$em,$ed,$ey);
        } else {
            $starDAY = mktime(00,00,00,$sm,$sd,$sy);
            $endDAY = mktime(23,59,59,$em,$ed,$ey);
        }

        if($starDAY<$star_mk){
?>
    <script language="JavaScript">
        alert('�ȯ�d��<? echo date('Y�~m��d��',$star_mk); ?>�᪺���!');
        location.href="root_noOK.php";
    </script>
<?php
        }
        
        # �@�P�d�߱���
        $sql_cond = ' WHERE tmstp BETWEEN "' . mysql_real_escape_string($starDAY) . '" AND "' . mysql_real_escape_string($endDAY) . '"';
        $sql_cond .= ' AND gwpaysn!=""';
        $sql_cond .= ' AND tmstp BETWEEN "' . mysql_real_escape_string($star_mk) . '" AND "' . mysql_real_escape_string($end_mk) . '"';
        if($go){ $sql_cond .= ' AND amount > 0'; }
        if($over){ $sql_cond .= ' AND necrrn="over"'; }
        if($bk){ $sql_cond .= ' AND amount < 0'; }
        if($goid){ $sql_cond .= ' AND goid=AES_ENCRYPT("' . mysql_real_escape_string($goid) . '","' . mysql_real_escape_string($key_str_ch) . '")'; }
        if($bkid){ $sql_cond .= ' AND bkid=AES_ENCRYPT("' . mysql_real_escape_string($bkid) . '","' . mysql_real_escape_string($key_str_ch) . '")'; }
        if($gosn){ $sql_cond .= ' AND gosn="' . mysql_real_escape_string($gosn) . '"'; }
        if($bksn){ $sql_cond .= ' AND bksn="' . mysql_real_escape_string($bksn) . '"'; }
        if($cd8){ $sql_cond .= ' AND card8="' . mysql_real_escape_string($cd8) . '"'; }
        if($cd6 || $cd4){ $sql_cond .= ' AND card8="' . mysql_real_escape_string($cd6) . '******' . mysql_real_escape_string($cd4) . '"'; }
        if($glu){ $sql_cond .= ' AND guolu="1"'; }
        if($eci){ $sql_cond .= ' AND eci="' . mysql_real_escape_string($eci) . '"'; }
        if($gwsn){ $sql_cond .= ' AND gwpaysn ="' . mysql_real_escape_string($gwsn) . '"'; }
        if($odnb){ $sql_cond .= ' AND odnb = "' . mysql_real_escape_string($odnb) . '"'; }
        if($amot){ $sql_cond .= ' AND amount ="' . mysql_real_escape_string($amot) . '"'; }
        if($noclose) { $sql_cond .= ' AND odnb NOT IN (SELECT odnb FROM uorder WHERE tmstp BETWEEN "' . mysql_real_escape_string($starDAY) . '" AND "' . mysql_real_escape_string($endDAY) . '" AND gwpaysn!="")'; }
        if($amot=='0'){ $sql_cond .= ' AND amount =0'; }
        
        # ���o�ŦX������v�渹
        $sel_sql = 'SELECT gwpaysn, amount FROM ' . $DBtabel;
        $sel_sql .= $sql_cond;
        if (TEST_MODE){
            write_log('debug_log', 'All trade SQL(root_noOK.php)', array('sql' => $sel_sql));
        }
        $sel_qry = myquery($sel_sql, $link);
        $pay_sn_ary = array();
        $amount = 0;
        while($sel_ary = @mysql_fetch_array($sel_qry)){
            # �O�����v�渹
            $t_pay_sn = $sel_ary['gwpaysn'];
            if (!in_array($t_pay_sn, $pay_sn_ary)) {
                array_push($pay_sn_ary, $t_pay_sn);
            }
            
            # �֥[�`���B
            $amount += $sel_ary['amount'];
        }
        mysql_free_result($sel_qry);
        
        # �`����
        $coun = count($pay_sn_ary);
        
        # ���o�K����
        $bento_info = array();
        if (!empty($pay_sn_ary)) {
            $pay_sn_list = implode(',', $pay_sn_ary);
            $sel_sql = "SELECT gw_pay_sn, go_nv_ben_num, go_nv_ben_total, go_v_ben_num, go_v_ben_total, bk_nv_ben_num, bk_nv_ben_total, bk_v_ben_num, bk_v_ben_total FROM " . TB_BENTO;
            $sel_sql .= " WHERE gw_pay_sn IN ($pay_sn_list)";
            $sel_sql .= " ORDER BY sn ";
            if (TEST_MODE){
                write_log('debug_log', 'Bento SQL(root.php)', array('sql' => $sel_sql));
            }
            $sel_qry = myquery($sel_sql, $link);
            
            while ($sel_ary = @mysql_fetch_array($sel_qry)) {
                $bento_info[$sel_ary['gw_pay_sn']] = $sel_ary;
            }
            mysql_free_result($sel_qry);
        }
        

        $SCROLL = 100;	// �X��n���@��
        $P=(($P)?"$P":'1');
        $from=($P-1)*$SCROLL;

        include('root_page.inc');
?>

    <table width="95%" border="0" cellpadding="3" cellspacing="1" bgcolor=#999999 class=font09>
        <tr bgcolor=#dddddd align="center">
            <td>��</td>
            <td>����ɶ�</td>
            <td>�渹</td>
            <td>�h�{ID</td>
            <td>�q���N�X</td>
            <td>�^�{ID</td>
            <td>�q���N�X</td>
            <td>������B</td>
            <td>�d�����X</td>
            <td>���</td>
            <td>��d�渹</td>
            <td>NEC RRN</td>
            <td>����O</td>
            <td>���b</td>
            <td>eci</td>
            <td>���v�X</td>
            <td>�h�{���K��</td>
            <td>���B</td>
            <td>�h�{���K��</td>
            <td>���B</td>
            <td>�^�{���K��</td>
            <td>���B</td>
            <td>�^�{���K��</td>
            <td>���B</td>
            <td>��NEC</td>
        </tr>

<?

        function sp_Spppcn($a){	//����
            $a = str_replace('    ***   ','',$a); //�N�Ů殳��
            if(!$a) $a = '&nbsp;'; 
                else $a = htmlspecialchars($a);
            return($a);
        }

        function sp_Glu($a){	//���
            if($a==1){ 
                $a = '<font color=#009900>�O</font>';
            } else {
                $a = '&nbsp;';
            } 
            return($a);
        }

        function sp_CanColo($a){	//�������B���r
            if($a<0){ 
                $a = '<font color=#ff0000>'.htmlspecialchars($a).'</font>';
            } else {
                $a = htmlspecialchars($a);
            } 
            return($a);
        }

        # ���o�������
        $sel_sql = 'SELECT ' . $DBtabel . '.*,AES_DECRYPT(pack2,"' . $key_str_ch . '") AS pack2,AES_DECRYPT(pack3,"' . $key_str_ch . '") AS pack3,AES_DECRYPT(goid,"' . $key_str_ch . '") AS goid,AES_DECRYPT(bkid,"' . $key_str_ch . '") AS bkid FROM ' . $DBtabel;
        $sel_sql .= $sql_cond;
        $sel_sql .= ' ORDER BY rtime DESC LIMIT ' . $from . ',' . $SCROLL;;
        if (TEST_MODE){
            write_log('debug_log', 'Page trade SQL(root_noOK.php)', array('sql' => $sel_sql));
        }
        $sel_qry = myquery($sel_sql, $link);
        while($ax = @mysql_fetch_array($sel_qry)){
            $t_pay_sn = $ax['gwpaysn'];# ���v�渹
            $t_bento_info = $bento_info[$t_pay_sn];# �����K���T
            
            $straa = "SELECT odnb FROM uorder where odnb='".mysql_real_escape_string($ax[odnb])."' limit 1 ";
            $qqaa = myquery($straa, $link);
            $axaa = @mysql_fetch_array($qqaa);

            if($axaa['odnb']){ 
                $bgcolOR='FFDDAA'; 
                $isDok = '<td>O</td>';
            } else if($ax['rclose']){ 
                $bgcolOR='FFFFFF'; 
                $isDok = '<td>O</td>';
            } else { 
                $bgcolOR='aaaaaa'; 
                if($USER_root_arr[2]==9){
                    if($ax['amount']<0){ $DoitPay = 'minus'; $upamount = 0-$ax['amount']; } else { $DoitPay = 'close'; $upamount = $ax['amount']; }
                    $isDok = '<form action="tarin_close.php?aa='.$ax['aa'].'&s='.$t_pay_sn.'&a='.$upamount.'&t='.$DoitPay.'" method="post" onClick="return confirm('."'�T�w�n�����b��?'".');" target=_blank><td><input type="submit" value="����" class=font09></td></form>';
                } else {
                    $isDok = '<td>X</td>';
                }
            }
            if( $ax['odnb'] ){
                $isASK = '<form action="root_noOK.php?D=ask&N=C&aa='.$ax['odnb'].'" method="post" onClick="return confirm('."'�T�w�n�d��ƶ�?'".');" target=_blank><input type="submit" value="�d���" class=font09></form>';
            } else {
                $isASK = '';
            }

            // ID ���X
            $ax['goid'] = substr($ax['goid'],0,4).'***'.substr($ax['goid'],-3);
            $ax['bkid'] = substr($ax['bkid'],0,4).'***'.substr($ax['bkid'],-3);


            $as++;
            echo '<tr bgcolor='.htmlspecialchars($bgcolOR).' align="center">
            <td>'.htmlspecialchars($as).'</td>
            <td>'.htmlspecialchars($ax['rtime']).'</td>
            <td>'.htmlspecialchars($ax['odnb']).'</td>
            <td>'.sp_Spppcn($ax['goid']).'</td>
            <td>'.sp_Spppcn($ax['gosn']).'</td>
            <td>'.sp_Spppcn($ax['bkid']).'</td>
            <td>'.sp_Spppcn($ax['bksn']).'</td>
            <td>'.sp_CanColo($ax['amount']).'</td>
            <td>'.htmlspecialchars($ax['card8']).'</td>
            <td>'.sp_Glu($ax['guolu']).'</td>
            <td>'.htmlspecialchars($t_pay_sn).'</td>
            <td>'.htmlspecialchars($ax['necrrn']).'</td>
            <td>'.sp_Spppcn($ax['spay']).'</td>
            '.$isDok.'
            <td>'.sp_Spppcn($ax['eci']).'</td>
            <td>'.O_SPACE($ax['authnb']).'</td>
            <td>'.O_SPACE($t_bento_info['go_nv_ben_num']).'</td>
            <td>'.O_SPACE($t_bento_info['go_nv_ben_total']).'</td>
            <td>'.O_SPACE($t_bento_info['go_v_ben_num']).'</td>
            <td>'.O_SPACE($t_bento_info['go_v_ben_total']).'</td>
            <td>'.O_SPACE($t_bento_info['bk_nv_ben_num']).'</td>
            <td>'.O_SPACE($t_bento_info['bk_nv_ben_total']).'</td>
            <td>'.O_SPACE($t_bento_info['bk_v_ben_num']).'</td>
            <td>'.O_SPACE($t_bento_info['bk_v_ben_total']).'</td>
            <td>'.$isASK.'</td>
            </tr>';
        }
        mysql_free_result($sel_qry);
        
        $gwAmount = $amount/100*$psChcn;
?>
    </table>
    <br>
    ���϶��p�p�`�B: <?=htmlspecialchars($amount)?> ��<font color=#ffffff> , ��ɪA�ȶO:<?=htmlspecialchars($gwAmount)?> </font><br>
    <br>
<?php
        include('root_page.inc');
        include_once('root_low.inc');
        exit;

    //===================================���n��	
    } else {
        include_once('root_top1.inc'); 
?>
    <table width="761" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width=154 background="img/menu_bg.gif" valign=top>
                <table width=100% height=259  border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td background="img/menu.gif" valign=top class=font09h15>
                        </td>
                    </tr>
                </table>
                </td>
                <td width=585 align=center background="img/center.gif">
                    <FONT style="PADDING-RIGHT: 1px; PADDING-LEFT: 1px; FONT-SIZE: 16px; FILTER: glow(color=#222222,strength=3); PADDING-BOTTOM: 1px; COLOR: #ffffff; PADDING-TOP: 1px; HEIGHT: 18px" color=#222222>
                        �� �z �� �� �� �n �J
                    </FONT>
                    <br>
                    <br>
                    <script language="JavaScript">
                        <!--
                        //===============================�T�{�����//
                        function isZeo(id) {
                            if(id.value != "")
                            {  return true; } else 
                            {  return false; }
                        }    
                        //============================== �P�O//
                        function isReady(form) {
                            if(isZeo(form.id) == false){
                                alert("�b���|����J!!");
                                form.id.focus();
                                return false;
                            }

                            if(isZeo(form.ps) == false){
                                alert("�K�X�|����J!!");
                                form.ps.focus();
                                return false;
                            }

                            return true;
                        }
                        //-->
                    </script>
                    <table border="0" cellpadding="4" cellspacing="0">
                        <form method="post" action="root_noOK.php" name="addfrm" onSubmit="return isReady(this)">
                            <tr>
                                <td>�b��: </td>
                                <td><input type="text" name="id" size=10 maxlength="10"></td>
                            </tr>
                            <tr>
                                <td>�K�X: </td>
                                <td><input type="password" name="ps" size=10 maxlength="10"></td>
                            </tr>
                            <tr>
                                <td colspan=2 align=center><input type="submit" name="submit" value="�n�J">  <input type="reset" value="����"></td>
                            </tr>
                        </form>
                    </table>
                    <br>
                    <br>
    <?php
        include_once('other_link.inc');
    ?>
                </td>
                <td width=22 align=center background="img/right_bg.gif">
                </td>
            </tr>
        </table>

<?php
        include_once('low.inc'); 
    }
?>
