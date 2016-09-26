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
    //�����Ҧr�� �[�K
    $key_str_ch='ax350svBgow81r4L';

    // �q�� Function
    include_once('comm_func.php');
    
    // ���ռҦ������]�w
    define('TEST_MODE', false);
    if (TEST_MODE) {
        // AAT Server IP
        $trainPAYip = array(
            '1' => '',
            '2' => ''
        );
        
        // �������D
        $webTITLE = 'Beta';
        
        // ��Ʈw IP
        define('DBHOST', '192.168.150.128');
    } else {
        // AAT Server IP
        $trainPAYip = array(
            '1' => '10.10.1.33',
            '2' => '10.10.1.34'
        );
        
        // �������D
        $webTITLE = '�O�K�����H�Υd�I���ʲ��t��';
        
        // ��Ʈw IP
        define('DBHOST', '10.10.1.65');
    }
    
    // ��Ʈw�w�q
    define('DBNAME', 'train');
    define('DBUSER', 'train');
    define('DBPASS', 'trainps');
    define('TB_BENTO', 'bento');
    define('TB_BIN', 'any_cardno');
    define('TB_MEM', 'mem');

    define('DB_TRAIN_LOG', 'train_log');
    define('TB_NEC_TRACE', 'nec_trace_log');
    define('TB_BKEND_ACT_LOG', 'bkend_act_log');

    // �q KEY Server �� KEy
    $key_t1 = microtime(true);
    $new_key_url = "https://trainkey.allpay.com.tw/KeyApi/GetEncryptKey";
    $new_key_str_ch = my_curl($new_key_url, "");
    $new_key_str_ch = trim($new_key_str_ch);
    if ( number_format(microtime(true)-$key_t1, 3) >= 6 ) {
        $line_msg = "�� Key �ɶ��L�[, ��O ".number_format(microtime(true)-$key_t1, 3)."��, ���ˬd! by form_ssl_train";
        exec_line(4, $line_msg, 'form_ssl_train');
    }
    if ( $new_key_str_ch != $key_str_ch ) {
        $line_msg = "���^Key���Ȭ� $new_key_str_ch �����`, ���ˬd! by form_ssl_train";
        exec_line(4, $line_msg, 'form_ssl_train');
    }
    
    // �y�t�]�w
    $Lange_Us = $_COOKIE['Lange_Us']; // user �y�t
    if ($_GET['LG'] == 'en') {
        $Lange_Us = 'language_en';
    } else {
        // �y�t�ˬd
        $aValidate_Lange = array('language_en', 'language_tw');
        if (!in_array($Lange_Us, $aValidate_Lange)) {
            $Lange_Us = 'language_tw'; // �w�]������
        }
    }
    include_once($Lange_Us . '.php');
    
    //AAT host name
    $train_aat_desc = array(
        '1' => 'AAT-01',
        '2' => 'AAT-02'
    );

    $Rips = file("/vhost/train.sunup.net/htdocs/railway/set_urlip.inc");  $uSiP = $Rips[0];		//���X�ϥΨ��@���u��
    $sockUrl=$trainPAYip[$uSiP];
    $sockPORT='18888';
    $sockPORTclose='18887';
    $BLovpa='�R�߲�';

    //===========================================================
    $NowISURLpay='traincc.allpay.com.tw';	//�{�b�����v�D�� gwpay.com.tw / ecpay.com.tw
    $client=621863;	//��d�N��(����) gwpay:311631 , ecpay:621863
    $loginName='train999';	//���y�n���b��

    $CheckURLpay="https://$NowISURLpay";	//�ˬd�b�����������v�D��
    $GWECpauUrl="http://$NowISURLpay:469";	//���v�D��

    $sen=1; //0����,1���q
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

    $payUnec=0;	//����O
    $timeOUT =10;	//�O�ɤ���

    $psChcn='0.2';		//���H���b0.29 > 0.25 > 0.2
    
    $roturl='https://'.$_SERVER["HTTP_HOST"].'/railway/pay_echo.php';	//��d�^������
    $CGI = $_SERVER['SCRIPT_NAME']; //$_SERVER["PHP_SELF"];
    $WEBURL = $_SERVER["HTTP_HOST"];
    $WEBURLhttp='https://'.$WEBURL.'/railway';
    $VHOSTWEB = $_SERVER["DOCUMENT_ROOT"].'/';  //�M����|
    $NTIME=time();
    $roomtime = date("Y"."/"."m"."/"."d"." "."H".":"."i".":"."s"); //�ɶ�
    $HOCOOKITA = $HOCOOKIT*60;          			//Cookie���Įɶ�����
    $hohsnY = date("Y"); 					// �~(YYYY)
    $hohsny = date("y"); 					// �~(yy)
    $hohsnM = date("m"); 					// ��(mm)
    $hohsnD = date("d"); 					// ��(dd)
    $hohsn = date("YmdHis"); //��ƮɽX
    $ip = $_SERVER["REMOTE_ADDR"]; 
    $F2MAIL='rd-group@greenworld.com.tw';		//�޲z�H�c
    $CHopenTMok=84600*5;	//�X�Ѥ��ӽж}�q����
    //===========================================================
    
    define('ORDER_BENTO_URL', 'http://bentobox.railway.gov.tw/QSR/OrderUpdate'); // �q�K���s��

    if(!defined('lf')) define('lf', "\n");
    if(!defined('crlf')) define('crlf', "\r\n");

    //============memdt
    $memCHname = array('��','�m�W','�b��','�K�X','����');
    $memCH = array('aa','name','uid','ups','lyv');
    $memCHnb = count($memCH);
    
    // �ۮe�ʳ]�w(�ݧﵽ����)
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
	
	define('GROUP_CODE', '4'); // 1: �x�K, 4: ALLPAY ALERT
	$oLine = new cls_line(); // Line Object
?>