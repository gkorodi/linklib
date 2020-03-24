<!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once('_metatags.php');?>
	<link rel="shortcut icon" href="assets/ico/favicon.ico">
	<title><?php echo APP_TITLE;?></title>
	<!-- Bootstrap core CSS -->
	<link href="assets/css/bootstrap.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="assets/css/style.css" rel="stylesheet">
	<!-- Just for debugging purposes. Don't actually copy this line! -->
	<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

	<script src="assets/js/modernizr.js"></script>
</head>
<body>
	<?php require_once('_menu.php'); ?>

	<div id="blue">
		<div class="container">
			<div class="row">
				<h3>Curate Files <sup><?php echo $objectCount;?></sup></h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->


	<div class="container mtb">
		<div class="row">
			<div id="randomlist" class="col-lg-12">
				<table class="table">
				<?php
				foreach (glob('Feeds/*.feed') AS $filename) {
					$raw = file_get_contents($filename);
					?>
					<tr>
						<td><?php echo $raw;?></td>
					</tr>
					<?php
				}
				?>
				</table>
			</div>
		</div>
	</div>

	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
	<script>
		function delfile(rowIdx) {
			var dfilename = $('#file'+rowIdx).val();
			$('#row'+rowIdx).css('background-color','orange');

			$.ajax({
				type: 'GET',
				url: '_functions.php',
				dataType: 'json',
				data: {
					"method":"delfile",
					"filename":dfilename
				},
				success: function(respObj) {
					console.log(respObj);

					if (respObj.status == 'ok') {
						console.log(dfilename+" has been deleted");
						$('#row'+rowIdx).hide();
					} else {
						$('#row'+rowIdx).css('background-color','red');
					}
				}
			});
		}
		function savefile(rowIdx) {
			var dfilename = $('#file'+rowIdx).val();
			$('#row'+rowIdx).css('background-color','orange');

			$.ajax({
				type: 'GET',
				url: '_functions.php',
				dataType: 'json',
				data: {
					"method":"savefile",
					"filename":dfilename
				},
				success: function(respObj) {
					console.log(respObj);

					if (respObj.status == 'ok') {
						console.log(dfilename+" has been imported");
						$('#row'+rowIdx).hide();
						delfile(rowIdx);
					} else {
						$('#row'+rowIdx).css('background-color','red');
					}
				}
			});
		}
	</script>
</body>
</html>
