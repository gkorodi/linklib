<?php
require_once('_includes.php');
?><!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once('_metatags.php');?>
	<link rel="shortcut icon" href="assets/ico/favicon.ico">
	<title><?php echo APP_TITLE;?></title>
	<link href="assets/css/bootstrap.css" rel="stylesheet">
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
				<h3>Status <?php echo $_REQUEST['status'];?></h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->


	 <div class="container mtb">
	 	<div class="row">
	 		<div id="randomlist" class="col-lg-4">
					
			</div>

	 		<div class="col-lg-8">
				
		 		<h4>Statuses</h4>
		 		<div class="hline"></div>
				<table class="table">
				<?php
				$resultset = query("SELECT status, count(*) FROM links GROUP BY status");
				foreach($resultset['rows'] AS $row) {
					?>
					<tr onClick="window.location='list_status.php?status=<?=(is_null($row[0])?'NULL':$row[0])?>';">
						<th><?=(is_null($row[0])?'NULL':$row[0])?></th>
						<td><?=$row[1]?></td>
					</tr><?php
				}
				?>
				</table>

		 		<div class="spacing"></div>

		 		<h4>Popular Tags</h4>
		 		<div class="hline"></div>
		 		<p id="popular_tags"></p>

	 		</div>
	 	</div><! --/row -->
	 </div><! --/container -->

	 <?php require_once('_footer.php'); ?>


	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>
	<script>
	$(document).ready(function(obj) {
		$('.btn.btn-danger').on('click', function(btnobj) {
			var linkid = $(this).parent().parent().attr('id');

			$.getJSON('_functions.php?method=deletelink&id='+linkid, function( data ) {
				if (data.status == 'ok') {
					console.log(data.info.redirect_count);
				} else {
					console.log(data.message);
				}
			});
		});
	});

	function test(linkid) {
		$.getJSON('_functions.php?method=testurl&id='+linkid, function(data) {
			if (data.status == 'ok') {
				$('#'+linkid).css('background-color','lightGreen');
			} else {
				$('#'+linkid).css('background-color','pink');
			}
		});
	}

	function repair(linkid) {
		$('#myModalLabel').html("Link: "+linkid);
		$('#fldModalBody').html("Loading ....");
		$('#myModal').modal({});

		var repairURL = '_functions.php?method=repair&id='+linkid;
		$.getJSON(repairURL, function(data) {
			if (data.status == 'ok') {
				$('#fldModalBody').html(data.body);
			} else {
				$('#fldModalBody').html("ERROR: ".data.message);
			}
		});
	}

	function deleteLink(linkid) {
		$.getJSON('_functions.php?method=deletelink&id='+linkid, function(data) {
			$('#row'+linkid).css('background-color','orange');
			if (data.status == 'ok') {
				$('#row'+linkid).hide();
			} else {
				$('#row'+linkid).css('background-color','red').css('color','white').html(data.message);
			}
		});
	}
	</script>

  </body>
</html>
