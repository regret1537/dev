<?php
include('config/RetriveConfig.php');

include(LIB_PATH . '/ClassCsv.php');
include(LIB_PATH . '/ClassMysql.php');
include(LIB_PATH . '/ClassDate.php');
include(LIB_PATH . '/ClassDisp.php');

$mysql = new ClassMysql(DB_HOST, DB_USER, DB_PASS);
$csv = new ClassCsv();

$sql = '';
$check_fields = ['goid', 'bkid', 'gosn', 'bksn', 'card8'];
$set_fields = ['pay_amount', 'refund_amount', 'fee', 'pay_day', 'refund_day'];
$report_fields = array_merge($check_fields, $set_fields);
$id = '';
$sn = '';
$query_format = 'Y/m/d';
$csv_format = 'Ymd';
$trade_day = '';
$refund_day = '';
$card4 = '';
$report_row = '';

try {
	// Parameter check
	if (isset($argv[1]) === false) {
		throw new Exception('01');
	}

	// Source CSV
	$src_csv_name = $argv[1];
	$src_csv_path = SRC_PATH . '/' . $src_csv_name;

	// Source CSV exist check
	if (file_exists($src_csv_path) === false) {
		throw new Exception('02');
	}

	// New CSV
	list($csv_name, $csv_ext) = explode('.', $src_csv_name);
	$new_csv_name = $csv_name . '_' . ClassDate::now('ymdHis') . '.csv';
	$new_csv_path = CSV_PATH . '/' . $new_csv_name;

	// Get CSV content
	$data = $csv->getData($src_csv_path);

	// Connect to Mysql
	$mysql->connect();
	$mysql->setDbName(DB_NAME);
	$mysql->setDbEnc(DB_ENC);

	// Write CSV fields
	$csv->setData($new_csv_path, $report_fields);

	// Parse CSV content
	foreach ($data as $row_data) {
    	$id = $row_data[0];
    	$sn = $row_data[2];
    	$trade_day = ClassDate::format($query_format, $row_data[10] . '000000');
    	$refund_day = ClassDate::format($query_format, $row_data[11] . '000000');
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
			$pay_day = ClassDate::format($csv_format, $detail[0]['rtime']);
			$refund_day = ClassDate::format($csv_format, $detail[1]['rtime']);

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

			// Write to new CSV
			$csv->setData($new_csv_path, $temp_data, true);
		}
	}
	$mysql->close();
} catch (Exception $e) {
	ClassDisp::dispString('error code: ' . $e->getMessage());
}

