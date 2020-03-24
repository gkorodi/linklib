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
				if (isset($_REQUEST['q'])) {
					$searchresults = query($_REQUEST['q']);
					if (count($searchresults['rows']) > 300) {
						$searchresults['rows'] = array_slice($searchresults['rows'],0,300);
					}
					foreach($searchresults['rows'] AS $row) {
						$link = new Link($row[0]);
						?>
						<tr id="row<?=$link->id?>">
							<td>
								<button class="btn btn-sm btn-danger" onClick="deleteLink(<?=$link->id?>);">
									<span class="glyphicon glyphicon-trash"> </span>
								</button>
							</td>
							<td>
								<b>
									<a href="<?=$link->link?>" id="title-<?=$link->id?>" target="_newWindow">
										<?=urldecode($link->title)?></a>
								</b><br />
								<p><?=json_decode($link->description)->description?></p>
								<small><?=$link->link?></small><br />
								<small id="cdate-<?=$link->id?>">Created: <b><?=date('Y-m-d', strtotime($link->created_at))?></b></small>
								<small id="date-<?=$link->id?>">Updated: <b><?=date('Y-m-d', strtotime($link->updated_at))?></b></small>
								<small id="status-<?=$link->id?>">Status: <b><?=$link->status?></b></small>
								<div id="description-<?=$link->id?>"></div>
							</td>
							<td>
								<input type="text" id="tags-<?=$link->id?>"
									onChange="tagLink(<?=$link->id?>, $(this).val());"
										value="<?=$link->tags?>" />
							</td>
							<td>
								<a class="btn btn-sm btn-info" href="linkedit.php?id=<?=$link->id?>" target="_winEditLink">
									<span class="glyphicon glyphicon-ok"> </span>
								</a>
							</td>
							<td>
								<a class="btn btn-sm btn-warning" onClick="repairQueryLink('<?=$link->id?>');">
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
	function repairQueryLink(linkid) {
		console.log('repairQueryLink() starting '+linkid);
		
		$('#title-'+linkid).html('...');
		
		$.getJSON( '_functions.php?method=repairlink&id='+linkid, {
		    format: "json"
		  })
    .done(function( data ) {
			if (data.status == 'ok') {
				console.log(data);
		
				$('#title-'+linkid).html(data.details.title);
				$('#tags'+linkid).val(data.details.tags);
				$('#date-'+linkid).html(data.details.updated_at);
				$('#status-'+linkid).val(data.details.status);
		
				$('#description-'+linkid).html('<b>'+data.meta['og:description']+'</b>');
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
