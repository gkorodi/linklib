<?php
require_once('_includes.php');

if (isset($_REQUEST['host'])) {
  $sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".strtoupper($_REQUEST['host'])."%'".
	  	(isset($_REQUEST['untaggedonly'])?" AND (tags IS NULL OR tags = '')":'').
		(isset($_REQUEST['nostatus'])?" AND (status IS NULL OR status = '')":'').
		' ORDER BY updated_at '.(isset($_REQUEST['olderfirst'])?' ASC':' DESC').
				', link, title';
  $resultset = query($sql);
}

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

				<form method="GET" id="frmRefine" class="pull-right">
					
					<input type="checkbox" class="adjusterfield" name="olderfirst" <?=(isset($_REQUEST['olderfirst'])?'checked':'')?>/> OlderFirst<br />
					<input type="checkbox" class="adjusterfield" name="untaggedonly" <?=(isset($_REQUEST['untaggedonly'])?'checked':'')?>/> UntaggedOnly<br />
					<input type="checkbox" class="adjusterfield" name="nostatus" <?=(isset($_REQUEST['nostatus'])?'checked':'')?>/> No Status<br />
					
					<input type="hidden" id="fldHost" name="host" value="<?php echo $_REQUEST['host'];?>" />
				</form>

				<h3>Search Results by host <a href="http://<?php echo $_REQUEST['host'];?>" target="_newWindow"><?php echo $_REQUEST['host'];?></a>
				</h3>
				<small>There are <?php echo count($resultset['rows']); ?> entries</small>
				
	    </div> <!-- /container -->
	</div><!-- /blue -->

	<?php
	
	require_once('__host_links.php')
		
	?>

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
