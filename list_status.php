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
				<h3>Status <?=(!isset($_REQUEST['status']) || empty($_REQUEST['status'])?' != 200 ':$_REQUEST['status'])?></h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->


	 <div class="container mtb">
	 	<div class="row">
	 		<div id="randomlist" class="col">
					<table class="table">
					<?php
					$extra_criteria = (isset($_REQUEST['status'])?' AND status='.$_REQUEST['status']:'');

					if (isset($_REQUEST['notags'])) {
						$extra_criteria .= ' AND tags IS NULL ';
					}
					if (isset($_REQUEST['emptytags'])) {
						$extra_criteria .= " AND tags = '' ";
					}
					if (isset($_REQUEST['oldestfirst'])) {
						$extra_criteria .= " ORDER BY updated_at ASC ";
					}
					$sql='SELECT * FROM links WHERE '.'status != 200 '.$extra_criteria.' LIMIT 100';
					$resultset = query($sql);
					foreach ($resultset['rows'] AS $row) {
						
						$bgColor = 'white';
						if ($row[ROW_STATUS]!=200) {
							$bgColor = '#f0f0f0';
						}
						?>
						<tr id="row<?=$row[ROW_ID]?>" style="background-color: <?=$bgColor?>">

							<td>
								<button class="btn btn-danger" id="btnDel"
									onClick="deleteLink('<?=$row[ROW_ID]?>');">
									<span class="glyphicon glyphicon-trash"> </span>
								</button>
								
								<button class="btn btn-warning" id="btnWarn"
									onClick="tagLink(<?=$row[ROW_ID]?>, 'later');">
									<span class="glyphicon glyphicon-cog"> </span>
								</button>
								
								<button class="btn btn-info" id="btnWarn"
									onClick="tagLink(<?=$row[ROW_ID]?>, 'later1');">
									<span class="glyphicon glyphicon-cog"> </span>
								</button>
							</td>

							<td id="rowDetails<?=$row[ROW_ID]?>">
								<a href="<?=$row[ROW_LINK]?>" target="_newWindow"><b><?=$row[ROW_TITLE]?></b></a><br />
								<small><?=$row[ROW_LINK]?><br />
									Status: <b><?=(!isset($row[ROW_STATUS])?'n/a':$row[ROW_STATUS])?></b> 
									Tags: <b><?=(!isset($row[ROW_TAGS]) || empty($row[ROW_TAGS])?'EMPTY':$row[ROW_TAGS])?></b> 
									Created: <b><?=(!isset($row[ROW_CREATED_AT]) || empty($row[ROW_CREATED_AT])?'n/a':date('Y-m-d', strtotime($row[ROW_CREATED_AT])))?></b> 
									Updated: <b><?=(!isset($row[ROW_UPDATED_AT]) || empty($row[ROW_UPDATED_AT])?'n/a':date('Y-m-d', strtotime($row[ROW_UPDATED_AT])))?></b> <sub><?=$row[ROW_UPDATED_AT]?></sub>
								</small>
							</td>

							<td>
								<a class="btn btn-info" href="linkedit.php?id=<?=$row[ROW_ID]?>" target="_newLinkWin">
									<span class="glyphicon glyphicon-pencil"> </span>
								</a>
							</td>
						</tr>
						<?php
					}
					?>
					</table>
			</div>
	 	</div><! --/row -->
	 </div><! --/container -->

	 <?php require_once('_footer.php'); ?>


	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>
	<script>

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

	function warnLink(linkid) {
		$('#row'+linkid).css('background-color','orange');
		$('#rowDetails'+linkid).html('');
		
		$.getJSON('_functions.php?method=warnlink&id='+linkid, function(data) {
			console.log(data);
			
			if (data.status == 'ok') {
				$('#rowDetails'+linkid).html(data.content);
			} else {
				$('#rowDetails'+linkid).css('background-color','red').css('color','white').html(data.message);
			}
		});
	}

	</script>

  </body>
</html>
