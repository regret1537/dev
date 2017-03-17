<?php
include_once('CardNoCheck.php');

class NcccCheck implements CardNoCheck
{
	public static function check($row)
	{
		$data = self::parse($row);
		$path = $data['path'];
		if (self::isNcccIn($path)) {
			return true;
		}

		if (self::isNcccOut($path)) {
			return true;
		}
		return false;
	}

	public static function parse($row)
	{
		list($path, $type, $cardNo) = explode("\t", $row);
		return compact(['path', 'type', 'cardNo']);
	}

	private static function isNcccIn($path)
	{
		return preg_match('/^\/vhost\/ecpay.com.tw\/nccc_in\/\d{10}_\d{8}\.[ut8|rsp]/', $path);
	}

	private static function isNcccOut($path)
	{
		return preg_match('/^\/vhost\/ecpay.com.tw\/nccc_out_bk\/\d{8}\/\d{10}\.dat/', $path);
	}
}