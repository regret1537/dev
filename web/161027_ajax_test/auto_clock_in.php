<?php
	define('WEB_ROOT', '../..');
	include(WEB_ROOT . '/tpl/header.php');
?>
	<script src="./jquery/jquery-1.7.2.min.js" type="text/javascript"></script>
	<div id="queryResult">
	</div>
	
	<script type="text/javascript">
		$(document).ready(function() {
			actCheckInOrCheckOut(0);
		});
		function actCheckInOrCheckOut(checkInOrCheckOut,isPre) {
			isPre = isPre || "false";
        
			$.ajax({
				type: "post",
				url: "https://admin.ecpay.com.tw/Attendance/EditMonthForCustomerService",
				// url: "http://homestead.app/dev/web/161027_ajax_test/ajax_service.php",
				data:
				{
					checkInOrCheckOut: checkInOrCheckOut,
					isPre: isPre
				},
				success: function (data) {
					$("#queryResult").html(data);
					// if (data != null) {
						// $("#queryResult").html(data);
					// }
				}
			});
		}
	</script>
<?php
	include(WEB_ROOT . '/tpl/footer.php');
?>