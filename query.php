<?php
require_once('_includes.php');
?><!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once('_metatags.php');?>
	<link rel="shortcut icon" href="assets/ico/favicon.ico">

	<title><?=APP_TITLE?></title>

	<!-- Bootstrap core CSS -->
	<link href="assets/css/bootstrap.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="assets/css/style.css" rel="stylesheet">
	<link href="assets/css/font-awesome.min.css" rel="stylesheet">

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
				foreach($searchresults['rows'] AS $row) {
					$link = new Link($row[0]);
						?>
						<tr id="row<?=$link->id?>">
							<td>
								<b>
									<a href="<?php echo $row[1];?>" id="title-<?php echo $row[0];?>" target="_newWindow">
										<?php echo urldecode($row[2]);?></a>
								</b><br />
								<small><?php echo $row[1];?></small><br />
								<small id="date-<?php echo $row[0];?>"><?php echo date('Y-m-d', strtotime($row[4]));?></small>
							<br />
							<?php
							if (isset($modified) && $modified != null && $modified == 'repair') {?>
								<input type="text" id="tags<?php echo $row[0];?>"
									onChange="repairLink(<?php echo $row[0];?>, $(this).val());"
										value="<?php echo $row[5];?>" />
											repairlink:<?php echo $row[0];?>
							<?php } else { ?>
								<input type="text" id="tags<?php echo $row[0];?>"
									onChange="tagLink(<?php echo $row[0];?>, $(this).val());"
										value="<?php echo $row[5];?>" />

							<?php } ?>
							<br /><br />
							<button class="btn btn-sm btn-danger" onClick="deleteLink(<?php echo $row[0];?>);">
								<span class="glyphicon glyphicon-trash"> </span>
							</button>
							
							<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
								<span class="glyphicon glyphicon-ok"> </span>
							</a>
							
							<a class="btn btn-sm btn-warning" onclick="repairlink('<?php echo $row[0];?>');">
									<span class="glyphicon glyphicon-check"> </span>
							</a>
							
							</td>
						</tr>
						<?php
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

		console.log("js.repairlink() Calling `repairlink` back-end process...");
		
		$.getJSON('_functions.php?method=repairlink&id='+linkid, function(data) {
			console.log("js.repairlink() returned values are:");
			console.log(data);

			if (data.status == 'ok') {

				$('#title-'+linkid).html(data.meta.og_title);
				var tagsVar = $('#tags'+linkid).val();
				var dateVar = getDateMetaTag(data.meta);

				$('#date-'+linkid).html(dateVar);

				if (data.meta.news_keywords) {
					tagsVar = data.meta.news_keywords;
				} else if (data.meta.category) {
					tagsVar = data.meta.category;
				}

				console.log("js.repairlink() Updating all fields");
				$.getJSON('_functions.php',
					{
						"method":"updatelink",
						"id": linkid,
						"last_updated": dateVar,
						"tags": tagsVar
					},
					function(ndata) {
						console.log("js.repairlink() Response data:");
						console.log(ndata);
						if (ndata.status == 'ok') {
							$('#row'+linkid).css('background-color','lightGreen');
						} else {
							console.log(ndata);
							$('#row'+linkid).css('background-color','red');
						}
					}
				);

			} else {
				console.log(data);
				$('#row'+linkid).css('background-color','pink');
			}
    })
		.fail(function(data) {
			console.log( "repairQueryLink() error" );
			console.log(data);
		});
	}
  </script>
	</body>
</html>
