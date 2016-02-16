<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
    $src_path = 'D:/Allpay/allpay_service/cart_dev/payment/allPay_HikaShop_v1.0.1_UTF8_Release02xx/allpay_webatm/';
    $des_path = 'C:/wamp/www/jml_3_4/plugins/hikashoppayment/allpay_webatm/';
    
    $file_list = scandir($src_path);
    $ignore_files = array('.', '..');
    foreach ($file_list as $file_name) {
        if (!in_array($file_name, $ignore_files)) {
            $src_file_path = $src_path . $file_name;
            $des_file_path = $des_path . $file_name;
            disp('copy ' . $src_file_path . ' to ' . $des_file_path);
            copy($src_file_path, $des_file_path);
        }
    }
    
    include('../tpl/footer.php');
?>