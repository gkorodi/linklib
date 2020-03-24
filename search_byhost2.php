<?php
require_once('_includes.php');

$sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".strtoupper($_REQUEST['host'])."%'".
        " AND (tags IS NULL OR tags = '')".
        ' ORDER BY updated_at ASC'.
        ', link, title';
$resultset = query($sql);


?><!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once('_metatags.php');?>
	<link rel="shortcut icon" href="assets/ico/favicon.ico">
	<title><?php echo APP_TITLE;?></title>
	<link href="assets/css/bootstrap.css" rel="stylesheet">
	<link href="assets/css/style.css" rel="stylesheet">
	<script src="assets/js/modernizr.js"></script>
</head>
<body>
	<?php require_once('_menu.php'); ?>

	<div id="blue">
	    <div class="container">
				<small><a href="search_byhost.php">Regular</a></small>
				<h3>Search Results by host <a href="http://<?=$_REQUEST['host']?>" target="_newWindow"><?=$_REQUEST['host']?></a>
				</h3>
				<small>There are <?=count($resultset['rows'])?> entries</small>
				
	    </div> <!-- /container -->
	</div><!-- /blue -->

	<?php require_once('__host_links3.php'); ?>

	 <?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>

	<script src="assets/js/jquery.toastmessage.js" type="text/javascript"></script>
	<link href="assets/css/jquery.toastmessage.css" rel="stylesheet" type="text/css" />

	<script>
	$(document).ready(function(){
		$('.adjusterfield').on('change', function() {
			$('#frmRefine').submit();
		});
	});
	</script>

  </body>
</html>
