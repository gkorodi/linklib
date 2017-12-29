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
	 		<div id="randomlist" class="col-lg-8">
					<table class="table">
					<?php
					$extra_criteria = (isset($_REQUEST['status'])?' AND status='.$_REQUEST['status']:'');

					if (isset($_REQUEST['notags'])) {
						$extra_criteria .= ' AND tags IS NULL ';
					}
					if (isset($_REQUEST['emptytags'])) {
						$extra_criteria .= " AND tags = '' ";
					}
					$sql='SELECT * FROM links WHERE '.'status != 200 '.$extra_criteria.' LIMIT 100';
					$resultset = query($sql);
					foreach ($resultset['rows'] AS $row) {
						?>
						<tr id="row<?php echo $row[0];?>">

							<td><a href="<?php echo $row[1];?>" target="_newWindow"><?php echo $row[2];?></a><br />
								<small><?php echo $row[1];?></small>
							</td>

							<td>
								<input type="text" name="tags" value="<?php echo $row[5];?>" />
							</td>

							<td>
								<small><?php echo ($row[4]==='0000-00-00 00:00:00'?'N/A':date('Y-m-d', strtotime($row[4])));?></small>
							</td>

							<td>
								<button class="btn btn-sm btn-danger" id="btnDel"
									onClick="deleteLink('<?php echo $row[0];?>');">
									<span class="glyphicon glyphicon-minus"> </span>
								</button>
							</td>

							<td>
								<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_newLinkWin">
									<span class="glyphicon glyphicon-pencil"> </span>
								</a>
							</td>
						</tr>
						<?php
					}
					?>
					</table>
			</div>

	 		<div class="col-lg-4">
		 		
				<h4>Search</h4>
		 		<div class="hline"></div>
		 			<p>
		 				<br/><form action="search.php">
		 				<input type="text" class="form-control" name="q" placeholder="Search something">
					</form>
		 			</p>

		 		<div class="spacing"></div>

		 		<h4>Statuses</h4>
		 		<div class="hline"></div>
				<table class="table">
				<?php

				$resultset = query("SELECT status, count(*) FROM links GROUP BY status");
				foreach($resultset['rows'] AS $row) {
					?>
					<tr onClick="window.location='<?php echo $_SERVER['PHP_SELF'];?>?status=<?php echo ($row[0]==null?'NULL':$row[0]);?>';">
						<th><?php echo ($row[0]==null?'NULL':$row[0]);?></th>
						<td><?php echo $row[1];?></td>
					</tr><?php
				}
				?>
				</table>

		 		<div class="spacing"></div>

				<div id="recent_posts">
				</div>

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
