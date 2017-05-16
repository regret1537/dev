<?php
    // 人工填入區塊 {
    $encryptKey = ''; // 加解密 Key
    $odnb = ''; // 單號
    $authSr = ''; // 授權單號
    $hiddenCardNo = ''; // 卡號前6 ****** 卡號後4
    $pack2 = ''; // 002B 電文(移除#)
    $send003A = ''; // 003A 電文(移除#)
    $pack3 = ''; // 003B 電文(移除#)

    $amount = 0; // o_auth.amount
    $processTime = ''; // o_auth.dt
    $isGuolu = ''; // o_auth.isGuolu
    $glCity = ''; // o_auth.glCity
    $eci = 0; // o_auth.eci
    $authCode = ''; // o_auth.auth
    // }