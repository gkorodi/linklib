<?php
require_once('_includes.php');
$sql="SELECT 
	* 
FROM 
	links
WHERE 
	(tags = 'later')
ORDER BY 
	id DESC 
LIMIT 200";
$raw = query($sql);

?><!DOCTYPE html>
<html lang="en">
  <head>
	  <?php require_once('_metatags.php');?>
		
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
    <title><?=APP_TITLE?> - Curate List</title>
    
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
				<h3>Links with <i>later</i> tag <sup><?=count($raw['rows'])?></sup></h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->


	<div class="container mtb">
		<div class="row">
			<div id="randomlist" class="col-lg-12">
				<table class="table">
				<?php
				$idx = 0;
				foreach ($raw['rows'] AS $row) {
					$idx++;
					?>
					<tr id="row<?=$row[ROW_ID]?>" >
						<td>
							<button class="btn btn-sm btn-danger" onClick="deleteLink('<?=$row[ROW_ID]?>');">
								<span class="glyphicon glyphicon-remove"> </span>
							</button>
						</td>
						<td>
						</td>
						
						<td>
							<a href="<?=$row[ROW_LINK]?>" target="_newWindow" class="h6"><?=urldecode($row[ROW_TITLE])?></a><br />
							<small><?=justHostName($row[ROW_LINK])?></strong></small>
						</td>
						<td>
							<?=date("Y-m-d", strtotime($row[ROW_CREATED_AT]))?>
						</td>
						
						<td>
							<?=date("Y-m-d", strtotime($row[ROW_UPDATED_AT]))?>
						</td>
						
						<td>
							<button class="btn btn-sm btn-warning" onClick="hideLink('<?=$row[ROW_ID]?>');">
								<span class="glyphicon glyphicon-cog"> </span>
							</button>
						</td>
						
						<td>
							<a class="btn btn-sm btn-info" href="linkedit.php?id=<?=$row[ROW_ID]?>" target="_newWin">
								<span class="glyphicon glyphicon-ok"> </span>
							</a>
						</td>
					</tr>
					<?php
				}
				?>
				</table>
			</div>
		</div>
	</div>

	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
</body>
</html>
