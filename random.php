<?php
require_once('_includes.php');

$sql="SELECT * FROM links WHERE tags IS NULL";
$resultset = query($sql);

$idlist = Array();
for($idx=0;$idx<(count($resultset['rows'])-1);$idx++) {
  $randomIndex = rand(0,count($resultset['rows'])-1);
  while (in_array($randomIndex, $idlist, TRUE)) {
    $randomIndex = rand(0,count($resultset['rows'])-1);
  }
  array_push($idlist, $randomIndex);
}

?><!DOCTYPE html>
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
				<h3>Random 100 of <span id="totalcount"><?php echo count($raw_rs['rows']);?></span>.</h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->
	<div class="container mtb">
		<div class="row">
			<div id="randomlist" class="col-lg-12">
				<?php require_once("_randomtable.php"); ?>
			</div>
		</div>
	</div>
	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
  <script>
  function checkDetails(linkid) {
    alert(linkid);
  }
  </script>
</body>
</html>
