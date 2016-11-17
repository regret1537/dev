<?php
/**
 * ���H BIN ���� CSV
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
	 *  �L�X���
	 *
	 * @author     Shawn Chang
	 * @category   misc
	 * @param      string $content ��Ƥ��e
	 * @version    1.0
	 */
	function disp($content) {
		echo $content . PHP_EOL;
	}
	
	class CtcbBin
	{
		/**
		 *  �ˬd�O�_�����H
		 *
		 * @author     Shawn Chang
		 * @category   CtcbBin
		 * @param      string $bank_name �Ȧ�W��
		 * @version    1.0
		 */
		private function isCtcb($bank_name)
		{
			$ctcb_bank_names = array('����H�U', '���H');
			foreach($ctcb_bank_names as $ctcb_name) {
				echo $this->toBIG5($ctcb_name) . ',' . $bank_name .PHP_EOL;# test
				if(substr_count($bank_name, $this->toBIG5($ctcb_name)) > 0) {
					return true;
				}
			}
			return false;
		}
		
		/**
		 *  ���o�d�����O
		 *
		 * @author     Shawn Chang
		 * @category   CtcbBin
		 * @param      string $bank_name �Ȧ�W��
		 * @version    1.0
		 */
		public function getCardType($bank_name)
		{
			$card_type = '';
			if ($this->isCtcb($bank_name)) {
				$card_type = '�ۦ�d';
			} else {
				$card_type = '�L��d';
			}
			return $this->toBIG5($card_type);
		}
		
		/**
		 *  ��big5
		 *
		 * @author     Shawn Chang
		 * @category   CtcbBin
		 * @param      string $content ���e
		 * @version    1.0
		 */
		private function toBIG5($content)
		{
			// return mb_convert_encoding($content, 'BIG5', 'UTF-8');
			return $content;
		}
	}

	class BinCsv
	{
		private $csv_file_path = '';
		
		public function __construct($path)
		{
			$this->csv_file_path = $path;
			$this->rmCsv($this->csv_file_path); // �R������
			$new_title_row = $this->genNewRow(['bin_code', 'bin_name', 'card_type']); // ���ͼ��D�C
			$this->saveCsv($new_title_row); // �g�J���D�C
		}
		
		/**
		 *  �R������
		 *
		 * @author     Shawn Chang
		 * @category   BinCsv
		 * @param      string $path CSV ���|
		 * @version    1.0
		 */
		private function rmCsv($path)
		{
			// �Y�ɮצs�b�h���R������
			if (file_exists($path)) {
				$result = unlink($path);
				if($result === false) {
					throw new Exception('11');
				}
			}
		}
		
		/**
		 *  ����CSV�C
		 *
		 * @author     Shawn Chang
		 * @category   BinCsv
		 * @param      array $path CSV ���|
		 * @version    1.0
		 */
		public function genNewRow($data)
		{
			$new_row = implode(',', $data) . PHP_EOL;
			return $new_row;
		}
		
		/**
		 *  �g�JCSV
		 *
		 * @author     Shawn Chang
		 * @category   BinCsv
		 * @param      string $row CSV �C
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
		 *  Ū��CSV
		 *
		 * @author     Shawn Chang
		 * @category   BinCsv
		 * @param      string $path CSV ���|
		 * @version    1.0
		 */
		public function loadCsv($path)
		{
			// Ū���ӷ���
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
		
		// Ū���ӷ���
		$rows = $csv->loadCsv($csv_file_path);
		
		// ���o���e
		foreach($rows as $row) {
			list($bin_code, $bank_name, $remark) = explode(',', $row);
			$card_type = $ctcb_bin->getCardType($bank_name);
			$new_row = $csv->genNewRow([$bin_code, $bank_name, $card_type, '']); // ���ͤ��e
			$csv->saveCsv($new_row); // �g�J���D�C
		}
		disp('finish');
	} catch (Exception $e) {
		disp('Exception: ' . $e->getMessage());
	}
	
?>