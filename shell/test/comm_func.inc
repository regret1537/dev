<?php
    // 常用功能
    function MB_CARD_SPACE($a, $b) {  //UTF8 中文字用 a=位數,b=值,靠左不足補空白
        // A23 B歐付寶－歐付寶歐付寶 C16.333333333333
        // MB_CARD_SPACE(23, substr($value2['bill_name'], 0, 10*3))
        #16.333 = 23 - (x/3)*2
        // 39.333 = (x/3)*2
        // 20 = (x/3)
        // x = 60
        //$c = $a - (mb_strlen($b) / 3 ) * 2;   //取出$b卡號位數,算出要補幾個空格
        // A23 B歐付寶－歐付寶歐付寶 C-7 strlen30
        $c = $a - (strlen($b) / 3 ) * 2;   //取出$b卡號位數,算出要補幾個空格
        
        $d = '';
        for ($i = 0; $i < $c; $i++) {
            $d .= ' ';
        }
        $e = $b . $d;
        return $e;
    }
  
    //  產生 HEADER	
    function gen_header($data = null) {
        if (empty($data)) {
            $data = array(
                'trd_date' => '071113',
                'mid' => '0130004079',
                'pay_count' => '1',
                'pay_amount' => '1',
                'pay_back_count' => '0',
                'pay_back_amount' => '0',
                'bill_name' => 'GWECPAY',
            );
        }

		// 取得交易說明
        $t_bill_name = substr($data['bill_name'], 0, 40);// 交易說明(全23/半25)(半形)
		
        // 只以英文半形處理
        $t_header = '8';                                                            // 記錄類型(頭或明細)(1), 固定帶 8
        $t_header .= $data['trd_date'];                                             // 交易日期(6)
        $t_header .= $data['mid'];                                                  // 特店代碼之主碼(9) + 特店代碼之檢碼(1)
        $t_header .= str_pad($data['pay_count'], 8, '0', STR_PAD_LEFT);             // 本批之銷售筆數(8)
        $t_header .= str_pad($data['pay_amount'], 10, '0', STR_PAD_LEFT);           // 本批之銷售總金額(10)
        $t_header .= str_pad($data['pay_back_count'], 8, '0', STR_PAD_LEFT);        // 本批之退貨筆數(8)
        $t_header .= str_pad($data['pay_back_amount'], 10, '0', STR_PAD_LEFT);      // 本批之退貨總金額(10)
        $t_header .= ' ';                                                           // 平帳記號(1), 固定帶空白
        // $t_header .= MB_CARD_SPACE(38, substr($data['bill_name'], 0, 10*3)) . '  '; // 交易敘述(全 38+2bits空白/半 40)(全形)
        $t_header .= str_pad($t_bill_name, 40, ' ', STR_PAD_RIGHT) ;                // 交易敘述(40)(全 38+2bits空白/半 40)(半形)
        $t_header .= ' ';                                                           // 平帳記號(1), 固定帶空白
        $t_header .= ' ';                                                           // 平帳記號(1), 固定帶空白
        $t_header .= '    ';                                                        // 保留欄位(4), 固定帶空白
        $t_header .= str_repeat(' ', 60);                                           // 保留欄位(60), 固定帶空白
        $t_header .= TXT_MESSAGE_NEW_LINE ;
        
        return $t_header;
    }
  
    // 產生 DETAIL
    function gen_detail($data = null) {
        if (empty($data)) {
            $data = array(
                'procdt' => '071113',
                'cdno' => '4311951002422222',
                'neg' => '41',
                'auth' => '777777',
                'amount' => '1',
                'bill_name' => 'GWECPAY',
                'eci' => '5',
                'tid' => '',
                'sub_mid' => ''
            );
        }
        
        // 取得 UCAF
        $cCredit = new cls_credit($data['cdno'], $data['eci']);
        $t_ucaf = $cCredit->get_ucaf();
        
        // 取得交易說明
        $t_bill_name = substr($data['bill_name'], 0, 25);// 交易說明(全23/半25)(半形)
        
        // 只以英文半形處理
        $t_detail = '1';                                                     // 記錄類型(頭或明細)(1)
        $t_detail .= $data['procdt'] ;                                        // 交易日期(6)
        $t_detail .= str_pad($data['cdno'], '16' , STR_PAD_LEFT) ;            // 卡號之主號(16)
        $t_detail .= '000' ;                                                  // 卡號之擴充號(3)
        $t_detail .= $data['neg'] ;                                           // 交易代碼(2)
        $t_detail .= str_pad($data['auth'], 6, '0', STR_PAD_LEFT) ;           // 授權碼(6)
        $t_detail .= str_pad($data['amount'], 10, '0', STR_PAD_LEFT) ;        // 交易金額(10)
        // $t_detail .= MB_CARD_SPACE(23, substr($data['bill_name'], 0, 10*3)) ; // 交易說明(全23/半25)(全形)
        $t_detail .= str_pad($t_bill_name, 25, ' ', STR_PAD_RIGHT) ;          // 交易說明(全23/半25)(半形)
        $t_detail .= $data['eci'] ;                                           // EC交易之安控方式(1)
        $t_detail .= ' ';                                                     // 處理回應碼(1)
        $t_detail .= str_pad($data['tid'], 8, ' ', STR_PAD_LEFT);             // 終端機代碼(8)
        $t_detail .= str_repeat(' ', 20);                                     // 保留欄位(20)
        $t_detail .= str_pad($t_ucaf, 1, ' ', STR_PAD_LEFT);                  // 萬事達卡UCAF(1), 未提供此服務，固定帶 0
        $t_detail .= str_pad($data['sub_mid'], 15, ' ', STR_PAD_RIGHT);       // 會員商店/次特店 ID(15)
        $t_detail .= str_repeat(' ', 40);                                     // 販售交易商品品項(全38/半40)
        $t_detail .= str_repeat(' ', 5);                                      // 保留欄位(5)
        $t_detail .= TXT_MESSAGE_NEW_LINE ;
        
        return $t_detail;
    }

    // 產生 TRAILER
    function gen_trailer($row_count = null) {
        if (empty($row_count)) {
            $row_count = '3';
        }
        // 最後要再加上一行 結尾
        $t_trailer = '9';                                         // 記錄類型(頭或明細)(1)
        $t_trailer .= date('ymd');                                 // 處理日期(同HEADER)(6)
        $t_trailer .= str_pad($row_count, 8, '0', STR_PAD_LEFT) ;  // 筆數(8)
        $t_trailer .=  str_repeat(' ', 145);                       // 保留欄位(145)
        
        return $t_trailer;
    }
    
    // 解析中信請款資料
    function parse_ctbc_row($row) {
        $t_row = trim($row);
        $parsed_data = array();
        
        // 記錄類型
        $t_record_type = substr($t_row, 0, 1);
        
        // 截取長度
        $cut_len = array(
            // DETAIL
            '1' => array(
                'rec_type' => 1,
                'proc_date' => 6,
                'card_no' => 16,
                'card_no_ext' => 3,
                'trans_code' => 2,
                'auth_code' => 6,
                'trans_amount' => 10,
                'trans_desc' => 25,
                'security_indicator' => 1,
                'proc_response_code' => 1,
                'term_id' => 8,
                'resv_field' => 23
            ),
            // HEADER
            '8' => array(
                'rec_type' => 1,
                'proc_date' => 6,
                'merchant_no' => 9,
                'merchant_no_chk' => 1,
                'sale_trans_count' => 8,
                'sale_trans_total' => 10,
                'refund_trans_count' => 8,
                'refund_trans_total' => 10,
                'balance_code' => 1,
                'trans_desc' => 40,
                'sale_balance_code' => 1,
                'refund_balance_code' => 1,
                'resv_field' => 4
            ),
            // TRAILER
            '9' => array(
                'rec_type' => 1,
                'proc_date' => 6,
                'record_count' => 8,
                'resv_field' => 85
            ),
        );
        $t_cut_len = $cut_len[$t_record_type];
        if (count($t_cut_len) > 0) {
            $s_idx = 0;
            foreach ($t_cut_len as $t_name => $t_len) {
                $t_piece = substr($row, $s_idx , $t_len);
                if ($t_name != 'trans_desc') {
                    $t_val = trim($t_piece);
                } else {
                    $t_val = $t_piece;
                }
                $parsed_data[$t_name] = $t_val;
                $s_idx += $t_len;
            }
        }
        return $parsed_data;
    }

    // 取得狀態圖示路徑
    function get_status_img($status) {
        $status_img_url = '';
        $img_url = 'https://cdn3.iconfinder.com/data/icons/softwaredemo/PNG/64x64';
        if ($status) {
            $status_img_url = $img_url. '/Circle_Green.png';
        } else {
            $status_img_url = $img_url . '/Circle_Red.png';
        }
        return $status_img_url;
    }
    
    // 產生 HTML 格式 MAIL HEADER
    function get_html_mail_header($charset = 'UTF8', $mail_from = '') {
        $t_header = 'MIME-Version: 1.0' . TXT_MESSAGE_NEW_LINE;
        $t_header .= 'Content-Type: text/html; Charset=' . $charset . TXT_MESSAGE_NEW_LINE;
        if (!empty($mail_from)) {
            $t_header .= 'From: ' . $mail_from . TXT_MESSAGE_NEW_LINE;
        }
        return $t_header;
    }
    
    // 產生通知 MAIL 內容
    function gen_notice_mail($result, $status, $cont_desc, $cont_title, $cont_data) {
        // 取得狀態圖示路徑
        $status_img_url = get_status_img($status);
        
        // 執行結果
        $t_cont = '';        
        $t_cont .= '        <table width="90%" style="border:3px #FFAC55 solid; padding:5px; font-family:Microsoft JhengHei;" rules="all" cellpadding="5">' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '            <tr>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                <td width="90%">' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                    <center>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                    <h2>執行結果</h2>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                    </center>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                    ' . $result . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                <td>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                    <center><img src="' . $status_img_url . '"></center>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                </td>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '            </tr>' . TXT_MESSAGE_NEW_LINE;
        
        // 內容說明
        if (!empty($cont_desc)) {
            $t_cont .= '            <tr>' . TXT_MESSAGE_NEW_LINE;
            $t_cont .= '                <td colspan="2">' . TXT_MESSAGE_NEW_LINE;
            $t_cont .= '                    <hr style="color:#FFAC55;">' . TXT_MESSAGE_NEW_LINE;
            $t_cont .= '                    ' . $cont_desc . TXT_MESSAGE_NEW_LINE;
            $t_cont .= '                </td>' . TXT_MESSAGE_NEW_LINE;
            $t_cont .= '            </tr>' . TXT_MESSAGE_NEW_LINE;
        }
        $t_cont .= '            <tr>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                <td colspan="2">' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                    <table width="100%" rules="all" cellpadding="5" style="border-collapse:collapse;">' . TXT_MESSAGE_NEW_LINE;
        
        // 內容標題
        $t_cont .= '                        <tr>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                            <th style="border:1px #FFAC55 solid; padding:5px; font-family:Microsoft JhengHei;">編號</th>' . TXT_MESSAGE_NEW_LINE;
        foreach ($cont_title as $t_title) {
            $t_cont .= '                            <th style="border:1px #FFAC55 solid; padding:5px; font-family:Microsoft JhengHei;">' . $t_title . '</th>' . TXT_MESSAGE_NEW_LINE;
        }
        $t_cont .= '                        </tr>' . TXT_MESSAGE_NEW_LINE;
        
        // 內容
        if (!empty($cont_data)) {
            foreach ($cont_data as $t_idx => $t_data) {
                $t_sn = $t_idx + 1;
                $t_cont .= '                        <tr>' . TXT_MESSAGE_NEW_LINE;
                $t_cont .= '                            <td style="border:1px #FFAC55 solid; padding:5px; font-family:Microsoft JhengHei;">' . TXT_MESSAGE_NEW_LINE;
                $t_cont .= '                                <center>' . $t_sn . '</center>' . TXT_MESSAGE_NEW_LINE;
                $t_cont .= '                            </td>' . TXT_MESSAGE_NEW_LINE;
                foreach ($t_data as $t_value) {
                    $t_cont .= '                            <td style="border:1px #FFAC55 solid; padding:5px; font-family:Microsoft JhengHei;">' . TXT_MESSAGE_NEW_LINE;
                    $t_cont .= '                                <center>' . $t_value . '</center>' . TXT_MESSAGE_NEW_LINE;
                    $t_cont .= '                            </td>' . TXT_MESSAGE_NEW_LINE;
                }
                $t_cont .= '                        </tr>' . TXT_MESSAGE_NEW_LINE;
            }
        }
        $t_cont .= '                    </table>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '                </td>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '            </tr>' . TXT_MESSAGE_NEW_LINE;
        $t_cont .= '        </table>' . TXT_MESSAGE_NEW_LINE;
        return $t_cont;
    }
    
    // 發 Line 訊息(傳入的訊息)
    function exec_line($sLine_Grp_Code, $sLine_Msg, $sSrc_File_Name) {
        $sLine_Log_TB = 'exec_line';// Line Log Table
        $sLog_Subject = 'Line群警示訊息';
        $sLine_Grp = '';
        $mStart_Microtime = microtime(true);
        $dStart_Time = date('YmdHis');
        
        // Line群組 1:GreenWorld TECH, 2:ALLPAY TECH, 其他:ALLPAY ALERT
        switch($sLine_Grp_Code){
            case '1':
                $sLine_Grp = 'GreenWorld TECH';
                break;
            case '2':
                $sLine_Grp = 'ALLPAY TECH';
                break;
            default:
                $sLine_Grp = 'ALLPAY ALERT';
                break;
        }
        
        if($sLine_Grp != '' and $sLine_Msg != '' and $sSrc_File_Name != '') {
            // Line Server 資訊, 定義於 g_00system.inc
            $sLine_Server_IP = LINE_SERVER_IP; // Line Server IP
            $sLine_Server_Port = LINE_SERVICE_PORT; // Line Server Port
            
            $sLine_Server_Info = $sLine_Server_IP . ':' . $sLine_Server_Port;// Line Server 資訊            
            $iLine_Server_Timeout = 20; // Line Server 等待時間
            $iWrite_Len_Limit = 300; // 傳送長度限制
            $iRead_Len_Limit = 100; // 接收長度限制
            $sHostname = gethostname(); // 本機 Server 名稱
            
            // 發送 Line 訊息
            $sSendStat = '[MSN=*LINE*' . $sLine_Grp . ', ]' . $sHostname . ', ' . $sLine_Msg . ' ('.date('Y-m-d').') ' . "\r\n";
            $rFile_Pointer = fsockopen($sLine_Server_IP, $sLine_Server_Port, $iErr_NO, $sErr_Str, $iLine_Server_Timeout);
            if($rFile_Pointer){
                $iWrite_Res = @fwrite($rFile_Pointer, mb_convert_encoding($sSendStat, 'Big5', 'UTF-8'), $iWrite_Len_Limit);
                $sRead_Res = @fread($rFile_Pointer, $iRead_Len_Limit);
                fclose($rFile_Pointer);
            } else {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    
    // 產生 LINE 訊息
    function gen_line_msg($msg, $from_php) {
        $t_msg = $msg . ' by ' . $from_php;
        return $t_msg;
    }
    
    include_once('cls_credit.php');
?>