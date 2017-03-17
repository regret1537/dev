<?php
include_once('CardNoCheck.php');

class PhpCheck implements CardNoCheck
{
	public static function check($row)
	{
		$whiteList = [
			'/vhost/ecpay.com.tw/htdocs/g_Close_gluno_CHN.php	VISA	4392375100000300',
			'/vhost/ecpay.com.tw/htdocs/g_auth_close.php	MASTERCARD	5468580005011411',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re.php	MASTERCARD	5135880012431195',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re.php	VISA	4311951002423986',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re.php	MASTERCARD	5135880012431195',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re.php	VISA	4311951002423986',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re.php	VISA	4311951002423986',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re.php	DINERS_CLUB_CARTE_BLANCHE	30000000320071',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re.php	VISA	4311951002423986',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re.php	DINERS_CLUB_CARTE_BLANCHE	30000000320071',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re_over.php	MASTERCARD	5135880012431195',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re_over.php	VISA	4311951002423986',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re_over.php	MASTERCARD	5135880012431195',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re_over.php	VISA	4311951002423986',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re_over.php	VISA	4311951002423986',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re_over.php	DINERS_CLUB_CARTE_BLANCHE	30000000320071',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re_over.php	VISA	4311951002423986',
			'/vhost/ecpay.com.tw/htdocs/g_Close_grniFn_CHN_re_over.php	DINERS_CLUB_CARTE_BLANCHE	30000000320071',
			'/vhost/ecpay.com.tw/htdocs/g_authlist.php	MASTERCARD	5313200707191539',
			'/vhost/ecpay.com.tw/htdocs/g_authlist.php	MASTERCARD	5313200707191539',
			'/vhost/ecpay.com.tw/htdocs/g_authlist.php	MASTERCARD	5313200707241136',
			'/vhost/ecpay.com.tw/htdocs/form_ssl.php	MASTERCARD	5523358900015404',
			'/vhost/ecpay.com.tw/htdocs/form_ssl.php	MASTERCARD	5523358900015404',
			'/vhost/ecpay.com.tw/htdocs/form_ssl.php	VISA	4056506900004608',
			'/vhost/ecpay.com.tw/htdocs/form_ssl.php	VISA	4056506900004608'
		];
		if (in_array($row, $whiteList)) {
			return true;
		} else {
			return false;
		}
	}

	public static function parse($row)
	{
	}
}