<?php
require_once('_includes.php');

$sql = 'SELECT * FROM links AS r1 JOIN (SELECT CEIL(RAND() '.
	'* (SELECT MAX(id) FROM links WHERE tags IS NULL)) AS id) AS r2 '.
		'WHERE r1.id >= r2.id AND tags IS NULL ORDER BY r1.id ASC LIMIT '.(isset($_REQUEST['limit'])?$_REQUEST['limit']:'50');
$resultset = query($sql);

?><!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once('_metatags.php');?>
	<link rel="shortcut icon" href="assets/ico/favicon.ico">
	<title><?php echo APP_TITLE;?> - Random Links to Curate</title>
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
				<h3>Random <?=count($resultset['rows'])?> of <span id="totalcount"><?=count($raw_rs['rows'])?></span>.</h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->
	<div class="container mtb">
		<div class="row">
			<div id="randomlist" class="col-lg-12">
				<?php
				require_once("_randomtable3.php"); 
				?>
				<a href="#" onClick="document.location.reload(true);" class="btn btn-info">Reload</a>
			</div>
		</div>
	</div>
	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
</body>
</html>
