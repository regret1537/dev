<?php
    // Function �w�q

    // Server POST
    function my_curl($url,$post) {
        if(!$post)  {
            $post='';
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($ch, CURLOPT_TIMEOUT,  15);
        $result = curl_exec($ch);
        curl_close ($ch);
        return $result;
    }

    // �oLine(���ϥ�)
    function exec_line($linegroup, $msg, $source) {
        // $msg = base64_encode( $msg );
        // $execstr = "php -q /manage/cronjob/ctcb/exec_line.php ".$linegroup." ".$msg." ".$source." &";
        // return 0;
    }

    // �O��LOG
    function write_log($file_name, $title, $data_ary = array()) {
        $t_file_name = $file_name . '-' . date('ymd') . '.txt';
        $log_dir_path = '/var/log/write_log/' . date('Ym') . '/';
        if(!is_dir($log_dir_path)){
            mkdir($log_dir_path,0777);
        }												
		$log_file_path = $log_dir_path . $t_file_name;
        $log_cont = '------------------------------------' . "\n";
        $log_cont .= 'Time :' . date('H:i:s') . "\n";
        $log_cont .= 'Title :' . $title . "\n";
        if (!empty($data_ary)) {
            foreach ($data_ary as $name => $value) {
                $log_cont .= $name . ' :' . $value . "\n";
            }
        }
        file_put_contents($log_file_path, $log_cont, FILE_APPEND);
	}

    // MySQL �s�u�P��� DB
    function mylink($type = 'trade', $conn_type = 'P') {
        $my_link = null;
        switch ($conn_type) {
            case 'P':
                $my_link = mysql_pconnect(DBHOST, DBUSER, DBPASS);
                break;
            case 'NP':
                $my_link = mysql_connect(DBHOST, DBUSER, DBPASS);
        }
        $db_list = array(
            'trade' => DBNAME,
            'trace_log' => DB_TRAIN_LOG,
        );
        $result = mysql_select_db($db_list[$type], $my_link);
        if($result) {
            return $my_link;
        } else {
            return 0; 
        }
    }

    // MySQL �d��
    function myquery($sSQL, $rLink) {
        $rQuery = mysql_query($sSQL, $rLink);
        return $rQuery;
    }
    
    // �����u��Ů�
    function spas_nab($a,$b){
        if($a==''){ 
            for($i=0;$i<$b;$i++){
                $a .= ' ';
            }
        } else {
            $aa = "%0".$b."s";
            $a = sprintf($aa, $a); 
        }
        return($a);
    }

    // 0�ŭȥH������N
    function O_SPACE($a) {
        if($a){
            return SanitizHTML($a);
        }else{
            return '&nbsp;' ;
        }
    }

    // �N \ ' " ���N���ť�
    function inSQLch($a){
        $a = preg_replace("/\\\'\"/",'',$a);

        // $a = preg_replace("/[\\\(\)\+\'\>\<\"]/",'',$a);
        // $a = str_replace('%27','',$a);
        // $a = str_replace('0xbf27','',$a);
        // $a = str_replace('\x1a','',$a);
        // $a = str_replace('\x00','',$a);
        // $a = str_replace("\n",'',$a);
        // $a = str_replace("\r",'',$a);
        // $a = str_replace("CHAR(39)",'',$a);
        // $a = str_replace("char(39)",'',$a);
        // $a = str_replace("\t",'',$a);
        // $a = str_replace("\Z",'',$a);
        return $a;
    }

    // �P�_�O�_�������s����
    function IsAliShan($train_no) {
        if (preg_match('/A[1,2,3,6]{1}/', $train_no)) {
            return true;
        } else {
            return false;
        }
    }

    // ���o�����s����
    function GetAliShanTrainNo($train_no) {
        // �����s�����q��榡��[A1 ]
        $no_key = substr($train_no, 0, 2);
        $alishan_train_no = array(
            'A1' => '1',
            'A2' => '2',
            'A3' => '3',
            'A6' => '6',
        );
        return $alishan_train_no[$no_key];
    }

    // ��ܲ��ƤU�Ԧ����
    function DispTicketNumOption($name, $class, $limit_num) {
        echo '<select name="' . SanitizHTML($name) . '" class="' . SanitizHTML($class) . '">';
        echo '<option value="">0</option>';
        for ($i = 1 ; $i <= $limit_num ; $i++) {
            echo '<option value="' . $i . '">' . $i . '</option>';
        }
        echo '</select>';
    }

    // ����`��
    function DispTotalNum($name, $total, $unit) {
        echo SanitizHTML($name) . '<span style="font-weight:bold; color:#0F6C65;">' . SanitizHTML($total) . '</span> ' . SanitizHTML($unit);
    }

    // ��ܲ��ȶ���
    function DispTicketItem($name, $cont) {
        echo SanitizHTML($name) . ': ' . SanitizHTML($cont);
    }

    // �L�o HTML ���e
    function SanitizHTML($html) {
        $enc = mb_detect_encoding($html);
        if (!$enc) {
            $enc = 'BIG5';
        }

        return htmlentities($html, ENT_COMPAT | ENT_HTML401, $enc, true);
    }

    // �O�� DB LOG
    function DBLog($log_table, $log_subject, $log_value) {
        // �L�o MySQL �S��r��
        $link = mylink('trace_log', 'NP');
        $sql_val = array();
        foreach ($log_value as $name => $value) {
            $sql_val[$name] = SenitizMySQL($value, $link);
        }
        
        switch ($log_table) {
            case 'nec_trace_log':
                $ins_sql = 'INSERT INTO ' . TB_NEC_TRACE . ' ';
                $ins_sql .= '(`subject`, `insert_time`, `send_nec`, `bk_nec`, `file_name`, `msg`) ';
                $ins_sql .= 'VALUES("' . $log_subject. '", "' . date('YmdHis') . '", "' . $sql_val['send_nec'] . '", "' . $sql_val['bk_nec'] . '", "' . $sql_val['file_name'] . '", "' . $sql_val['msg'] . '")';
                @mysql_query($ins_sql, $link);
                break;
            case 'bkend_act_log':
                $ins_sql = 'INSERT INTO ' . TB_BKEND_ACT_LOG . ' ';
                $ins_sql .= '(`function`, `action`, `user`, `client_ip`, `act_date`, `remark`) ';
                $ins_sql .= 'VALUES("' . $log_subject. '", "' . $sql_val['action'] . '", "' . $sql_val['user'] . '", "' . $sql_val['client_ip'] . '", "' . date('Y-m-d H:i:s') . '", "' . $sql_val['remark'] . '")';
                @mysql_query($ins_sql, $link);
                break;
            default:
        }
            
        @mysql_close($link);
    }
    
    // �L�o MySQL �S��r��
    function SenitizMySQL($sql, $my_link = null) {
        return mysql_real_escape_string($sql, $my_link);
    }
    
    // �զX AAT NEC �q��
    function CreateSendNEC($data) {
        $t_SendNEC = implode('*', $data) . '#';
        return $t_SendNEC;
    }

    // ���� HTML + �Ů��Y��
    function genHtml($cont, $sp_num) {
        return str_repeat(' ', $sp_num) . $cont . "\n";
    }
    
    // ��� HTML
    function dispHtml($cont, $sp_num) {
        echo genHtml($cont, $sp_num);
    }
    
    // ��� Table
    function disp_table ($title, $col_name, $data) {
        dispHtml('<table style="border-collapse: collapse;">', 2);
        dispHtml('<tr>', 4);
        dispHtml('<td colspan="' . count($col_name) . '" style="border: 1px solid #000;text-align: center;">' . SanitizHTML($title) . '</td>', 6);
        dispHtml('</tr>', 4);
        dispHtml('<tr>', 4);
        foreach ($col_name as $t_info) {
            dispHtml('<td style="width: ' . SanitizHTML($t_info['width']) . 'px;border: 1px solid #000;text-align: center;">' . SanitizHTML($t_info['desc']) . '</td>', 6);
        }
        dispHtml('</tr>', 4);
        foreach ($data as $t_row) {
            dispHtml('<tr>', 4);
            foreach ($t_row as $t_val) {
                dispHtml('<td style="border: 1px solid #000;text-align: center;">' . SanitizHTML($t_val) . '</td>', 6);
            }
            dispHtml('</tr>', 4);
        }
        dispHtml('</table>', 2);
        dispHtml('<br />', 2);
    }
    
    // ���o003A�q�媺�K���`���B
    function gen_003A_bento_total($a002B) {
        $iBento_Total = $a002B[77] + $a002B[79] + $a002B[81] + $a002B[83];
        return $iBento_Total;
    }
    
    // �ˬd�O�_���ҥ~ IP
    function is_allow_ip() {
        $white_ip_list = array(
            '211.23.128.211',// allPay-OA
            '211.23.76.78',// GW-OA
        );
        $uip = $_SERVER["REMOTE_ADDR"];
        
        if (in_array($uip, $white_ip_list)) {
            return true;
        } else {
            return false;
        }
    }
    
    // �Ѹ�Ʈw���o�n�J�ϥΪ�
    function get_login_user($aa) {
        $type = 'trade';
        $conn_type = 'NP';
        $my_link = mylink($type, $conn_type);
        $sel_sql = 'SELECT uid FROM ' . TB_MEM;
        $sel_sql .= ' WHERE aa = "' . SenitizMySQL($aa, $my_link) . '"';
        $sel_qry = myquery($sel_sql, $my_link);
        $sel_ary = mysql_fetch_array($sel_qry);
        mysql_free_result($sel_qry);
        $t_user = $sel_ary['uid'];
        mysql_close($my_link);
        
        return $t_user;
    }

    // �ˬd�O�_�������Ҧr��
    function is_id($sID) {
        // �r������
        $aHead_Point = array(
            'A' => 10, 'B' => 11,'C' => 12, 'D' => 13, 'E' => 14, 'F' => 15, 'G' => 16, 'H' => 17, 'I' => 34,
            'J' => 18, 'K' => 19, 'L' => 20, 'M' => 21, 'N' => 22, 'O' => 35,
            'P' => 23, 'Q' => 24, 'R' => 25, 'S' => 26, 'T' => 27, 'U' => 28, 'V' => 29, 'W' => 32,
            'X' => 30, 'Y' => 31, 'Z' => 33
        );
        
        // �[�v���
        $aMultiply = array(8, 7, 6, 5, 4, 3, 2, 1);
        
        // ���׻P�򥻮榡�ˬd
        $sUpper_ID = strtoupper($sID);
        if (!preg_match('/^[A-Z]{1}[1-2]{1}[0-9]{8}/', $sUpper_ID)) {
            return false;
        } else {
            // ���Φr��
            $aChar_List = str_split($sUpper_ID);
            
            // ���o�r������
            $iOrg_Eng_Point = $aHead_Point[array_shift($aChar_List)];
            $iEng_Point = (($iOrg_Eng_Point % 10) * 9 ) + floor($iOrg_Eng_Point / 10);
            
            // ���o���X
            $sOrg_Chk_Code = array_pop($aChar_List);
            
            // ���o�Ʀr����
            $iInt_Point = 0;
            $iTmp_Len = count($aChar_List);
            for ($iIdx = 0 ; $iIdx < $iTmp_Len ; $iIdx++) {
                $iInt_Point += $aChar_List[$iIdx] * $aMultiply[$iIdx];
            }
            
            // ���o�`����
            $iTotal_Point = $iEng_Point + $iInt_Point;
            
            // �p����X
            $iTotal_Mod_Point = $iTotal_Point % 10;
            $iChk_Code = ($iTotal_Mod_Point == 0) ? 0 : (10 - $iTotal_Mod_Point);
            
            // ����l�P�p��X�����X
            if ($iChk_Code != $sOrg_Chk_Code) {
                return false;
            } else {
                return true;
            }
        }
    }
    
    // ��������
    function rm_hashtag($sNEC_Stat) {
        $sNEC_Stat_No_Tag = str_replace('#', '', $sNEC_Stat);
        return $sNEC_Stat_No_Tag;
    }
    
    // �ѪR AAT NEC �q��
    function parse_nec_stat($sNEC_Stat) {
        $aValue = explode('*', rm_hashtag($sNEC_Stat));
        return $aValue;
    }
    
    // �����Ҧr�����X
    function mask_id($sID) {
        $sMasked_ID = substr($sID, 0, 3) . 'XXXX' . substr($sID, -3);
        return $sMasked_ID;
    }
    
    // �N AAT NEC �q�夤�����������X
    function mask_nec_stat($sNEC_Stat) {
        $aMasked_Value = array();
        $sTmp_Masked_Value = '';
        
        // �ѪRAAT NEC�q��
        $aValue = parse_nec_stat($sNEC_Stat);
        
        foreach ($aValue as $sTmp_Value) {
            if (is_id($sTmp_Value)) {
                // �����Ҧr�����X
                $sTmp_Masked_Value = mask_id($sTmp_Value);
            } else {
                $sTmp_Masked_Value = $sTmp_Value;
            }
            array_push($aMasked_Value, $sTmp_Masked_Value);
        }
        
        // �զX AAT NEC �q��
        $sMasked_Stat = CreateSendNEC($aMasked_Value);
        return $sMasked_Stat;
    }
    
    // Line �\��
    include_once('cls_line.php');
?>