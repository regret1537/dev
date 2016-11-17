<?php
	// $aTest_Response = ['result' => 'ajax_service'];
	// echo json_encode($aTest_Response);
	
	if (isset($_POST['checkInOrCheckOut']) and isset($_POST['isPre'])) {
		$aTest_Response = [
			'checkInOrCheckOut' => $_POST['checkInOrCheckOut'],
			'isPre' => $_POST['isPre'],
		];
		echo json_encode($aTest_Response);
	} else {
		
		echo '<pre>'.print_r($_SERVER, true).'</pre>';
		echo 'ajax_response';
	}
?>