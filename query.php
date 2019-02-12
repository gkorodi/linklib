<?php
require_once('_includes.php');

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
	<link href="assets/css/font-awesome.min.css" rel="stylesheet">


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

	<!-- *****************************************************************************************************************
	 BLUE WRAP
	 ***************************************************************************************************************** -->
	<div id="blue">
	    <div class="container">
			<div class="row">
				<form method="GET"><input type="text" name="q"
					<?=(isset($_REQUEST['q'])?'value="'.$_REQUEST['q'].'"':'')?> size="100"/>
				</form>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container">
	 	<div class="row">
			<div class="col-12">
			<table class="table">
			<tbody>
				<?php
				if (isset($_REQUEST['q'])) {
					$searchresults = query($_REQUEST['q']);
					if (count($searchresults['rows']) > 300) {
						$searchresults['rows'] = array_slice($searchresults['rows'],0,300);
					}
					foreach($searchresults['rows'] AS $row) {
						?>
						<tr id="row<?php echo $row[0];?>">
							<td>
								<b>
									<a href="<?=$row[1]?>" id="title-<?=$row[0]?>" target="_newWindow">
										<?=urldecode($row[2])?></a>
								</b><br />
								<small><?=$row[1]?></small><br />
								<small id="date-<?=$row[0]?>"><?=date('Y-m-d', strtotime($row[4]))?></small>
								
								<input type="text" id="status-<?=$row[0]?>" value="<?=$row[3]?>" /><br />

								<div id="description-<?=$row[0]?>"></div>
								
							</td>
							<td>
								<input type="text" id="tags-<?=$row[0]?>"
									onChange="tagLink(<?=$row[0]?>, $(this).val());"
										value="<?=$row[5]?>" />
							</td>
							<td>
								<button class="btn btn-sm btn-danger" onClick="deleteLink(<?=$row[0]?>);">
									<span class="glyphicon glyphicon-trash"> </span>
								</button>
							</td>
							<td>
								<a class="btn btn-sm btn-info" href="linkedit.php?id=<?=$row[0]?>" target="_winEditLink">
									<span class="glyphicon glyphicon-ok"> </span>
								</a>
							</td>
							<td>
								<a class="btn btn-sm btn-warning" onclick="repairlink('<?=$row[0]?>');">
										<span class="glyphicon glyphicon-check"> </span>
								</a>
							</td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
			</table>
			</div>
	 	</div><!--/row -->
	 </div><!--/container -->

	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
	<script>
	function repairlink(linkid) {
		$('#title-'+linkid).html('...');
		$.getJSON('_functions.php?method=repairlink&id='+linkid, function(data) {
			if (data.status == 'ok') {
				console.log(data);
				
				$('#title-'+linkid).html(data.details.title);
				$('#tags'+linkid).val(data.details.tags);
				$('#date-'+linkid).html(data.details.last_updated);
				$('#status-'+linkid).val(data.details.status);
				
				$('#description-'+linkid).html('<b>'+data.meta['og:description']+'</b>');
			} else {
				$('#row'+linkid).css('background-color','pink');
			}
		});
	}
  </script>
	</body>
</html>
