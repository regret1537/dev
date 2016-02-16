<?php
    $app_set = array
    (
        'title' => 'API串接測試',
        'body' => 'admin_qa',
    );
    require_once('../include/runtime.inc.php');
    require_once(SYS_ROOT . './_admin/include/page_head.inc.php');
    require_once(SYS_ROOT . './check_value_engine.php');
    
    $error = '';
    $mer_id = '9493';
    $amount = 100;
    $order_no = date('ymdHis');
    $enc_key = 'Htg3v48wB5L94RixlVZCeMEO';
    $check_mac_value = '';
    $ecbank_web_root = 'https://' . ECBANK_IP . '/';
    $ignore_params = array('check_mac');
    
    # test
    function t_disp($cont) {
        set_html($cont . '<br />', 4);
    }
    
    # 送出參數處理
    if (isset($_POST['t_submit'])) {
        $send_url = '';
        $rev_params = $_POST;
        
        # 測試UI用參數
        unset($rev_params['check_mac']);
        
        if (isset($_POST['check_mac']) and $_POST['check_mac'] == 'on') {
            $send_url = 'https://' . ECBANK_IP . '/gateway_v2.php';
            
            if (isset($_POST['mer_id']) and !empty($_POST['mer_id'])) {
                # 取得 HashKey, HashIV
                $mer_id = $_POST['mer_id'];
                $sql = 'SELECT hashkey, hashiv FROM tblMerchants_Key WHERE mer_id = ' . $mer_id . ' LIMIT 1;';
                $sql_result = $db->query($sql);
                $row = $db->fetch_array($sql_result);
                $hash_key = $row['hashkey'];
                $hash_iv = $row['hashiv'];
                $check_mac_value = GenerateCheckMacValue($rev_params, $hash_key, $hash_iv);
            } else {
                $error = 'ECBank商店代號 不可為空';
            }
        } else {
            $send_url = 'https://' . ECBANK_IP . '/gateway.php';
        }
        
        # 轉導至 gateway.php , gateway_v2.php
        if (empty($error)) {
            set_html('<form id="ecbank_form" action="' . $send_url . '" method="POST">', 4);
            foreach($rev_params as $param_name => $param_val) {
                if (gettype($param_val) == 'array') {
                    foreach ($param_val as $p_idx => $p_val) {
                        set_html('<input type="hidden" name="' . $param_name . '[]" value="' . $p_val . '">', 6);
                    }
                } else {
                    set_html('<input type="hidden" name="' . $param_name . '" value="' . $param_val . '">', 6);
                }
            }
            if (!empty($check_mac_value)) {
                set_html('<input type="hidden" name="checkmacvalue" value="' . $check_mac_value . '">', 6);
            }
            set_html('<input type="submit" name="t_submit" value="送出">', 6);
            // set_html('<script type="text/javascript">document.getElementById("ecbank_form").submit();</script>', 6);
            set_html('</form>', 4);
            exit;
        }
    }
    
    $options = array(
        array(
            'title' => 'PayPal 交易表單輸入參數',
            'form' => array('name' => 't_form','act' => 'qa_index.php',),
            'input' =>
                array(
                    array('width' => 300, 'txt' => '檢查碼', 'name' => 'check_mac', 'type' => 'checkbox', 'id' => 'check_mac', 'value' => ''),
                    array('width' => 300, 'txt' => 'ECBank商店代號', 'name' => 'mer_id', 'type' => 'text', 'id' => 'mer_id', 'size' => 80, 'value' => $mer_id),
                    array('width' => 300, 'txt' => '付款方式', 'name' => 'payment_type', 'type' => 'text', 'id' => 'payment_type', 'size' => 80, 'value' => 'paypal'),
                    array('width' => 300, 'txt' => '賣家自訂交易編號', 'name' => 'od_sob', 'type' => 'text', 'id' => 'od_sob', 'size' => 80, 'value' => 'pp' . $order_no),
                    array('width' => 300, 'txt' => '交易項目名稱', 'name' => 'item_name', 'type' => 'text', 'id' => 'item_name', 'size' => 80, 'value' => '測試項目'),
                    array('width' => 300, 'txt' => '交易項目說明', 'name' => 'item_desc', 'type' => 'text', 'id' => 'item_desc', 'size' => 80, 'value' => '測試說明'),
                    array('width' => 300, 'txt' => '貨幣類別', 'name' => 'cur_type', 'type' => 'text', 'id' => 'cur_type', 'size' => 80, 'value' => 'TWD'),
                    array('width' => 300, 'txt' => '交易金額', 'name' => 'amt', 'type' => 'text', 'id' => 'amt', 'size' => 80, 'value' => $amount),
                    array('width' => 300, 'txt' => '從 PayPal 取消交易返回網址', 'name' => 'cancel_url', 'type' => 'text', 'id' => 'cancel_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '交易結果返回網址', 'name' => 'return_url', 'type' => 'text', 'id' => 'return_url', 'size' => 80, 'value' => $ecbank_web_root),
                ),
            'submit' => array('name' => 't_submit','value' => '送出'),
        ),
        array(
            'title' => '超商繳費代碼取號輸入參數',
            'form' => array('name' => 't_form','act' => 'qa_index.php',),
            'input' =>
                array(
                    array('width' => 300, 'txt' => '檢查碼', 'name' => 'check_mac', 'type' => 'checkbox', 'id' => 'check_mac', 'value' => ''),
                    array('width' => 300, 'txt' => 'ECBank商店代號', 'name' => 'mer_id', 'type' => 'text', 'id' => 'mer_id', 'size' => 80, 'value' => $mer_id),
                    array('width' => 300, 'txt' => '付款方式', 'name' => 'payment_type', 'type' => 'text', 'id' => 'payment_type', 'size' => 80, 'value' => 'cvs'),
                    array('width' => 300, 'txt' => '交易加密私鑰', 'name' => 'enc_key', 'type' => 'text', 'id' => 'enc_key', 'size' => 80, 'value' => $enc_key),
                    array('width' => 300, 'txt' => '賣家自訂交易編號', 'name' => 'od_sob', 'type' => 'text', 'id' => 'od_sob', 'size' => 80, 'value' => 'cvs' . $order_no),
                    array('width' => 300, 'txt' => '交易金額', 'name' => 'amt', 'type' => 'text', 'id' => 'amt', 'size' => 80, 'value' => $amount),
                    array('width' => 300, 'txt' => '商品說明', 'name' => 'prd_desc', 'type' => 'text', 'id' => 'prd_desc', 'size' => 80, 'value' => '測試說明'),
                    array('width' => 300, 'txt' => '備註第1行', 'name' => 'desc1', 'type' => 'text', 'id' => 'desc1', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '備註第2行', 'name' => 'desc2', 'type' => 'text', 'id' => 'desc2', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '備註第3行', 'name' => 'desc3', 'type' => 'text', 'id' => 'desc3', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '備註第4行', 'name' => 'desc4', 'type' => 'text', 'id' => 'desc4', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '付款完成通知網址', 'name' => 'ok_url', 'type' => 'text', 'id' => 'ok_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '繳費有效時間', 'name' => 'expire_datetime', 'type' => 'text', 'id' => 'expire_datetime', 'size' => 80, 'value' => ''),
                ),
            'submit' => array('name' => 't_submit','value' => '送出'),
        ),
        array(
            'title' => 'WEB-ATM 串接輸入參數',
            'form' => array('name' => 't_form','act' => 'qa_index.php',),
            'input' =>
                array(
                    array('width' => 300, 'txt' => '檢查碼', 'name' => 'check_mac', 'type' => 'checkbox', 'id' => 'check_mac', 'value' => ''),
                    array('width' => 300, 'txt' => 'ECBank商店代號', 'name' => 'mer_id', 'type' => 'text', 'id' => 'mer_id', 'size' => 80, 'value' => $mer_id),
                    array('width' => 300, 'txt' => '付款方式', 'name' => 'payment_type', 'type' => 'text', 'id' => 'payment_type', 'size' => 80, 'value' => 'web_atm'),
                    array('width' => 300, 'txt' => '收單銀行', 'name' => 'setbank', 'type' => 'text', 'id' => 'setbank', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '賣家自訂交易編號', 'name' => 'od_sob', 'type' => 'text', 'id' => 'od_sob', 'size' => 80, 'value' => 'wa' . $order_no),
                    array('width' => 300, 'txt' => '交易金額', 'name' => 'amt', 'type' => 'text', 'id' => 'amt', 'size' => 80, 'value' => $amount),
                    array('width' => 300, 'txt' => '交易結果返回網址', 'name' => 'return_url', 'type' => 'text', 'id' => 'return_url', 'size' => 80, 'value' => $ecbank_web_root),
                ),
            'submit' => array('name' => 't_submit','value' => '送出'),
        ),
        array(
            'title' => '虛擬帳號取號輸入參數',
            'form' => array('name' => 't_form','act' => 'qa_index.php',),
            'input' =>
                array(
                    array('width' => 300, 'txt' => '檢查碼', 'name' => 'check_mac', 'type' => 'checkbox', 'id' => 'check_mac', 'value' => ''),
                    array('width' => 300, 'txt' => 'ECBank商店代號', 'name' => 'mer_id', 'type' => 'text', 'id' => 'mer_id', 'size' => 80, 'value' => $mer_id),
                    array('width' => 300, 'txt' => '付款方式', 'name' => 'payment_type', 'type' => 'text', 'id' => 'payment_type', 'size' => 80, 'value' => 'vacc'),
                    array('width' => 300, 'txt' => '收單銀行', 'name' => 'setbank', 'type' => 'text', 'id' => 'setbank', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '交易加密私鑰', 'name' => 'enc_key', 'type' => 'text', 'id' => 'enc_key', 'size' => 80, 'value' => $enc_key),
                    array('width' => 300, 'txt' => '賣家自訂交易編號', 'name' => 'od_sob', 'type' => 'text', 'id' => 'od_sob', 'size' => 80, 'value' => 'atm' . $order_no),
                    array('width' => 300, 'txt' => '交易金額', 'name' => 'amt', 'type' => 'text', 'id' => 'amt', 'size' => 80, 'value' => $amount),
                    array('width' => 300, 'txt' => '允許繳費有效天數', 'name' => 'expire_day', 'type' => 'text', 'id' => 'expire_day', 'size' => 80, 'value' => 7),
                    array('width' => 300, 'txt' => '付款完成通知網址', 'name' => 'ok_url', 'type' => 'text', 'id' => 'ok_url', 'size' => 80, 'value' => $ecbank_web_root),
                ),
            'submit' => array('name' => 't_submit','value' => '送出'),
        ),
        array(
            'title' => '超商條碼取號輸入參數',
            'form' => array('name' => 't_form','act' => 'qa_index.php',),
            'input' =>
                array(
                    array('width' => 300, 'txt' => '檢查碼', 'name' => 'check_mac', 'type' => 'checkbox', 'id' => 'check_mac', 'value' => ''),
                    array('width' => 300, 'txt' => 'ECBank商店代號', 'name' => 'mer_id', 'type' => 'text', 'id' => 'mer_id', 'size' => 80, 'value' => $mer_id),
                    array('width' => 300, 'txt' => '付款方式', 'name' => 'payment_type', 'type' => 'text', 'id' => 'payment_type', 'size' => 80, 'value' => 'barcode'),
                    array('width' => 300, 'txt' => '交易加密私鑰', 'name' => 'enc_key', 'type' => 'text', 'id' => 'enc_key', 'size' => 80, 'value' => $enc_key),
                    array('width' => 300, 'txt' => '賣家自訂交易編號', 'name' => 'od_sob', 'type' => 'text', 'id' => 'od_sob', 'size' => 80, 'value' => 'bc' . $order_no),
                    array('width' => 300, 'txt' => '交易金額', 'name' => 'amt', 'type' => 'text', 'id' => 'amt', 'size' => 80, 'value' => $amount),
                    array('width' => 300, 'txt' => '允許繳費有效天數', 'name' => 'expire_day', 'type' => 'text', 'id' => 'expire_day', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '商品說明', 'name' => 'item_desc', 'type' => 'text', 'id' => 'item_desc', 'size' => 80, 'value' => '測試說明'),
                    array('width' => 300, 'txt' => '付款完成通知網址', 'name' => 'ok_url', 'type' => 'text', 'id' => 'ok_url', 'size' => 80, 'value' => $ecbank_web_root),
                ),
            'submit' => array('name' => 't_submit','value' => '送出'),
        ),
        array(
            'title' => '7-Eleven ibon代碼取號輸入參數',
            'form' => array('name' => 't_form','act' => 'qa_index.php',),
            'input' =>
                array(
                    array('width' => 300, 'txt' => '檢查碼', 'name' => 'check_mac', 'type' => 'checkbox', 'id' => 'check_mac', 'value' => ''),
                    array('width' => 300, 'txt' => 'ECBank商店代號', 'name' => 'mer_id', 'type' => 'text', 'id' => 'mer_id', 'size' => 80, 'value' => $mer_id),
                    array('width' => 300, 'txt' => '付款方式', 'name' => 'payment_type', 'type' => 'text', 'id' => 'payment_type', 'size' => 80, 'value' => 'ibon'),
                    array('width' => 300, 'txt' => '交易加密私鑰', 'name' => 'enc_key', 'type' => 'text', 'id' => 'enc_key', 'size' => 80, 'value' => 'Htg3v48wB5L94RixlVZCeMEO'),
                    array('width' => 300, 'txt' => '賣家自訂交易編號', 'name' => 'od_sob', 'type' => 'text', 'id' => 'od_sob', 'size' => 80, 'value' => 'cvsib' . $order_no),
                    array('width' => 300, 'txt' => '交易金額', 'name' => 'amt', 'type' => 'text', 'id' => 'amt', 'size' => 80, 'value' => $amount),
                    array('width' => 300, 'txt' => '商品說明', 'name' => 'prd_desc', 'type' => 'text', 'id' => 'prd_desc', 'size' => 80, 'value' => '測試說明'),
                    array('width' => 300, 'txt' => '備註第1行', 'name' => 'desc1', 'type' => 'text', 'id' => 'desc1', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '備註第2行', 'name' => 'desc2', 'type' => 'text', 'id' => 'desc2', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '備註第3行', 'name' => 'desc3', 'type' => 'text', 'id' => 'desc3', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '付款完成通知網址', 'name' => 'ok_url', 'type' => 'text', 'id' => 'ok_url', 'size' => 80, 'value' => $ecbank_web_root),
                ),
            'submit' => array('name' => 't_submit','value' => '送出'),
        ),
        array(
            'title' => '支付寶串接輸入參數',
            'form' => array('name' => 't_form','act' => 'qa_index.php',),
            'input' =>
                array(
                    array('width' => 300, 'txt' => '檢查碼', 'name' => 'check_mac', 'type' => 'checkbox', 'id' => 'check_mac', 'value' => ''),
                    array('width' => 300, 'txt' => 'ECBank商店代號', 'name' => 'mer_id', 'type' => 'text', 'id' => 'mer_id', 'size' => 80, 'value' => $mer_id),
                    array('width' => 300, 'txt' => '付款方式', 'name' => 'payment_type', 'type' => 'text', 'id' => 'payment_type', 'size' => 80, 'value' => 'alipay'),
                    array('width' => 300, 'txt' => '交易金額', 'name' => 'amt', 'type' => 'text', 'id' => 'amt', 'size' => 80, 'value' => $amount),
                    array('width' => 300, 'txt' => '商品編號1', 'name' => 'goods_name[]', 'type' => 'text', 'id' => 'goods_name[]', 'size' => 80, 'value' => 'A10000'),
                    array('width' => 300, 'txt' => '商品編號2', 'name' => 'goods_name[]', 'type' => 'text', 'id' => 'goods_name[]', 'size' => 80, 'value' => 'A10000'),
                    array('width' => 300, 'txt' => '商品數量1', 'name' => 'goods_amount[]', 'type' => 'text', 'id' => 'goods_amount[]', 'size' => 80, 'value' => '1'),
                    array('width' => 300, 'txt' => '商品數量2', 'name' => 'goods_amount[]', 'type' => 'text', 'id' => 'goods_amount[]', 'size' => 80, 'value' => '2'),
                    array('width' => 300, 'txt' => '賣家自訂交易編號', 'name' => 'od_sob', 'type' => 'text', 'id' => 'od_sob', 'size' => 80, 'value' => 'ali' . $order_no),
                    array('width' => 300, 'txt' => '交易完成後返回的網址', 'name' => 'return_url', 'type' => 'text', 'id' => 'return_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '交易結果返回網址', 'name' => 'ok_url', 'type' => 'text', 'id' => 'ok_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '購買者信箱', 'name' => 'buyer_email', 'type' => 'text', 'id' => 'buyer_email', 'size' => 80, 'value' => '@greenworld.com.tw'),
                    array('width' => 300, 'txt' => '購買者電話', 'name' => 'buyer_tel', 'type' => 'text', 'id' => 'buyer_tel', 'size' => 80, 'value' => '0911222333'),
                    array('width' => 300, 'txt' => '買家姓名', 'name' => 'buyer_name', 'type' => 'text', 'id' => 'buyer_name', 'size' => 80, 'value' => '消費者'),
                ),
            'submit' => array('name' => 't_submit','value' => '送出'),
        ),
        array(
            'title' => '財付通串接輸入參數',
            'form' => array('name' => 't_form','act' => 'qa_index.php',),
            'input' =>
                array(
                    array('width' => 300, 'txt' => '檢查碼', 'name' => 'check_mac', 'type' => 'checkbox', 'id' => 'check_mac', 'value' => ''),
                    array('width' => 300, 'txt' => 'ECBank商店代號', 'name' => 'mer_id', 'type' => 'text', 'id' => 'mer_id', 'size' => 80, 'value' => $mer_id),
                    array('width' => 300, 'txt' => '付款方式', 'name' => 'payment_type', 'type' => 'text', 'id' => 'payment_type', 'size' => 80, 'value' => 'tenpay'),
                    array('width' => 300, 'txt' => '交易金額', 'name' => 'amt', 'type' => 'text', 'id' => 'amt', 'size' => 80, 'value' => $amount),
                    array('width' => 300, 'txt' => '賣家自訂交易編號', 'name' => 'od_sob', 'type' => 'text', 'id' => 'od_sob', 'size' => 80, 'value' => 'tp' . $order_no),
                    array('width' => 300, 'txt' => '交易完成後返回的網址', 'name' => 'return_url', 'type' => 'text', 'id' => 'return_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '交易結果返回網址', 'name' => 'ok_url', 'type' => 'text', 'id' => 'ok_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '付款截止時間', 'name' => 'expire_time', 'type' => 'text', 'id' => 'expire_time', 'size' => 80, 'value' => ''),
                ),
            'submit' => array('name' => 't_submit','value' => '送出'),
        ),
        array(
            'title' => '歐付寶第三方支付串接輸入參數',
            'form' => array('name' => 't_form','act' => 'qa_index.php',),
            'input' =>
                array(
                    array('width' => 300, 'txt' => '檢查碼', 'name' => 'check_mac', 'type' => 'checkbox', 'id' => 'check_mac', 'value' => ''),
                    array('width' => 300, 'txt' => '歐付寶第三方支付串接輸入參數', 'name' => 'mer_id', 'type' => 'text', 'id' => 'mer_id', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '付款方式', 'name' => 'payment_type', 'type' => 'text', 'id' => 'payment_type', 'size' => 80, 'value' => 'allpay'),
                    array('width' => 300, 'txt' => '交易金額', 'name' => 'amt', 'type' => 'text', 'id' => 'amt', 'size' => 80, 'value' => $amount),
                    array('width' => 300, 'txt' => '賣家自訂交易編號', 'name' => 'od_sob', 'type' => 'text', 'id' => 'od_sob', 'size' => 80, 'value' => 'ap' . $order_no),
                    array('width' => 300, 'txt' => '交易描述', 'name' => 'desc', 'type' => 'text', 'id' => 'desc', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '交易完成後返回的網址', 'name' => 'return_url', 'type' => 'text', 'id' => 'return_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '交易結果返回網址', 'name' => 'ok_url', 'type' => 'text', 'id' => 'ok_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '交易履約完成通知網', 'name' => 'complete_url', 'type' => 'text', 'id' => 'complete_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '商品狀態變更完成通知網', 'name' => 'delivery_url', 'type' => 'text', 'id' => 'delivery_url', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '商品商品編號1', 'name' => 'item_id[]', 'type' => 'text', 'id' => 'item_id[]', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '商品商品編號2', 'name' => 'item_id[]', 'type' => 'text', 'id' => 'item_id[]', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '商品名稱1', 'name' => 'item_name[]', 'type' => 'text', 'id' => 'item_name[]', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '商品名稱2', 'name' => 'item_name[]', 'type' => 'text', 'id' => 'item_name[]', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '商品數量1', 'name' => 'item_amount[]', 'type' => 'text', 'id' => 'item_amount[]', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '商品數量2', 'name' => 'item_amount[]', 'type' => 'text', 'id' => 'item_amount[]', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '商品猶豫期1', 'name' => 'item_consider_hour[]', 'type' => 'text', 'id' => 'item_consider_hour[]', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '商品猶豫期2', 'name' => 'item_consider_hour[]', 'type' => 'text', 'id' => 'item_consider_hour[]', 'size' => 80, 'value' => ''),
                    array('width' => 300, 'txt' => '商品網址1', 'name' => 'item_url[]', 'type' => 'text', 'id' => 'item_url[]', 'size' => 80, 'value' => $ecbank_web_root),
                    array('width' => 300, 'txt' => '商品網址2', 'name' => 'item_url[]', 'type' => 'text', 'id' => 'item_url[]', 'size' => 80, 'value' => $ecbank_web_root),
                ),
            'submit' => array('name' => 't_submit','value' => '送出'),
        ),
    );
    
    if (!empty($error)) {
        set_html('<div>', 4);
        set_html('錯誤: ' . $error . '<br />', 6);
        set_html('</div>', 4);
    }
    
    foreach ($options as $o_idx => $option) {
        gen_ui_form($option['title'], $option['form'], $option['input'], $option['submit']);
    }
    
    require_once(SYS_ROOT . "./_admin/include/page_bottom.inc.php");
    exit;
?>