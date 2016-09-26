<?php
    // Copyright(C)2000, Lin Zhemin.
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    session_start();
    
    $sess_name = "trmem";
    $sess_USER = "trmemuser";
    $FreadNB=400;
    $email='rd-group@greenworld.com.tw';
    $sockUrl_call='192.168.0.185';
    $sockPORT_call='17878';

    $sockMachineName = gethostname();
    $sockUrl_Msn='10.0.0.240';
    $sockPORT_Msn='17879';
    //身份證字號 加密
    $key_str_ch='ax350svBgow81r4L';

    // 通用 Function
    include_once('comm_func.php');
    
    // 測試模式相關設定
    define('TEST_MODE', false);
    if (TEST_MODE) {
        // AAT Server IP
        $trainPAYip = array(
            '1' => '',
            '2' => ''
        );
        
        // 網頁標題
        $webTITLE = 'Beta';
        
        // 資料庫 IP
        define('DBHOST', '192.168.150.128');
    } else {
        // AAT Server IP
        $trainPAYip = array(
            '1' => '10.10.1.33',
            '2' => '10.10.1.34'
        );
        
        // 網頁標題
        $webTITLE = '臺鐵網路信用卡付款購票系統';
        
        // 資料庫 IP
        define('DBHOST', '10.10.1.65');
    }
    
    // 資料庫定義
    define('DBNAME', 'train');
    define('DBUSER', 'train');
    define('DBPASS', 'trainps');
    define('TB_BENTO', 'bento');
    define('TB_BIN', 'any_cardno');
    define('TB_MEM', 'mem');

    define('DB_TRAIN_LOG', 'train_log');
    define('TB_NEC_TRACE', 'nec_trace_log');
    define('TB_BKEND_ACT_LOG', 'bkend_act_log');

    // 從 KEY Server 取 KEy
    $key_t1 = microtime(true);
    $new_key_url = "https://trainkey.allpay.com.tw/KeyApi/GetEncryptKey";
    $new_key_str_ch = my_curl($new_key_url, "");
    $new_key_str_ch = trim($new_key_str_ch);
    if ( number_format(microtime(true)-$key_t1, 3) >= 6 ) {
        $line_msg = "取 Key 時間過久, 花費 ".number_format(microtime(true)-$key_t1, 3)."秒, 請檢查! by form_ssl_train";
        exec_line(4, $line_msg, 'form_ssl_train');
    }
    if ( $new_key_str_ch != $key_str_ch ) {
        $line_msg = "取回Key的值為 $new_key_str_ch 有異常, 請檢查! by form_ssl_train";
        exec_line(4, $line_msg, 'form_ssl_train');
    }
    
    // 語系設定
    $Lange_Us = $_COOKIE['Lange_Us']; // user 語系
    if ($_GET['LG'] == 'en') {
        $Lange_Us = 'language_en';
    } else {
        // 語系檢查
        $aValidate_Lange = array('language_en', 'language_tw');
        if (!in_array($Lange_Us, $aValidate_Lange)) {
            $Lange_Us = 'language_tw'; // 預設為中文
        }
    }
    include_once($Lange_Us . '.php');
    
    //AAT host name
    $train_aat_desc = array(
        '1' => 'AAT-01',
        '2' => 'AAT-02'
    );

    $Rips = file("/vhost/train.sunup.net/htdocs/railway/set_urlip.inc");  $uSiP = $Rips[0];		//取出使用那一條線路
    $sockUrl=$trainPAYip[$uSiP];
    $sockPORT='18888';
    $sockPORTclose='18887';
    $BLovpa='愛心票';

    //===========================================================
    $NowISURLpay='traincc.allpay.com.tw';	//現在的授權主機 gwpay.com.tw / ecpay.com.tw
    $client=621863;	//刷卡代號(正式) gwpay:311631 , ecpay:621863
    $loginName='train999';	//金流登錄帳號

    $CheckURLpay="https://$NowISURLpay";	//檢查帳平不平的授權主機
    $GWECpauUrl="http://$NowISURLpay:469";	//授權主機

    $sen=1; //0測試,1接通
    if($sen==1){
        $GWPAY_url_fm="https://$NowISURLpay/form_ssl_train.php";
    } else {
        $GWPAY_url_fm='pay_echo.php';
    }

    $uorderDT = array('aa','odnb','tmstp','goid','gosn','bkid','bksn','amount','rtime','card8','guolu','rclose','rcancel','gwpaysn','necrrn','glwhere','pack2','pack3','uppp','spay','bspay');
    $uorderDTcount = count($uorderDT); 
    //===========================================================
    while(list($l, $r) = each($HTTP_GET_VARS)) { ${$l}=$r; }
    while(list($l, $r) = each($HTTP_POST_VARS)) { ${$l}=$r; }

    $payUnec=0;	//手續費
    $timeOUT =10;	//逾時分鐘

    $psChcn='0.2';		//中信分帳0.29 > 0.25 > 0.2
    
    $roturl='https://'.$_SERVER["HTTP_HOST"].'/railway/pay_echo.php';	//刷卡回應接收
    $CGI = $_SERVER['SCRIPT_NAME']; //$_SERVER["PHP_SELF"];
    $WEBURL = $_SERVER["HTTP_HOST"];
    $WEBURLhttp='https://'.$WEBURL.'/railway';
    $VHOSTWEB = $_SERVER["DOCUMENT_ROOT"].'/';  //決對路徑
    $NTIME=time();
    $roomtime = date("Y"."/"."m"."/"."d"." "."H".":"."i".":"."s"); //時間
    $HOCOOKITA = $HOCOOKIT*60;          			//Cookie失效時間換算
    $hohsnY = date("Y"); 					// 年(YYYY)
    $hohsny = date("y"); 					// 年(yy)
    $hohsnM = date("m"); 					// 月(mm)
    $hohsnD = date("d"); 					// 日(dd)
    $hohsn = date("YmdHis"); //資料時碼
    $ip = $_SERVER["REMOTE_ADDR"]; 
    $F2MAIL='rd-group@greenworld.com.tw';		//管理信箱
    $CHopenTMok=84600*5;	//幾天內申請開通有效
    //===========================================================
    
    define('ORDER_BENTO_URL', 'http://bentobox.railway.gov.tw/QSR/OrderUpdate'); // 訂便當連結

    if(!defined('lf')) define('lf', "\n");
    if(!defined('crlf')) define('crlf', "\r\n");

    //============memdt
    $memCHname = array('序','姓名','帳號','密碼','等級');
    $memCH = array('aa','name','uid','ups','lyv');
    $memCHnb = count($memCH);
    
    // 相容性設定(待改善項目)
    foreach($_REQUEST as $sF => $value) {
        $_REQUEST[$sF]=inSQLch($value);
        ${$sF} = inSQLch($value);
    }
    foreach($_COOKIE as $sF => $value) {
        $_COOKIE[$sF]=inSQLch($value);
        ${$sF} = inSQLch($value);
    }
    foreach($_SESSION as $sF => $value) {
        $_SESSION[$sF]=inSQLch($value);
        ${$sF} = inSQLch($value);
    }
	
	$iAAT_Timeout = 120; // AAT Socket Timeout
	
	define('GROUP_CODE', '4'); // 1: 台鐵, 4: ALLPAY ALERT
	$oLine = new cls_line(); // Line Object
?>
