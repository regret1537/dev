<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
    $src_dir_path = $_SERVER["DOCUMENT_ROOT"] . 'dev/file';
    
    $replace_cont_list = array(
        array('payment' => 'credit', 'payment_en_desc' => 'Credit','payment_tw_desc' => '信用卡(一次付清)'),
        array('payment' => 'credit_03', 'payment_en_desc' => 'Credit(3 Installments)','payment_tw_desc' => '信用卡(3期)'),
        array('payment' => 'credit_06', 'payment_en_desc' => 'Credit(6 Installments)','payment_tw_desc' => '信用卡(6期)'),
        array('payment' => 'credit_12', 'payment_en_desc' => 'Credit(12 Installments)','payment_tw_desc' => '信用卡(12期)'),
        array('payment' => 'credit_18', 'payment_en_desc' => 'Credit(18 Installments)','payment_tw_desc' => '信用卡(18期)'),
        array('payment' => 'credit_24', 'payment_en_desc' => 'Credit(24 Installments)','payment_tw_desc' => '信用卡(24期)'),
        array('payment' => 'webatm', 'payment_en_desc' => 'Web-ATM', 'payment_tw_desc' => 'Web-ATM'),
        array('payment' => 'atm', 'payment_en_desc' => 'ATM','payment_tw_desc' => 'ATM'),
        array('payment' => 'cvs', 'payment_en_desc' => 'CVS','payment_tw_desc' => '超商代碼'),
        array('payment' => 'barcode', 'payment_en_desc' => 'BARCODE','payment_tw_desc' => '超商條碼'),
        array('payment' => 'alipay', 'payment_en_desc' => 'Alipay','payment_tw_desc' => '支付寶'),
        array('payment' => 'tenpay', 'payment_en_desc' => 'Tenpay','payment_tw_desc' => '財付通'),
        array('payment' => 'topupused', 'payment_en_desc' => 'TopUpUsed','payment_tw_desc' => '儲值/餘額消費'),
    );
    $gen_file_list = array();
    recurciveScan($src_dir_path);
    $gen_dir_path = $src_dir_path . '/../lazy_gen_' . date('ymdhis');
    mkdir($gen_dir_path);
    
    foreach ($replace_cont_list as $replace_cont) {
        $lazy_from = array();
        $lazy_to = array();
        foreach ($replace_cont as $key => $val) {
            $l_p = genLazyPrompt($key);
            $lazy_from[] = $l_p;
            $lazy_to[] = $val;
        }
        if (!empty($gen_file_list)) {
            foreach ($gen_file_list as $file_type => $file_list) {
                foreach ($file_list as $src_file_path) {
                    if (isLazyPrompt($src_file_path)) {
                        switch ($file_type) {
                            case 'dir':
                                $new_name = str_replace($lazy_from, $lazy_to, $src_file_path);
                                $gen_file_path = str_replace($src_dir_path, $gen_dir_path, $new_name);
                                disp('from ' . $src_file_path . ' to ' . $gen_file_path);
                                if (file_exists($gen_file_path)) {
                                    rmdir($gen_file_path);
                                }
                                mkdir($gen_file_path);
                                break;
                            case 'file':
                                $new_name = str_replace($lazy_from, $lazy_to, $src_file_path);
                                $gen_file_path = str_replace($src_dir_path, $gen_dir_path, $new_name);
                                if (file_exists($gen_file_path)) {
                                    unlink($gen_file_path);
                                }
                                disp('from ' . $src_file_path . ' to ' . $gen_file_path);
                                $file_cont = file_get_contents($src_file_path);
                                $gen_file_cont = str_replace($lazy_from, $lazy_to, $file_cont);
                                file_put_contents($gen_file_path, $gen_file_cont);
                                break;
                            default:
                                # do nothing
                        }
                    }
                }
            }
            disp('<hr />');
        }
    }
    
    # zip files
    $zip_file_list = scandir($gen_dir_path);
    chdir($gen_dir_path);
    $ignore_files = array('.', '..');
    foreach ($zip_file_list as $file_name) {
        if (!in_array($file_name, $ignore_files)) {
            exec('"C:\Program Files\7-Zip\7z.exe" a -tzip ' . $file_name . '.zip ' . $file_name);
        }
    }
    
    function recurciveScan ($abs_dir_path) {
        global $gen_file_list;
        $ignore_files = array('.', '..');
        $file_list = scandir($abs_dir_path);
        foreach ($file_list as $file_name) {
            if (!in_array($file_name, $ignore_files)) {
                $abs_file_path = $abs_dir_path . '/' . $file_name;
                if (is_dir($abs_file_path)) {
                    $gen_file_list['dir'][] = $abs_file_path;
                    recurciveScan($abs_file_path);
                } else {
                    $gen_file_list['file'][] = $abs_file_path;
                }
            }
        }
    }
    
    function isLazyPrompt($cont) {
        $reg_str = '/\[lazy=[a-zA-Z0-9_]+\]/';
        return preg_match($reg_str, $cont);
    }
    
    function replacePrompt($cont, $replace_cont) {
        $fmt_cont = $replace_cont;
        foreach ($fmt_cont as $key => $val) {
            $fmt_cont[genLazyPrompt($key)] = $val;
        }
    }
    
    function genLazyPrompt($cont) {
        $lazy_prompt = '[lazy=' . $cont . ']';
        return $lazy_prompt;
    }
    
    include('../tpl/footer.php');
?>