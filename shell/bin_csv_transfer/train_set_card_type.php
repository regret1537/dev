<?php
/**
 * 中信 BIN 表轉 CSV
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @version    1.0
 */
	define('CSV_DIR_PATH', './file');
	
	$csv_file_name = 'bin_table_161103_01.csv';
	$csv_file_path = CSV_DIR_PATH . '/' . $csv_file_name;
	$new_csv_file_name = 'new_bin_table_' . date('ymd') . '_01.csv';
	$new_csv_file_path = CSV_DIR_PATH . '/' . $new_csv_file_name;
	
	/**
	 *  印出資料
	 *
	 * @author     Shawn Chang
	 * @category   misc
	 * @param      string $content 資料內容
	 * @version    1.0
	 */
	function disp($content) {
		echo $content . PHP_EOL;
	}
	
	class CtcbBin
	{
		/**
		 *  檢查是否為中信
		 *
		 * @author     Shawn Chang
		 * @category   CtcbBin
		 * @param      string $bank_name 銀行名稱
		 * @version    1.0
		 */
		private function isCtcb($bank_name)
		{
			$ctcb_bank_names = array('中國信託', '中信');
			foreach($ctcb_bank_names as $ctcb_name) {
				if(substr_count($bank_name, $this->toBIG5($ctcb_name)) > 0) {
					return true;
				}
			}
			return false;
		}
		
		/**
		 *  取得卡片類別
		 *
		 * @author     Shawn Chang
		 * @category   CtcbBin
		 * @param      string $bank_name 銀行名稱
		 * @version    1.0
		 */
		public function getCardType($bank_name)
		{
			$card_type = '';
			if ($this->isCtcb($bank_name)) {
				$card_type = '自行卡';
			} else {
				$card_type = '他行卡';
			}
			return $this->toBIG5($card_type);
		}
		
		/**
		 *  轉big5
		 *
		 * @author     Shawn Chang
		 * @category   CtcbBin
		 * @param      string $content 內容
		 * @version    1.0
		 */
		private function toBIG5($content)
		{
			return mb_convert_encoding($content, 'BIG5', 'UTF-8');
		}
	}

	class BinCsv
	{
		private $csv_file_path = '';
		
		public function __construct($path)
		{
			$this->csv_file_path = $path;
			$this->rmCsv($this->csv_file_path); // 刪除舊檔
			$new_title_row = $this->genNewRow(['bin_code', 'bin_name', 'card_type']); // 產生標題列
			$this->saveCsv($new_title_row); // 寫入標題列
		}
		
		/**
		 *  刪除舊檔
		 *
		 * @author     Shawn Chang
		 * @category   BinCsv
		 * @param      string $path CSV 路徑
		 * @version    1.0
		 */
		private function rmCsv($path)
		{
			// 若檔案存在則先刪除舊檔
			if (file_exists($path)) {
				$result = unlink($path);
				if($result === false) {
					throw new Exception('11');
				}
			}
		}
		
		/**
		 *  產生CSV列
		 *
		 * @author     Shawn Chang
		 * @category   BinCsv
		 * @param      array $path CSV 路徑
		 * @version    1.0
		 */
		public function genNewRow($data)
		{
			$new_row = implode(',', $data) . PHP_EOL;
			return $new_row;
		}
		
		/**
		 *  寫入CSV
		 *
		 * @author     Shawn Chang
		 * @category   BinCsv
		 * @param      string $row CSV 列
		 * @version    1.0
		 */
		public function saveCsv($row)
		{
			if (file_exists($this->csv_file_path)) {
				$result = file_put_contents($this->csv_file_path, $row, FILE_APPEND);
			} else {
				$result = file_put_contents($this->csv_file_path, $row);
			}
			if($result < 1) {
				throw new Exception('12');
			}
		}
		
		/**
		 *  讀取CSV
		 *
		 * @author     Shawn Chang
		 * @category   BinCsv
		 * @param      string $path CSV 路徑
		 * @version    1.0
		 */
		public function loadCsv($path)
		{
			// 讀取來源檔
			$rows = file($path, FILE_IGNORE_NEW_LINES);
			if(count($rows) < 1) {
				throw new Exception('13');
			}
			return $rows;
		}
	}
	
	try {
		$ctcb_bin = new CtcbBin();
		$csv = new BinCsv($new_csv_file_path);
		
		// 讀取來源檔
		$rows = $csv->loadCsv($csv_file_path);
		
		// 取得內容
		foreach($rows as $row) {
			list($bin_code, $bank_name, $remark) = explode(',', $row);
			$card_type = $ctcb_bin->getCardType($bank_name);
			$new_row = $csv->genNewRow([$bin_code, $bank_name, $card_type, '']); // 產生內容
			$csv->saveCsv($new_row); // 寫入內容
		}
		disp($new_csv_file_path . ' created');
	} catch (Exception $e) {
		disp('Exception: ' . $e->getMessage());
	}
	
?>