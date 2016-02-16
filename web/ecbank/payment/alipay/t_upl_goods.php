<?php
    define('WEB_ROOT', 'C:/wamp/www/dev');
    include(WEB_ROOT . '/tpl/header.php');
    include(WEB_ROOT . '/lib/html_common.inc');
    include(WEB_ROOT . '/lib/misc.inc');

    // 商店設定在ECBank管理後台的交易加密私鑰
    $enc_key = 'Htg3v48wB5L94RixlVZCeMEO';

    // type
    $type="upload_goods";

    // 商店代號
    $mer_id="9493";

    // 商品編號
    $goods_id = "A10000";

    // 商品名稱
    $goods_title = "測試商品1";

    // 商品單價
    $goods_price = "100";

    // 商品網址
    $goods_href = "http://www.test.com.tw/shop/A1000.php";

    // ECBank 商品上架網址。
    $ecbank_gateway = 'https://ecbank.com.tw/web_service/alipay_goods_upload.php';

    // 其他非必填參數，可以個人需求選填加入。

    // 串接驗證參數
    $post_str='enc_key='.$enc_key.
        '&mer_id='.$mer_id.
        '&type='.$type.
        '&goods_id='.$goods_id.
        '&goods_title='.$goods_title.
        '&goods_price='.$goods_price.
        '&goods_href='.$goods_href;

    // 使用curl取得驗證結果
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$ecbank_gateway);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_str);
    $strAuth = curl_exec($ch);

    if (curl_errno($ch)){
        $strAuth = false;
    }

    curl_close($ch);
    if($strAuth == 'state=NEW_SUCCESS'){
        echo '新增上架商品成功';
    }else if($strAuth == 'state=MODIFY_SUCCESS'){
        echo '修改上架商品成功';
    }else{
        echo '錯誤：'.$strAuth;
    }
    
    include(WEB_ROOT . '/tpl/footer.php');
?>