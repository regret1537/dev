<?php
/**
 * ���H BIN ���� CSV
 *
 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
 * @version    1.0
 */
 
	/**
	 *  �L�X���
	 *
	 * @author     Shawn Chang <shawn.chang@greenworld.com.tw>
	 * @category   misc
	 * @param      String $sContent ��Ƥ��e
	 * @version    1.0
	 */
	function disp($sContent) {
		echo $sContent . PHP_EOL;
	}
 
	$sSrc_Dir = __DIR__ . '/file';
	$sSrc_File_Name = 'bin_table_201608.csv';
	$sSrc_File_Path = $sSrc_Dir . '/' . $sSrc_File_Name;
	$sDest_File_Name = 'bin_table_' . date('ymd') . '.csv';
	$sDest_File_Path = $sSrc_Dir . '/' . $sDest_File_Name;
	$aCSV_Rows = file($sSrc_File_Path);
	$sNew_CSV_Rows = 'bin_num,bin_bank,bin_type' . PHP_EOL;
	// �� 1 �欰���D�A���L
	for($iIdx = 1 ; $iIdx < (count($aCSV_Rows) - 1) ; $iIdx++) {
		list($sTmp_BIN_Num, $sTmp_BIN_Bank, $sTmp_Remark) = explode(',', $aCSV_Rows[$iIdx]);
		$sNew_Row = trim($sTmp_BIN_Num) . ',' . trim($sTmp_BIN_Bank) . ',';
		if (substr_count('����H�U', $sTmp_BIN_Bank) > 0 or substr_count('���H', $sTmp_BIN_Bank) > 0) {
			$sNew_Row .= '�ۦ�d';
		} else {
			$sNew_Row .= '�L��d';
		}
		$sNew_CSV_Rows .= $sNew_Row . ',' . PHP_EOL;
		unset($sTmp_BIN_Num, $sTmp_BIN_Bank, $sTmp_Remark, $sNew_Row);
	}
	$iFile_Bytes = file_put_contents($sDest_File_Path, $sNew_CSV_Rows);
	if ($iFile_Bytes > 0) {
		disp($sDest_File_Path . ' created.');
	} else {
		disp($sDest_File_Path . ' created failed.');
	}
	unset($sSrc_Dir, $sSrc_File_Name, $sSrc_File_Path, $sDest_File_Name, $sDest_File_Path, $aCSV_Rows, $sNew_CSV_Rows, $iFile_Bytes);
?>