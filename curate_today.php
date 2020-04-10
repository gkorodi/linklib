<?php
require_once('_includes.php');
$sql="SELECT * FROM links WHERE (tags = '' OR tags IS NULL) AND DATE(updated_at) = CURDATE() ORDER BY updated_at DESC LIMIT 200";
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
				<h3>Curate Today <?=count($raw['rows'])?> rows</h3>
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
							<a href="<?=$row[ROW_LINK]?>" target="_newWindow"><?=urldecode($row[ROW_TITLE])?></a><br />
							<small>Host: <strong><?=justHostName($row[ROW_LINK])?></strong></small><br />
							<small><br />
								<button class="btn btn-warning" onClick="hideLink('<?=$row[ROW_ID]?>');">
									<span class="glyphicon glyphicon-cog"> </span>
								</button>
								
								<a class="btn btn-info" href="linkedit.php?id=<?=$row[ROW_ID]?>" target="_newWin">
									<span class="glyphicon glyphicon-ok"> </span>
								</a>
								
							</small>
							<?php
							if (!empty($row[ROW_CREATED_AT])) {
								?>
								<small>Created: <strong><?=date("Y-m-d", strtotime($row[ROW_CREATED_AT]))?></strong></small>
								<?php
							}
							?>
						</td>
						
						<td>
							<small><?=date("Y-m-d", strtotime($row[ROW_UPDATED_AT]))?></small>
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
