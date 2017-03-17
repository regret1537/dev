<?php
//測試用店家參數 
abstract class TestMerchantInfo
{
	//特店編號
    const MERCHANT_ID = '2000132';
	//Hash Key
    const HASH_KEY = '5294y06JbISpM5x9';
	//Hash IV
    const HASH_IV = 'v77hoKGq4kWxNNIS';
}

// 歐付寶正式測試環境網址
abstract class ECPayTestURL 
{
    const CHECKOUT = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V2';
	const CHARGEBACK = 'https://payment-stage.ecpay.com.tw/Cashier/AioChargeback';
}
?>
