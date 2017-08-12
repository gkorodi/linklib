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
					<?php echo (isset($_REQUEST['q'])?'value="'.$_REQUEST['q'].'"':'');?> size="100"/>
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
					foreach($searchresults['rows'] AS $row) {
						?>
						<tr id="row<?php echo $row[0];?>">
							<td>
								<a href="<?php echo $row[1];?>" id="title-<?php echo $row[0];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a><br />
								<small><?php echo $row[1];?></small><br />
								<small id="date-<?php echo $row[0];?>"><?php echo date('Y-m-d', strtotime($row[4]));?></small>
							</td>
							<td>
							<?php
							if ($modified != null && $modified == 'repair') {?>
								<input type="text" id="tags<?php echo $row[0];?>"
									onChange="repairLink(<?php echo $row[0];?>, $(this).val());"
										value="<?php echo $row[5];?>" />

							<?php } else { ?>
								<input type="text" id="tags<?php echo $row[0];?>"
									onChange="tagLink(<?php echo $row[0];?>, $(this).val());"
										value="<?php echo $row[5];?>" />

							<?php } ?>
							</td>
							<td>
								<button class="btn btn-sm btn-danger" onClick="deleteLink(<?php echo $row[0];?>);">
									<span class="glyphicon glyphicon-remove"> </span>
								</button>
							</td>
							<td>
								<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
									<span class="glyphicon glyphicon-ok"> </span>
								</a>
							</td>
							<td>
								<a class="btn btn-sm btn-warning" onclick="repairlink('<?php echo $row[0];?>');">
										<span class="glyphicon glyphicon-check"> </span>
								</a>
							</td>
						</tr>
						<?php
					}
					//foreach($searchresults AS $k=>$v) {
					//	if ($k==='rows') { continue; }
					//	echo '<b>'.$k.'</b>: '.$v.'<br />';
					//}
				}
				?>
			</tbody>
			</table>
			</div>
	 	</div><!--/row -->
	 </div><!--/container -->

	<?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>
	<script>
	
	function repairlink(linkid) {
		$('#title-'+linkid).html('...');

		$.getJSON('_functions.php?method=repairlink&id='+linkid, function(data) {
			console.log(data);
			if (data.status == 'ok') {
				$('#title-'+linkid).html(data.meta.ogtitle);
				var dateVar = Date();

				if (data.meta.ogupdated_time) {
					dateVar = data.meta.ogupdated_time;
				} else if (data.meta.datePublished) {
					dateVar = data.meta.datePublished;
				} else if (data.meta.articlepublished_time) {
					dateVar = data.meta.articlepublished_time;
				} else if (data.meta.btmodDate) {
					dateVar = data.meta.btmodDate;
				}
				$('#date-'+linkid).html(dateVar);

				if (data.meta.news_keywords) {
					$('#tags'+linkid).val(data.meta.news_keywords);
				}

				console.log("Updating last_updated field");
				$.getJSON('_functions.php?method=updatelink&id='+linkid+'&column=last_updated&value='+dateVar,
					function(ndata) {
						console.log(ndata);
						if (ndata.status == 'ok') {
							$('#row'+linkid).css('background-color','lightGreen');
						} else {
							console.log(ndata);
							$('#row'+linkid).css('background-color','red');
						}
					}
				);

				console.log("Updating status.");
				$.getJSON('_functions.php?method=updatelink&id='+linkid+'&column=status&value=200',
					function(ndata) {
						console.log(ndata);
						if (ndata.status == 'ok') {
							$('#row'+linkid).css('background-color','lightGreen');
						} else {
							console.log(ndata);
							$('#row'+linkid).css('background-color','red');
						}
					}
				);

				$.getJSON('_functions.php?method=updatelink&id='+linkid+'&column=title&value='+data.meta.ogtitle, function(ndata) {
					if (ndata.status == 'ok') {
						$('#row'+linkid).css('background-color','lightGreen');
					} else {
						console.log(ndata);
						$('#row'+linkid).css('background-color','red');
					}
				});

			} else {
				$('#row'+linkid).css('background-color','pink');
			}
		});
	}
  </script>
	</body>
</html>
