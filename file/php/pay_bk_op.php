<?php
    include('mysql.inc'); 
    include('ch_bk.inc'); 

    function nbok($a){
        $a = $a + 1 - 1;
        return($a);
    }

    $Cxc = explode('*', $ax['pack2']);
    $Dxd = explode('*', $ax['pack3']);
    $Pxp = explode('*', $ax['uppp']);


    $gogoaa='';
    if($Cxc[32]){ $gogoaa .= '['.$trainWhatP[$Cxc[32]].'] '.$trainWppl[$Cxc[33]].': '.nbok($Cxc[35]).$language['_payING_089'].nbok($Cxc[34]).' '.$language['_payING_029'].'<br>'; }
    if($Cxc[37]){ $gogoaa .= '['.$trainWhatP[$Cxc[37]].'] '.$trainWppl[$Cxc[38]].': '.nbok($Cxc[40]).$language['_payING_089'].nbok($Cxc[39]).' '.$language['_payING_029'].'<br>'; }
    if($Cxc[42]){ $gogoaa .= '['.$trainWhatP[$Cxc[42]].'] '.$trainWppl[$Cxc[43]].': '.nbok($Cxc[45]).$language['_payING_089'].nbok($Cxc[44]).' '.$language['_payING_029'].'<br>'; }
    if($Cxc[47]){ $gogoaa .= '['.$trainWhatP[$Cxc[47]].'] '.$trainWppl[$Cxc[48]].': '.nbok($Cxc[50]).$language['_payING_089'].nbok($Cxc[49]).' '.$language['_payING_029'].'<br>'; }
    if($Cxc[52]){ $gogoaa .= '['.$trainWhatP[$Cxc[52]].'] '.$trainWppl[$Cxc[53]].': '.nbok($Cxc[55]).$language['_payING_089'].nbok($Cxc[54]).' '.$language['_payING_029'].'<br>'; }
    if($Cxc[57]){ $gogoaa .= '['.$trainWhatP[$Cxc[57]].'] '.$trainWppl[$Cxc[58]].': '.nbok($Cxc[60]).$language['_payING_089'].nbok($Cxc[59]).' '.$language['_payING_029'].'<br>'; }
    if($Cxc[62]){ $gogoaa .= '['.$trainWhatP[$Cxc[62]].'] '.$trainWppl[$Cxc[63]].': '.nbok($Cxc[65]).$language['_payING_089'].nbok($Cxc[64]).' '.$language['_payING_029'].'<br>'; }
    if($Cxc[67]){ $gogoaa .= '['.$trainWhatP[$Cxc[67]].'] '.$trainWppl[$Cxc[68]].': '.nbok($Cxc[70]).$language['_payING_089'].nbok($Cxc[69]).' '.$language['_payING_029'].'<br>'; }

    # 便當明細
    $with_bento = trim($Cxc[75]);
    $bento_info = array(
        array('trip' => $trainWhatP[1], 'name' => $language['_payING_131'], 'total' => $Cxc[77], 'num' => $Cxc[76]),# 去程葷便當
        array('trip' => $trainWhatP[1], 'name' => $language['_payING_132'], 'total' => $Cxc[79], 'num' => $Cxc[78]),# 去程素便當
        array('trip' => $trainWhatP[2], 'name' => $language['_payING_131'], 'total' => $Cxc[81], 'num' => $Cxc[80]),# 回程葷便當
        array('trip' => $trainWhatP[2], 'name' => $language['_payING_132'], 'total' => $Cxc[83], 'num' => $Cxc[82]),# 回程素便當
    );
    foreach ($bento_info as $info) {
        if ($info['num'] > 0) {
            $t_bento_total = nbok($info['total']);
            $t_bento_price = nbok($t_bento_total / $info['num']);
            $t_bento_num = nbok($info['num']);
            $gogoaa .= sprintf($language['_payING_136'], $info['trip'], $info['name'], $t_bento_num, $t_bento_price) . '<br/>';
        }
    }

    $cardno = '************'.substr($ax['card8'], 4,4);
    if($ax['guolu']){ $guolu=$language['_payING_057']; } else { $guolu=$language['_payING_046']; }

    $gp='';
    if($Pxp[0]){ for($i=0;$i<$Pxp[0];$i++){ $gp .= $language['_payPHP_009'].'-'.$language['_payING_012'].'*'; } }
    if($Pxp[1]){ for($i=0;$i<$Pxp[1];$i++){ $gp .= $language['_payPHP_009'].'-'.$language['_payING_013'].'*'; } }
    if($Pxp[2]){ for($i=0;$i<$Pxp[2];$i++){ $gp .= $language['_payPHP_009'].'-'.$language['_payING_014'].'*'; } }
    if($Pxp[3]){ for($i=0;$i<$Pxp[3];$i++){ $gp .= $language['_payPHP_009'].'-'.$language['_payING_022'].'*'; } }
    $bp='';
    if($Pxp[4]){ for($i=0;$i<$Pxp[4];$i++){ $bp .= $language['_payPHP_010'].'-'.$language['_payING_012'].'*'; } }
    if($Pxp[5]){ for($i=0;$i<$Pxp[5];$i++){ $bp .= $language['_payPHP_010'].'-'.$language['_payING_013'].'*'; } }
    if($Pxp[6]){ for($i=0;$i<$Pxp[6];$i++){ $bp .= $language['_payPHP_010'].'-'.$language['_payING_014'].'*'; } }
    if($Pxp[7]){ for($i=0;$i<$Pxp[7];$i++){ $bp .= $language['_payPHP_010'].'-'.$language['_payING_022'].'*'; } }

    $hwgo = explode('*', $gp);
    $hwbk = explode('*', $bp);

    function goPPis($w,$a,$z,$i,$o){
        if(($a)&&($w)){
            $g = $w.' , '.$i.': '.htmlspecialchars($a).' , '.$o.': '.htmlspecialchars($z).'<BR>';
        }
        return $g ;
    }

    $goPPP = '';
    $goPPP .= goPPis($hwgo[0],$Dxd[7],$Dxd[8],$language['_payING_058'],$language['_payING_059']);
    $goPPP .= goPPis($hwgo[1],$Dxd[9],$Dxd[10],$language['_payING_058'],$language['_payING_059']);
    $goPPP .= goPPis($hwgo[2],$Dxd[11],$Dxd[12],$language['_payING_058'],$language['_payING_059']);
    $goPPP .= goPPis($hwgo[3],$Dxd[13],$Dxd[14],$language['_payING_058'],$language['_payING_059']);
    $goPPP .= goPPis($hwgo[4],$Dxd[15],$Dxd[16],$language['_payING_058'],$language['_payING_059']);
    $goPPP .= goPPis($hwgo[5],$Dxd[17],$Dxd[18],$language['_payING_058'],$language['_payING_059']);
    $goPPP .= goPPis($hwgo[6],$Dxd[19],$Dxd[20],$language['_payING_058'],$language['_payING_059']);
    $goPPP .= goPPis($hwgo[7],$Dxd[21],$Dxd[22],$language['_payING_058'],$language['_payING_059']);
    $goPPP .= goPPis($hwgo[8],$Dxd[23],$Dxd[24],$language['_payING_058'],$language['_payING_059']);
    $goPPP .= goPPis($hwgo[9],$Dxd[25],$Dxd[26],$language['_payING_058'],$language['_payING_059']);

    $bkPPP = '';
    $bkPPP .= goPPis($hwbk[0],$Dxd[27],$Dxd[28],$language['_payING_058'],$language['_payING_059']);
    $bkPPP .= goPPis($hwbk[1],$Dxd[29],$Dxd[30],$language['_payING_058'],$language['_payING_059']);
    $bkPPP .= goPPis($hwbk[2],$Dxd[31],$Dxd[32],$language['_payING_058'],$language['_payING_059']);
    $bkPPP .= goPPis($hwbk[3],$Dxd[33],$Dxd[34],$language['_payING_058'],$language['_payING_059']);
    $bkPPP .= goPPis($hwbk[4],$Dxd[35],$Dxd[36],$language['_payING_058'],$language['_payING_059']);
    $bkPPP .= goPPis($hwbk[5],$Dxd[37],$Dxd[38],$language['_payING_058'],$language['_payING_059']);
    $bkPPP .= goPPis($hwbk[6],$Dxd[39],$Dxd[40],$language['_payING_058'],$language['_payING_059']);
    $bkPPP .= goPPis($hwbk[7],$Dxd[41],$Dxd[42],$language['_payING_058'],$language['_payING_059']);
    $bkPPP .= goPPis($hwbk[8],$Dxd[43],$Dxd[44],$language['_payING_058'],$language['_payING_059']);
    $bkPPP .= goPPis($hwbk[9],$Dxd[45],$Dxd[46],$language['_payING_058'],$language['_payING_059']);

    include('top.inc'); 


    if($Cxc[3]==2){
        $goUtimeYM = substr($Cxc[5], 0,6); 	$goUtimeD = substr($Cxc[5], 6,2);
        $bkUtimeYM = substr($Cxc[15], 0,6); 	$bkUtimeD = substr($Cxc[15], 6,2);
    } else {
        $goUtimeYM = substr($Cxc[5], 0,6); 	$goUtimeD = substr($Cxc[5], 6,2);
        $bkUtimeYM = ''; 	$bkUtimeD = '';
    }

?>
    <table width="991" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width=154 valign=top align=center class=font09h15>
                <? include_once('menu.inc'); ?>
                <? include_once('pop.inc'); ?>
            </td>
            <td width=837 bgcolor=#ffffff valign=top>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align=center background="img/center.gif">
                            <br>
                            <b>
                                <font color=#3B699A>
                                    <?=$language['_payING_086']?>
                                </font>
                            </b>
                            <br>
                            <table width=600 border="0" cellpadding="4" cellspacing="0" class=font09>
                                <tr>
                                    <td width=30%>
                                        <hr size=1>
                                    </td>
                                    <td width=40% align=center>
                                        <font color=#0F6C65>
                                            <u>
                                                <?=$language['_payING_075']?>
                                            </u>
                                        </font>
                                    </td>
                                    <td width=30%>
                                        <hr size=1>
                                    </td>
                                </tr>
                            </table>
                            <table width=600 border="0" cellpadding="2" cellspacing="0" class=font09>
                                <tr>
                                    <td colspan=3>
                                        <font color=#0F6C65>
                                            <?=$language['_payING_076']?>:
                                        </font>
                                        <br>
                                        <?=$language['_payING_077']?>: <?=htmlspecialchars($ax['rtime'])?> , <?=$language['_payING_078']?>: <?=htmlspecialchars($ax['amount'])?> <?=$language['_payING_029']?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=3>
                                        <hr size=1>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?=$language['_payING_003']?>: <?=htmlspecialchars($Cxc[4])?>
                                    </td>
                                    <td>
                                        <?=$language['_payING_004']?>: <?=htmlspecialchars($Cxc[7])?>
                                    </td>
                                    <td>
                                        <?=$language['_payING_005']?>: <?=$trainType[$Cxc[8]]?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?=$language['_payING_006']?>: <?=htmlspecialchars(substr($Cxc[5], 0,4))?>/<?=htmlspecialchars(substr($Cxc[5], 4,2))?>/<?=htmlspecialchars(substr($Cxc[5], 6,2))?> <?=htmlspecialchars(substr($Cxc[6], 0,2))?>:<?=htmlspecialchars(substr($Cxc[6], 2,2))?>
                                    </td>
                                    <td>
                                        <?=$language['_payING_008']?>: <?=$trainWhere[$Cxc[9]]?>
                                    </td>
                                    <td>
                                        <?=$language['_payING_009']?>: <?=$trainWhere[$Cxc[10]]?>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=top>
                                        <?=$language['_payING_010']?> 
                                        <B>
                                            <font color=#0F6C65>
                                                <?=htmlspecialchars(nbok($Cxc[11]))?>
                                            </font>
                                        </B> <?=$language['_payING_011']?>
                                    </td>
                                    <td colspan=2>
                                        <?=$goPPP?>
                                    </td>
                                </tr>
                            <?php
                                # 有無便當
                                if ($with_bento) {
                            ?>
                                <tr>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td>
                                        <?php
                                            # 去程葷便當
                                            echo SanitizHTML(sprintf($language['_payING_134'], nbok($bento_info[0]['num'])));
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            # 去程素便當
                                            echo SanitizHTML(sprintf($language['_payING_135'], nbok($bento_info[1]['num'])));
                                        ?>
                                    </td>
                                </tr>
                            <?php
                                }
                                
                                $Cxc[18]= str_replace(' ','',$Cxc[18]);
                                if($Cxc[18]){
                            ?>
                                <tr>
                                    <td colspan=3>
                                        <hr size=1>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?=$language['_payING_017']?>: <?=htmlspecialchars($Cxc[18])?>
                                    </td>
                                    <td>
                                        <?=$language['_payING_004']?>: <?=htmlspecialchars($Cxc[21])?>
                                    </td>
                                    <td>
                                        <?=$language['_payING_005']?>: <?=$trainType[$Cxc[22]]?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?=$language['_payING_006']?>: <?=htmlspecialchars(substr($Cxc[19], 0,4))?>/<?=htmlspecialchars(substr($Cxc[19], 4,2))?>/<?=htmlspecialchars(substr($Cxc[19], 6,2))?> <?=htmlspecialchars(substr($Cxc[20], 0,2))?>:<?=htmlspecialchars(substr($Cxc[20], 2,2))?>
                                    </td>
                                    <td>
                                        <?=$language['_payING_008']?>: <?=$trainWhere[$Cxc[23]]?>
                                    </td>
                                    <td>
                                        <?=$language['_payING_009']?>: <?=$trainWhere[$Cxc[24]]?>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=top>
                                        <?=$language['_payING_018']?> 
                                        <B>
                                            <font color=#0F6C65>
                                                <?=htmlspecialchars(nbok($Cxc[25]))?>
                                            </font>
                                        </B>
                                        <?=$language['_payING_011']?>
                                    </td>
                                    <td colspan=2>
                                        <?=$bkPPP?>
                                    </td>
                                </tr>
                                <?php
                                    # 有無便當
                                    if ($with_bento) {
                                ?>
                                <tr>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td>
                                        <?php
                                            # 回程葷便當
                                            echo SanitizHTML(sprintf($language['_payING_134'], nbok($bento_info[2]['num'])));
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                            # 回程素便當
                                            echo SanitizHTML(sprintf($language['_payING_135'], nbok($bento_info[3]['num'])));
                                        ?>
                                    </td>
                                </tr>
                            <?php
                                    }
                                }
                            ?>
                                <tr>
                                    <td colspan=3>
                                        <hr size=1>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=top>
                                        <?=$language['_payING_121']?>&nbsp;
                                    </td>
                                    <td colspan=2>
                                        <?=$gogoaa?>
                                        <br>
                                        <?=$language['_payING_028']?>: 
                                        <B>
                                            <font color=#0F6C65>
                                                <?=htmlspecialchars($ax['amount'])?>
                                            </font>
                                        </B> 
                                        <?=$language['_payING_029']?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=3>
                                        <hr size=1>
                                    </td>
                                </tr>
								<tr>
                                    <td valign=top colspan=3>
                                        <?php echo $language['_payING_094']; ?>
									</td>
								</tr>
								<tr>
									<td valign=top colspan=3>
										<span style="color:#0F6C65;">
                                            <?php echo $language['_payING_074']; ?>
                                        </span>
									</td>
								</tr>
								<?php
									if ($language['_payING_138'] !== '') {
										echo '<tr>' . PHP_EOL;
										echo '    <td colspan="3" style="font-weight:bold;">' . PHP_EOL;
										echo '        ' . $language['_payING_138'] . PHP_EOL;
										echo '    </td>' . PHP_EOL;
										echo '</tr>' . PHP_EOL;
									}
								?>
                            </table>

                            <table width=600 border="0" cellpadding="4" cellspacing="0" class=font09>
                                <script language="JavaScript">
                                    function mask(szLayer){
                                        if(navigator.appName == "Netscape"){
                                            
                                        }else{
                                            if(document.all[szLayer].style.visibility == "hidden"){
                                                horizon_start = (document.body.clientWidth - 778) / 2;
                                                document.all[szLayer].style.left = horizon_start+"px";
                                                document.all[szLayer].style.visibility = "visible";
                                            }else{
                                                document.all[szLayer].style.visibility = "hidden";
                                            }
                                        }         
                                    }

                                    function isReady(form) {
                                        form.submit.style.display = 'none';
                                        mask("mask");
                                    }
                                </script>
                                <form action="pay_bk_ok.php" method="post" onSubmit="return isReady(this)">
                                    <tr>
                                        <td colspan=2 align=center class=font09>
                                            <input type="submit" name="submit" value="<?=$language['_payING_095']?>" style="background:#ff0000;width:200px;height:40px;">
                                            <br>
                                            <font color=#ff0000>
                                                <?=$language['_payING_096']?>
                                            </font>
                                        </td>
                                    </tr>
                                </form>
                            </table>
                            <img src="img/center_2.gif">
                            <table width=80% border="0" cellpadding="4" cellspacing="0" class=font09>
                                <tr>
                                    <td valign=top width=40>
                                        <u>
                                        <?=$language['_payPHP_014']?> 
                                        </u>
                                    </td>
                                    <td valign=top>
                                        1.
                                    </td>
                                    <td valign=top>
                                    <?php
                                        if ($Lange_Us=='language_tw') {
                                            echo $language['_payING_072-1'];
                                        } else {
                                            echo $language['_payING_072'];
                                        }
                                    ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=top>
                                    </td>
                                    <td valign=top>
                                        2.
                                    </td>
                                    <td valign=top>
                                        <?=$language['_payING_073-1']?>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=top>
                                    </td>
                                    <td valign=top>
                                        3.
                                    </td>
                                    <td valign=top>
                                        <?=$language['_payING_074']?>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=top>
                                    </td>
                                    <td valign=top>
                                        4.
                                    </td>
                                    <td valign=top>
                                        <?=$language['_payING_097']?>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=top>
                                    </td>
                                    <td valign=top>
                                        5.
                                    </td>
                                    <td valign=top>
                                        <?=$language['_payING_098']?>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            <img src="images/low.gif">
                            <br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div id="mask" style="position:absolute;visibility:hidden;top:0px" align="center">
        <table border="0">
            <tr>
                <td id="mask_td" width="760" height="500" align="center" valign="middle">
                    <img src="img/clock.gif" border="0">
                </td>
            </tr>
        </table>
    </div>
<?php include_once('low.inc'); ?>
