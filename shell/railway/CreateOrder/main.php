<?php
    include('config.php');

    function disp($content) {
        echo print_r($content, true) . PHP_EOL . PHP_EOL;
    }

    function filter($content, $length) {
        $contentLength = strlen($content);
        if ($contentLength != $length) {
            $content = '';
        }
        return $content;
    }

    if ($encryptKey === '') {
        exit('Encrypt key is required');
    }

    // 間隔
    for ($index = 0 ; $index < 4 ; $index++) {
        disp('');
    }

    // 啟始標籤
    disp('------------------------------------------------  START  --------------------------------------------------');

    // 解析 002B 電文
    $pack2 = str_replace('#', '', $pack2);
    $parsed002B = explode('*', $pack2);
    $goId = filter($parsed002B[3], 10);
    $goSn = filter($parsed002B[4], 6);
    $bkId = filter($parsed002B[17], 10);
    $bkSn = filter($parsed002B[18], 6);
    $railwayType = trim($parsed002B[74]);

    // 解析 003B 電文
    $pack3 = str_replace('#', '', $pack3);
    $parsed003B = explode('*', $pack3);

    $timeStamp = strtotime($parsed003B[47] . ' - 8 hours'); // 若 timezone 不符
    // $timeStamp = strtotime($parsed003B[47]); // 若 timezone 相符
    $tradeTime = date('Y/m/d H:i', strtotime($processTime));
    $isClose = 0; // 請款關帳狀態，固定為 0
    $isCancel = 0; // 退款關帳狀態，固定為 0

    // 是否為國旅
    if ($isGuolu == 'Y') {
        $isGuolu = 1;
    } else {
        $isGuolu = 0;
    }

    // 火車類別
    // 1: 台鐵
    // 2: 森鐵
    if (empty($railwayType)) {
        $railwayType = '1';
    }

    // 取得 uppp
    $parsed003A = explode('*', $send003A);
    $uppps = [];
    for ($index = 0 ; $index < 8 ; $index++) {
        $value = intval($parsed003A[(6 + $index)]);
        array_push($uppps, $value);
    }
    $uppp = implode('*', $uppps);

    // 原始訂單
    $sql = "insert into uorder_gwp ";
    $sql .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,eci,authnb,RailwayType) ";
    $sql .= "values(0,'$odnb','$timeStamp',AES_ENCRYPT('$goId','$encryptKey'),'$goSn',AES_ENCRYPT('$bkId','$encryptKey'),'$bkSn','$amount','$tradeTime','$hiddenCardNo','$isGuolu','$isClose','$isCancel','$authSr','','$glCity','$pack2','','$uppp','$parsed002B[72]','$eci','$authCode','$railwayType')";
    disp($sql);
    
    // 便當
    $goCommonCount = $parsed002B[76];
    $goCommonTotal = $parsed002B[77];
    $goVegetarianCount = $parsed002B[78];
    $goVegetarianTotal = $parsed002B[79];
    $bkCommonCount = $parsed002B[80];
    $bkCommonTotal = $parsed002B[81];
    $bkVegetarianCount = $parsed002B[82];
    $bkVegetarianTotal = $parsed002B[83];
    $sql = "INSERT INTO bento ";
    $sql .= "(gw_pay_sn,go_sn,go_nv_ben_num,go_nv_ben_total,go_v_ben_num,go_v_ben_total,bk_sn,bk_nv_ben_num,bk_nv_ben_total,bk_v_ben_num,bk_v_ben_total) ";
    $sql .= "VALUE('$authSr','$goSn','$goCommonCount','$goCommonTotal','$goVegetarianCount','$goVegetarianTotal','$bkSn','$bkCommonCount','$bkCommonTotal','$bkVegetarianCount','$bkVegetarianTotal')";
    disp($sql);

    // 預約訂單
    $sql = "insert into uorder_bk ";
    $sql .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,eci,authnb,SendNEC,RailwayType) ";
    $sql .= "values(0,'$odnb','$timeStamp',AES_ENCRYPT('$goId','$encryptKey'),'$goSn',AES_ENCRYPT('$bkId','$encryptKey'),'$bkSn','$amount','$tradeTime','$hiddenCardNo','$isGuolu','$isClose','$isCancel','$authSr','$parsed003B[2]','$glCity','$pack2','$pack3','$uppp','$parsed002B[72]','$eci','$authCode','$send003A','$railwayType')";
    disp($sql);

    // 成交訂單
    $sql = "insert into uorder ";
    $sql .= "(aa,odnb,tmstp,goid,gosn,bkid,bksn,amount,rtime,card8,guolu,rclose,rcancel,gwpaysn,necrrn,glwhere,pack2,pack3,uppp,spay,eci,authnb,SendNEC,RailwayType) ";
    $sql .= "values(0,'$odnb','$timeStamp',AES_ENCRYPT('$goId','$encryptKey'),'$goSn',AES_ENCRYPT('$bkId','$encryptKey'),'$bkSn','$amount','$tradeTime','$hiddenCardNo','$isGuolu','$isClose','$isCancel','$authSr','$parsed003B[2]','$glCity','$pack2','$pack3','$uppp','$parsed002B[72]','$eci','$authCode','$send003A','$railwayType')";
    disp($sql);

    // 結束標籤
    disp('-------------------------------------------------  END  ---------------------------------------------------');