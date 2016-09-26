<?php

    //綠界匯款出去的帳戶資料
    $bankOUT_email = 'sheryl@sunup.net'; 	//公司會計的信箱

    $bankOUT_hana_name = '綠界科技股份有限公司'; //華南用的
    $bankOUT_hana_sn = '117100183939';
    $bankOUT_hana_tt = '117';			//華南總行

    $bankOUT_taishin_name = '綠界科技股份有限公司'; //台新用的
    $bankOUT_taishin_sn = '20680100019186';
    $bankOUT_taishin_tt = '812';			//台新總行
    $bankOUT_taishin_id = '0687';			//台新分行

    $bankOUT_kotai_name = '綠界科技股份有限公司'; 	//國泰世華用的
    $bankOUT_kotai_sn = '220032002480';
    $bankOUT_kotai_tt = '0132206';						//國泰世華付款代碼

    $gw_unino = '97025978';						//綠界公司的統編


    //$pcidss_ch='';			//空,起動 pcidss 保護
    $pcidss_ch='1';			//1,停止 pcidss 保護
    //確認檔案有無
    if(!$pcidss_ch){
        $overISss = file_exists("/dev/shm/pcidss");
        if(!$overISss){
            echo '<center><p>System Stop!</center>';
            exit;
        }
    }


    $log_erro_no=6;	//登錄幾次失敗
    $inRoom_ck=time()+900;	//閒置時間 15分鐘=60*15

    //===============防範XSS跨站腳本攻擊
    $XssBAD=0;
    $aa_GET=$_GET;
    while(list($l, $r)=each($aa_GET)){

        $CHecK_BodyOUT.=$l.'='.$r.'<br>';
        
        $RRRR_r = strtolower($r);	//都先轉小寫
        $RRRR_r=str_replace('<br>','',$RRRR_r);
        $RRRR_r=str_replace('</br>','',$RRRR_r);
        $RRRR_r=str_replace('<br/>','',$RRRR_r);
        $RRRR_r=str_replace('<br />','',$RRRR_r);
        $RRRR_r=str_replace('<b>','',$RRRR_r);
        $RRRR_r=str_replace('<b/>','',$RRRR_r);
        $RRRR_r=str_replace('</b>','',$RRRR_r);
        $RRRR_r=str_replace('<hr>','',$RRRR_r);
        $RRRR_r=str_replace('<hr/>','',$RRRR_r);

    /*
        //如果有 http:// & ftp:// 的就踢掉
        $Xssbno_http=substr_count($RRRR_r,'http://');
        if($Xssbno_http) $XssBAD+=5;
        $Xssbno_ftp=substr_count($RRRR_r,'ftp://');
        if($Xssbno_ftp) $XssBAD+=5;
        //如果有 http:// & ftp:// 的就踢掉  end
    */

        $Xssbno_a=substr_count($RRRR_r,'<');
        if($Xssbno_a) $XssBAD+=$Xssbno_a;
        
        $Xssbno_b=substr_count($RRRR_r,'>');
        if($Xssbno_b) $XssBAD+=$Xssbno_b;
            
        $Xssbno_a=substr_count($RRRR_r,'"');
        if($Xssbno_a) $XssBAD+=$Xssbno_a;
            
        $Xssbno_a=substr_count($RRRR_r,"'");
        if($Xssbno_a) $XssBAD+=$Xssbno_a;
    }
    if($XssBAD>=4){
        
        $OMOMiuuy = 'ecpay-GET商代: '.$client.','.basename($PHP_SELF).' 被阻擋了';
        //==寄信給 over
        mail('over.chang@allpay.com.tw',mb_convert_encoding($OMOMiuuy,"Big5","UTF-8"),mb_convert_encoding($CHecK_BodyOUT,"Big5","UTF-8"),"From:gomos2@sunup.net\nMIME-Version: 1.0\nContent-Type: text/html; charset=big5\nContent-Transfer-Encoding: 8bit");


        $CHecK_BodyOUT='';

?>
    <script language="JavaScript">
        <!--
        alert("交易失敗!請與商家連絡!");
        location.href="http://www.ecpay.com.tw";
        // -->
    </script>
<?php
        exit;
    }

    $XssBAD=0;
    $aa_POST=$_POST;
    while(list($l, $r)=each($aa_POST)){
        if(($l!='ha')&&($l!='oder_list')&&($l!='OrderList')&&($l!='cOdrItem')&&($l!='hoho')&&($l!='hoho_name')){

            $CHecK_BodyOUT.=$l.'='.$r.'<br>';

            $RRRR_r = strtolower($r);	//都先轉小寫
            $RRRR_r=str_replace('<lang>','',$RRRR_r);
            $RRRR_r=str_replace('</lang>','',$RRRR_r);
            $RRRR_r=str_replace('<br>','',$RRRR_r);
            $RRRR_r=str_replace('<hr>','',$RRRR_r);
            $RRRR_r=str_replace('<br/>','',$RRRR_r);
            $RRRR_r=str_replace('<br />','',$RRRR_r);
            $RRRR_r=str_replace('<hr/>','',$RRRR_r);
            $RRRR_r=str_replace('<hr />','',$RRRR_r);

            $Xssbno_a=substr_count($RRRR_r,'<');
            if($Xssbno_a) $XssBAD+=$Xssbno_a;
        
            $Xssbno_b=substr_count($RRRR_r,'>');
            if($Xssbno_b) $XssBAD+=$Xssbno_b;
            
            $Xssbno_a=substr_count($RRRR_r,'"');
            if($Xssbno_a) $XssBAD+=$Xssbno_a;
            
            $Xssbno_a=substr_count($RRRR_r,"'");
            if($Xssbno_a) $XssBAD+=$Xssbno_a;
        }
    }
    if($XssBAD>=4){
        
        $OMOMiuuy = 'ecpay-POST商代: '.$client.','.basename($PHP_SELF).' 被阻擋了';
        //==寄信給 over
        mail('over.chang@allpay.com.tw',mb_convert_encoding($OMOMiuuy,"Big5","UTF-8"),mb_convert_encoding($CHecK_BodyOUT,"Big5","UTF-8"),"From: 'janme@sunup.net'\nMIME-Version: 1.0\nContent-Type: text/html; charset=big5\nContent-Transfer-Encoding: 8bit");
        $CHecK_BodyOUT='';

?>
    <script language="JavaScript">
        <!--
        alert("交易失敗!請與商家連絡!");
        location.href="http://www.ecpay.com.tw";
        // -->
    </script>
<?php

        //Header("Location:http://www.ecpay.com.tw"); 
        exit;
    }
    //===============防範XSS跨站腳本攻擊end

    define('TXT_MESSAGE_NEW_LINE',	"\r\n");
    
    //測試模式
    $test_mode = false;
    if (!$test_mode) {
        define("HOME_DIR", "/vhost/bk-file");
        $Rootmail='rd-group@greenworld.com.tw';
    } else {
        // 測試模式
        define("HOME_DIR", "/home/shawn.chang/" . date('Ymd'));
        $Rootmail='shawn.chang@greenworld.com.tw';
    }

    $BGcolor='#C9F380';
    //$key_str_ch='OvEr_3838';
    $key_str_ch='ax350svBgow81r4L';
    //=====================================================
    $WebName='GreenWorld - Payment Gateway';
    $IndexPG='index.php';
    $SCROLL=20;
    $CGI = basename($PHP_SELF);
    $test_card_no='4311952222222222';

    $Payps=0.028;		//代收服務費
    $Payps_07=0.024;		//代收服務費
    $BankSv=15;		//匯費
    $bankWithoutFee = array('中國信託', '台灣銀行', '臺灣銀行'); 	// 不需要匯費的銀行
    $WbankNB = count($bankWithoutFee); 
    $aStagePercent = array(                 			// 分期數和利息 % 數
        3   => 5,
        6   => 6.5,
        12  => 11,
        24  => 18,
        36  => 20
    );

    if(!$ecpayTOP_cdno){
         $ecpayTOP_cdno='';		//隱卡號
    } else {
        $uip = $_SERVER["REMOTE_ADDR"];
        //if(substr($uip,0,8)=='192.168.'){	//這是我們內網的電腦
            $_SESSION['ecpayTOP_cdno']=1;
        //}
    }

    //=====N花期的3D功能旗標
    $CITY3D_OPEN=1;	//1開,0失效
    //=====N中信/小店的3D功能旗標
    $CHIN3D_OPEN=1;	//1開,0失效

    //=====拒交前6碼
    $BADcardNO6 = array('456918','456919','547018','535317','456874','547018');
    //2007-10-30 加入國外偽卡: '547018','535317','456874'
    //2007-10-31 建中: 擋英國'547018'

    //=====MPI3D==============================================================
    $APIbankurl = array(
        '1' => '',						//聯信授權主機
        '2' => '',						//中信授權主機
        '3' => '',						//中信授權主機
        '4' => '',						//聯信授權主機
        '5' => 'http://192.168.8.61:2011',			//花旗授權主機
        '6' => 'http://192.168.8.61:2011',			//花旗授權主機
        '7' => 'https://epos.chinatrust.com.tw:2011',		//中信小店授權主機
        '8' => 'https://epos.chinatrust.com.tw:2011',		//中信小店授權主機
        '11' => 'https://ctnpg.twpay.com.tw:2099',		//新花旗授權主機
        '12' => 'https://ctnpg.twpay.com.tw:2099',		//新花旗授權主機
        //'13' => 'https://npg.hyweb.com.tw:2021',		//N花旗授權主機
        //'14' => 'https://npg.hyweb.com.tw:2021',		//N花旗授權主機
        '13' => 'https://www.hypay.com.tw:2011',		//N花旗授權主機
        '14' => 'https://www.hypay.com.tw:2011',		//N花旗授權主機
        '15' => '',						//EZPAY授權主機
        '16' => '',						//N中信授權主機
        '17' => '',						//N中信授權主機
        '18' => '',						//台新授權主機
        '19' => '',						//台新授權主機
    );
    $MPIbankurl = array(
        '1' => '',							//聯信3D主機
        '2' => '',							//中信3D主機
        '3' => '',							//中信3D主機
        '4' => '',							//聯信3D主機
        '5' => 'https://mpi.twpay.com.tw/index.asp',			//花旗3D主機
        '6' => 'https://mpi.twpay.com.tw/index.asp',			//花旗3D主機
        '7' => 'https://nmpi.chinatrust.com.tw/index.jsp',		//中信小店3D主機
        '8' => 'https://nmpi.chinatrust.com.tw/index.jsp',		//中信小店3D主機
        '11' => 'https://mpi.twpay.com.tw/index.asp',			//新花旗3D主機
        '12' => 'https://mpi.twpay.com.tw/index.asp',			//新花旗3D主機
        //'13' => 'https://mpi.twpay.com.tw/index.asp',			//N花旗3D主機
        '13' => 'https://www.hypay.com.tw/mpi/index.jsp',		//N花旗3D主機
        //'14' => 'https://mpi.twpay.com.tw/index.asp',			//N花旗3D主機
        '14' => 'https://www.hypay.com.tw/mpi/index.jsp',		//N花旗3D主機
        '15' => '',							//EZPAY授權主機
        '16' => 'https://nmpi.chinatrust.com.tw/index.jsp',	//N中信3D主機
        '17' => 'https://nmpi.chinatrust.com.tw/index.jsp',	//N中信3D主機
        '18' => '',	//台新3D主機
        '19' => '',	//台新3D主機
    );
    $VISA3Dcode = array(
        '1' => '',		//聯信
        '2' => '',		//中信
        '3' => '',		//中信
        '4' => '',		//聯信
        '5' => '405536',	//花旗
        '6' => '405536',	//花旗
        '7' => '429339',	//中信小店
        '8' => '429339',	//中信小店
        '11' => '405536',	//新花旗
        '12' => '405536',	//新花旗
        '13' => '405536',	//N花旗
        '14' => '405536',	//N花旗
        '15' => '',						//EZPAY授權主機
        '16' => '429339',		//N中信授權主機
        '17' => '429339',		//N中信授權主機
        '18' => '',	//台新授權主機
        '19' => '',	//台新授權主機
    );
    $MAST3Dcode = array(
        '1' => '',		//聯信
        '2' => '',		//中信
        '3' => '',		//中信
        '4' => '',		//聯信
        '5' => '541430',	//花旗
        '6' => '541430',	//花旗
        '7' => '522257',	//中信小店
        '8' => '522257',	//中信小店
        '11' => '541430',	//新花旗
        '12' => '541430',	//新花旗
        '13' => '541430',	//N花旗
        '14' => '541430',	//N花旗
        '15' => '',						//EZPAY授權主機
        '16' => '522257',		//N中信授權主機
        '17' => '522257',		//N中信授權主機
        '18' => '',					//台新授權主機
        '19' => '',					//台新授權主機
    );


    //=====ezpay用==============================================================
    $NPCIP = "127.0.0.1";
    $NPCPort = 5600;
    $HostIP = "steel.neweb.com.tw:443";
    //=====發MSN SKTPE用==============================================================
    $sockUrl='10.1.0.4';
    $sockPORT_Msn='17879';
    //=====聯信FTP==============================================================
    $Ftp_host='172.26.16.25';  
    $Ftp_port=21;
    $Ftp_user='ecf019';
    $Ftp_pass='wcba427';
    $Ftp_dir='';
    $FtpGlu_host='172.26.16.25';  
    $FtpGlu_port=21;
    $FtpGlu_user='trf001';
    $FtpGlu_pass='72uwhvri';
    //===================================================================
    //$City_host_id='192.168.2.121';  	//N中信
    $City_host_id='10.20.1.99';  	//N中信,2011-12-29 更新的     台固機房的授權主機
    $City_host_id_NCI='10.20.1.99';  	//台新測試的,網址IP
    //===============================================================================
    $NTIME=time();
    //$getntim=getdate($NTIME);
    // $getntim[year]. $getntim[mon]. $getntim[mday]. $getntim[hours]. $getntim[minutes]. $getntim[seconds]
    $pt = strtr($NTIME,"0123456789","AsDfGhJkLz");


    $roomtime = date("Y"."/"."m"."/"."d"." "."H".":"."i".":"."s"); //時間
    //$HOCOOKITA = $HOCOOKIT * 60 ;          			//Cookie失效時間換算
    $hohsnY = date("Y"); 					//年
    $hohsny = date("y"); 					//年
    $hohsnM = date("m"); 					//月
    $hohsnD = date("d"); 					//日
    $hohsn = date("YmdHis"); 				//日
    $hohsnYMD = date("Ymd"); 				//日
    $hohsnyMD = date("ymd"); 				//日
    $uip = $_SERVER["REMOTE_ADDR"]; 

    //===============================================================================
    $DBNAME = "newecpay";	

    define('LINE_SERVER_IP', '10.5.0.131'); // GW Line Server
    define('LINE_SERVICE_PORT', '17879'); // GW Line PORT
    
    if (!$test_mode) {
        $DBUSER = "ecpay_dbadmin";
        $DBPASS = "BTO5A2B7";
        $DBHOST = "10.20.1.65";
        $B_DBHOST = "10.20.1.65";	//同步機
    } else {
        $DBUSER = "root";
        $DBPASS = "";
        $DBHOST = "localhost";
    }


    function myerr($link){
      echo "<p><center>系統錯誤(".mysql_errno($link).")：".mysql_error($link)."<br></center>\n";  
    }

    function mylink(){
        global $DBNAME, $DBUSER, $DBPASS, $DBHOST; 
        $plink = mysql_pconnect($DBHOST, $DBUSER, $DBPASS);
     // 	$plink = mysql_connect($DBHOST, $DBUSER, $DBPASS);
        mysql_query("set names 'utf8'", $plink);	//UTF-8一定要有這行
        $result = mysql_select_db($DBNAME);
        if($result) {
                return $plink;
        } else { 
                myerr($plink);
                return 0; 
        }
    }

    function B_mylink(){
        global $DBNAME, $DBUSER, $DBPASS, $B_DBHOST; 
        $plink = mysql_pconnect($B_DBHOST, $DBUSER, $DBPASS);
     // 	$plink = mysql_connect($DBHOST, $DBUSER, $DBPASS);
        mysql_query("set names 'utf8'", $plink);	//UTF-8一定要有這行
        $result = mysql_select_db($DBNAME);
        if($result) {
                return $plink;
        } else { 
                myerr($plink);
                return 0; 
        }
    }

    function debug($msg){
      echo "<P>DEBUG:".$msg."<BR>".$endl;  
    }

    //=========================================================
    function O_Tset($DbTy) {
        $_Tu = fopen($DbTy."-set.inc","r"); 
        $fsize = filesize($DbTy."-set.inc");
        if($fsize){
        $DTy=fread($_Tu, $fsize);
        }
        fclose($_Tu);
        return($DTy);
    }

    //=========================================================
    function O_TsetPOP($DbTy) {
        $IS_dir=$_SERVER["DOCUMENT_ROOT"].'/'.$DbTy.'/000.inc';
        $_pS = file($IS_dir); 

        $DbNew = '/'.$DbTy.'/'.$_pS[0];
        $_Tu = file($_SERVER["DOCUMENT_ROOT"].'/'.$DbTy."-set.inc");  
        $coLOW  = count($_Tu);  
        $DTy=''; 
        for ($a=0 ; $a<$coLOW ; $a++) { 
            $DTy.=$_Tu[$a];  
            $DTy = str_replace('/1/pop-01/pop-01.gif',$DbNew,$DTy);
        } 
        return($DTy);
    }

    //=========================================================
    function O_Tcol($DbCol) {
        $_Ca = file($DbCol."-set.inc");
        $DCol .= $_Ca[0];
        return($DCol);
    }

    //========================================================= 過濾特殊符號
    function DelOthFn($Saol) {
        $NAME=trim("$Saol");  //去除頭尾空格
        $NAME = strtr($NAME,"\"'<>=+.,","        ");
        $NAME = str_replace(' ','',$NAME);
        return($NAME);
    }

    //==================selest
    function SelEctOP($A,$SB,$N) {
        if($A==$SB){ 
            $ECA = '<option value="'.$SB.'" selected>'.$N; 
        } else { 
            $ECA = '<option value="'.$SB.'">'.$N; 
        }
                return $ECA;
    }
    //==================checked
    function CHecKedH($B,$A,$SB) {	//欄位名,原值,比對值
        if($A==$SB){ 
            $ECA = '<input name="'.$B.'" type="radio" value="'.$SB.'" checked>'; 
        } else { 
            $ECA = '<input name="'.$B.'" type="radio" value="'.$SB.'">'; 
        }
                return $ECA;
    }
    //==================checked
    function CHecKedHBOX($B,$A,$SB) {	//欄位名,原值,比對值
        if($A==$SB){ 
            $ECA = '<input name="'.$B.'" type="checkbox" value="'.$SB.'" checked>'; 
        } else { 
            $ECA = '<input name="'.$B.'" type="checkbox" value="'.$SB.'">'; 
        }
                return $ECA;
    }
    //==================input text
    function formText($field,$size,$maxlength,$Value) {
        return '<input type="text" id="'.$field.'" name="'.$field.'" size="'.$size.'" maxlength="'.$maxlength.'" value="'.htmlspecialchars($Value).'">';
    }

    //==================checked radio
    function formRadio($Sname,$SB,$VSB) {	//欄位名,原值,比對值
        if($SB==$VSB){ 
            $ECA = '<input name="'.$Sname.'" type="radio" value="'.$SB.'" checked>'; 
        } else { 
            $ECA = '<input name="'.$Sname.'" type="radio" value="'.$SB.'">'; 
        }
                return $ECA;
    }
    function formPass($field,$size,$maxlength,$Value) {
        return '<input type="password" name="'.$field.'" size="'.$size.'" maxlength="'.$maxlength.'" value="'.htmlspecialchars($Value).'">';
    }
    //========================================================
    function QuotedPrintableEncode($sString)
    {
        for($i = 0; $i < strlen($sString); $i++)
        {
            $cCode = ord(substr($sString, $i, 1));
            
            if($cCode >= 128)
                $cNew = sprintf('=%02X', $cCode);
            else
            if(($cCode == 92) || ($cCode == 61) || (($cCode >= 33) && ($cCode <= 47)))
                $cNew = sprintf('=%02X', $cCode);
            else
                $cNew = chr($cCode);
            
            $sStr .= $cNew;
        }

            return($sStr);
    }
    //========================================================
    function dispCardno($n,$m,$o) {	//卡號,等級1-3(A-C),卡號全看1/0
        if(!$o){
    //	if(($m==3)||($o)){
    //	if(($o==0)||($m==3)){
                $l = strlen($n);
                $n = substr($n, 0, 6). @str_repeat('*', $l - 10). substr($n, -4);
        }
        return $n;
    }

    //=========================================================
    function O_SPACE($a) {		//0空值以空欄替代
        if($a){
                return $a ;
        }else{
                return '&nbsp;' ;
        }
    }
    //=========================================================
    function CARD_SPACE($a,$b) {		//a=位數,b=值,靠左不足補空白

        $c = $a - strlen($b);	//取出$b卡號位數,算出要補幾個空格
        $d='';
        for($i=0;$i<$c;$i++){
            $d .= ' ';
        }
        $e=$b.$d;
        return $e;
    }
    //=========================================================
    function CARD_ZAO($a,$b) {		//a=位數,b=值,靠左不足補0

        $c = $a - strlen($b);	//取出$b卡號位數,算出要補幾個空格
        $d='';
        for($i=0;$i<$c;$i++){
            $d .= '0';
        }
        $e=$b.$d;
        return $e;
    }
    //=========================================================取出中文部份字
    function utf8_substr($str,$start,$end) 
    { 
       preg_match_all("/./u", $str, $ar); 

       if(func_num_args() >= 3) { 
           $end = func_get_arg(2); 
           return join("",array_slice($ar[0],$start,$end)); 
       } else { 
           return join("",array_slice($ar[0],$start)); 
       } 
    } 

    //========================確認是否都是半形數字
    function Crad_isNomber($a) 
    { 
        $ppcontssa = strlen($a);
        $Card_BDvv=0;
        for($_i=0;$_i<$ppcontssa;$_i++){
            $VCAS_i=substr($a, $_i,1);
            //if(ereg("([0-9]{1})",$VCAS_i)){}else{ $Card_BDvv++; }
            if(substr_count('0123456789',$VCAS_i)){}else{ $Card_BDvv++; }
        }
         return $Card_BDvv; 
    } 
    //========================非析是何種卡片
    function Crad_isTYPE($a){ 
        $a_long = strlen($a);	//取出長度
        $a_one = substr($a,0,1);
        if($a_long==16){
            
            if($a_one==4){
                return 'VISA'; 
            }else if($a_one==5){
                return 'MAST'; 
            }else if($a_one==3){
                return 'JCB'; 
            }

        } else if($a_long==15){
        
            if($a_one==3){
                return 'AE'; 
            }else if($a_one==1){
                return 'JCB'; 
            }else if($a_one==2){
                return 'JCB'; 
            }

        }
    } 
    //=========================
    function data_crypt($k,$data,$mode='encrypt') {
        $k=substr(trim($k),0,24);
        $iv = rand(99999999,10000000);

        $cipher = mcrypt_module_open(MCRYPT_TripleDES,'','cbc','');
        mcrypt_generic_init($cipher, $k, $iv);
        if(strtoupper($mode) == 'ENCRYPT') {
            $data='12345678'.$data;
            $cbc = mcrypt_generic($cipher,$data);
        } else {
            $data = pack('H*',$data);
            $cbc = mdecrypt_generic($cipher,$data);		
        }

        return (strtoupper($mode) == 'ENCRYPT') ? bin2hex($cbc) : trim(substr($cbc,8));
    }

    //========關掉SSL的幕後POST用, 分辦成功失敗
    function my_curl_true_nossl($url,$post) {
        $ch = curl_init($url);
        $HJL_log= "===========Start CURL\n".$url."\n".$post."\nch=".$ch."\n";
        error_log($HJL_log, 3, "/tmp/HJL_date");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookiefile");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");

        $HJL_log= "===========Set OK! CURL\n";
        error_log($HJL_log, 3, "/tmp/HJL_date");

        $result = curl_exec($ch);

        if(curl_errno($ch))
        {
                $my_error= 'Curl jm error:['.curl_error($ch)."]\n";
        } else {
            $my_error= 'Curl jm ok:['. curl_error($ch)."]\n";
        }
     
        $info = curl_getinfo($ch);


        $HJL_log= "===========Run exe OK! CURL\nresult=".$result."\n------".$post."\ninfo=".$info[http_code]."\n\n";
        $HJL_log.=$my_error."\n";

        error_log($HJL_log, 3, "/tmp/HJL_date");
        curl_close ($ch);          
     
        if ($result == NULL) { 
            $result="PostError"; 
        } else {
            $result="PostOK"; 
        }
        return $result;
    }

    //將檔案加密或解密, 注意:會覆蓋過原本的檔案
	function FileEncrypt($filename, $mode='encrypt'){
		if( !file_exists($filename) ) {
			return 0;
		}
		$k = "Gtfrdc34RTfgXc34defV6yh4"; 
		$iv = "52417855";
		$filedata = file_get_contents($filename);
		$encryptdata = data_cryptNew($k, $iv, $filedata, $mode);
		return file_put_contents($filename, $encryptdata);
	}
    
	function data_cryptNew($k, $iv, $data, $mode='encrypt'){
		$cipher = mcrypt_module_open(MCRYPT_TripleDES,'','cbc','');
		mcrypt_generic_init($cipher, $k, $iv);
		if(strtoupper($mode) == 'ENCRYPT') {
			$cbc = mcrypt_generic($cipher,$data);
		} else {
			$data = pack('H*',$data);
			$cbc = mdecrypt_generic($cipher,$data);		
		}
		return (strtoupper($mode) == 'ENCRYPT') ? bin2hex($cbc) : trim($cbc, "\0");
	}


?>