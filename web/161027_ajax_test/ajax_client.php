<?php
	define('WEB_ROOT', '../..');
	include(WEB_ROOT . '/tpl/header.php');
?>
	<script src="./jquery/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		function actCheckInOrCheckOut(checkInOrCheckOut,isPre) {
			isPre = isPre || "false";
			
			$.ajax({
				type: 'post',
				url: 'http://homestead.app/dev/web/161027_ajax_test/ajax_service.php',
				data:
				{
					checkInOrCheckOut: checkInOrCheckOut,
					isPre: isPre
				},
				success: function (result) {
					alert(result);
				}
			});
		}
	</script>

	
	<input type="button" value="簽到" onclick="actCheckInOrCheckOut(0);" />
	<input type="button" value="簽退" onclick="actCheckInOrCheckOut(1);" />
<?php
	include(WEB_ROOT . '/tpl/footer.php');
?>