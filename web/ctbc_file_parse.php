<?php
    include('../tpl/header.php');
    include('../lib/html_common.inc');
    include('../lib/misc.inc');
    
	$aValidate_Type = array('G', 'F', 'U');
	

// 國旅
$sParse_Type = 'U';
$sCtcb_Content = 'H                                                                                                   
D 0130003612      4649618819000207    093672 04062016 0005000 07012016 07022016 022     A           
D 0130003612      4392372191000562    769512 04062016 0001880 04072016 04082016 003     A           
T                                                                                                   
';
// 分期
// $sParse_Type = 'F';
// $sCtcb_Content = 'H01541006800001000080001188802016090899306807A                                                      
// B05015410068431195******5867240000132400001312000315002016090708509600                 A01120160119
// B05015410068431195******5462120000100000001000000120002016090801147000                 A01120150909 
// H01541007200001000010000840002016090899306936A                                                      
// B05015410072552049******3777030002800000028000000840002016090704374600                 A01120150909 
// T140303000000006';
// 一般
// $sParse_Type = 'G';
// $sCtcb_Content = '81609050133300239000000110000001126000000010000000104 allpay-test                                                                                               
// 11606284096700012341238000401234560000000100allpay-test              5                                                                                          
// 11606284096700012341238000401234560000000101allpay-test              6                                                                                          
// 11606284096700012341238000401234560000000102allpay-test              7                                                                                          
// 11606284023100012341239000406168890000000103allpay-test              5                                                                                          
// 11606284023100012341239000409142440000000104allpay-test              6                                                                                          
// 11606284023100012341239000400455130000000105allpay-test              7                                                                                          
// 11606283528150012341236000403726530000000100allpay-test              5                                                                                          
// 11606283528150012341236000406348570000000101allpay-test              6                                                                                          
// 11606283528150012341236000408316140000000102allpay-test              7                                                                                          
// 11606283560580012341233000402249120000000103allpay-test              5                                                                                          
// 11606283560580012341233000414871600000000104allpay-test              6                                                                                          
// 11606285560580012341233000407493810000000105allpay-test              7                             0                                                            
// 916090500000019                                                                                                                                                 ';
	$aRecord_Type_List = array(
		'F' => array(
			'HEADER' => 'H',
			'DETAIL' => 'B',
			'TRAILER' => 'T',
		),
		'U' => array(
			'HEADER' => 'H',
			'DETAIL' => 'D',
			'TRAILER' => 'T',
		),
		'G' => array(
			'HEADER' => '8',
			'DETAIL' => '1',
			'TRAILER' => '9',
		),
	);
	$aParse_Type = $aRecord_Type_List[$sParse_Type];
	$aLength_List = array(
		'F' => array(
			'HEADER' => array(1,9,5,5,9,8,8,1,54),
			'DETAIL' => array(1,2,9,16,2,8,8,8,8,6,2,17,1,12),
			'TRAILER' => array(1,6,9),
		),
		'U' => array(
			'HEADER' => array(1,99),
			'DETAIL' => array(1,1,15,1,19,1,6,1,8,1,7,1,8,1,8,1,3,5,1,11,1),
			'TRAILER' => array(1,99),
		),
		'G' => array(
			'HEADER' => array(1,6,9,1,8,10,8,10,1,40,1,1,4,60),
			'DETAIL' => array(1,6,16,3,2,6,10,25,1,1,8,20,1,15,40,5),
			'TRAILER' => array(1,6,8,145),
		),
	);
	$aParse_Length = $aLength_List[$sParse_Type];
    $aCtcb_Rows = explode("\n", $sCtcb_Content);
    
    foreach ($aCtcb_Rows as $sTmp_Row) {
        $sTmp_1_Chr = substr($sTmp_Row, 0, 1);
        switch ($sTmp_1_Chr) {
            case $aParse_Type['HEADER']:
                // HEADER
				$aLength = $aParse_Length['HEADER'];
                break;
            case $aParse_Type['DETAIL']:
                // DETAIL
				$aLength = $aParse_Length['DETAIL'];
                break;
			case $aParse_Type['TRAILER']:
				// TRAILER
				$aLength = $aParse_Length['TRAILER'];
				break;
        }
        
        $iStart_Idx = 0;
        foreach ($aLength as $sTmp_Len) {                
            $sTmp_String = substr($sTmp_Row, $iStart_Idx , $sTmp_Len);
            disp(str_replace(' ', '@', $sTmp_String));
            $iStart_Idx += $sTmp_Len;
        }
		$sTmp_String = substr($sTmp_Row, $iStart_Idx);
		disp('rest: ' . str_replace(' ', '@', $sTmp_String));
        disp('');
    }
    
    include('../tpl/footer.php');
?>