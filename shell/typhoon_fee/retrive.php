<?php
/**
 * 印出訊息
 * @param  [string] $content 內容
 */
function disp($content) {
	echo $content . PHP_EOL;
}



define('DB_HOST', '10.10.1.65');
define('DB_NAME', 'train');
define('DB_USER', 'train');
define('DB_PASS', 'trainps');
define('DB_ENC', 'UTF8');
define('ROOT_PATH', '.');
define('CSV_PATH', 'file');
define('KEY', 'ax350svBgow81r4L');

include(ROOT_PATH . '/lib/ClassCsv.php');
include(ROOT_PATH . '/lib/ClassMysql.php');

$src_csv_name = $argv[1];

$mysql = new ClassMysql(DB_HOST, DB_USER, DB_PASS);
$csv = new ClassCsv(CSV_PATH . '/' . $src_csv_name);

$new_csv_name = date('ymdHi') . '.csv';
$new_csv_path = CSV_PATH . '/' . $new_csv_name;
$sql = '';
$check_fields = ['goid', 'bkid', 'gosn', 'bksn', 'card8'];
$set_fields = ['pay_amount', 'refund_amount', 'fee', 'pay_day', 'refund_day'];
$report_fields = array_merge($check_fields, $set_fields);
$id = '';
$sn = '';
$trade_day = '';
$refund_day = '';
$card4 = '';
$report_row = '';

// Write CSV fields
$report_row = implode(',', $report_fields) . PHP_EOL;
file_put_contents($new_csv_path, $report_row);

try {
	// Get CSV content
	$data = $csv->get_data();

	// Connect to Mysql
	$mysql->connect();
	$mysql->set_db_name(DB_NAME);
	$mysql->set_db_enc(DB_ENC);

	// Parse CSV content
	foreach ($data as $row_data) {
    	$id = $row_data[0];
    	$sn = $row_data[2];
    	$trade_day = date('Y/m/d', strtotime($row_data[10] . '000000'));
    	$refund_day = date('Y/m/d', strtotime($row_data[11] . '000000'));
    	$card4 = $row_data[15];

    	// Set SQL
		$sql = 'SELECT ';
		$sql .= 'CAST(AES_DECRYPT(goid, "' . KEY . '") as CHAR) goid,';
		$sql .= 'gosn,';
		$sql .= 'CAST(AES_DECRYPT(bkid,"' . KEY . '") as CHAR) bkid,';
		$sql .= 'bksn,';
		$sql .= 'amount,';
		$sql .= 'rtime,';
		$sql .= 'card8 ';
		$sql .= 'FROM uorder ';
		$sql .= 'WHERE 1=1 ';
		$sql .= 'AND (gosn = "' . $sn . '" OR bksn = "' . $sn . '")';
		$sql .= ' AND (LEFT(rtime, 10) = "' . $trade_day . '" OR LEFT(rtime, 10) = "' . $refund_day . '")';
		$sql .= ' AND (AES_DECRYPT(goid, "' . KEY . '") = "' . $id . '"';
		$sql .= ' OR AES_DECRYPT(bkid, "' . KEY . '") = "' . $id . '")';
		$sql .= ' AND RIGHT(card8, 4) = "' . $card4 . '"';
		$sql .= ' ORDER BY amount DESC';

		// Query railway detail
		$detail = $mysql->query($sql);

		// Generate a report row
		if (count($detail) > 0) {
			$pay_amount = $detail[0]['amount'];
			$refund_amount = $detail[1]['amount'];
			$fee = $pay_amount + $refund_amount;
			$pay_day = date('Ymd', strtotime($detail[0]['rtime']));
			$refund_day = date('Ymd', strtotime($detail[1]['rtime']));

			// Set the check fields
			$temp_data = [];
			foreach ($check_fields as $field) {
				if ($detail[0][$field] === $detail[1][$field]) {
					array_push($temp_data, $detail[0][$field]);
				}
			}

			// Set the rest fields
			foreach ($set_fields as $field) {
				array_push($temp_data, ${$field});
			}

			// Generate a row
			$report_row = implode(',', $temp_data) . PHP_EOL;

			// Write to new CSV
			file_put_contents($new_csv_path, $report_row, FILE_APPEND);
		}
	}
	$mysql->close();
} catch (Exception $e) {
	disp('error code: ' . $e->getMessage());
}

