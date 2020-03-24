<?php
require_once('_includes.php');
$sql="SELECT * FROM links  WHERE (tags IS NULL OR tags = '') ORDER BY id ASC LIMIT 200";
$raw = query($sql);

?><!DOCTYPE html>
<html lang="en">
  <head>
	  <?php require_once('_metatags.php');?>
		
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
    <title><?=APP_TITLE?> - Curate List</title>
    
		<!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles for this template  -->
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
				<h3>Curate Oldest</h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->

<div class="container">
		<div class="row">
			
			
			<div id="randomlist" class="col-lg-12">
				<table class="table">
				<?php
				$idx = 0;
				foreach ($raw['rows'] AS $row) {
					$idx++;
					?>
					<tr id="row<?=$row[ROW_ID]?>" >
						<td><button class="btn btn-danger" onClick="deleteLink('<?=$row[ROW_ID]?>');">
									<span class="glyphicon glyphicon-remove"> </span>
								</button>
						</td>
						<td>
							<button class="btn btn-warning" onClick="tagLink(<?=$row[ROW_ID]?>, 'later');">
									<span class="glyphicon glyphicon-cog"> </span>
								</button>
						</td>
						<td>
							<button class="btn btn-info" onClick="tagLink(<?=$row[ROW_ID]?>, 'later2');">
									<span class="glyphicon glyphicon-cog"> </span>
								</button>
						</td>
						<td>
							<button class="btn btn-success" onClick="tagLink(<?=$row[ROW_ID]?>, 'later3');">
									<span class="glyphicon glyphicon-cog"> </span>
								</button>
						</td>
						<td>
							<strong>
								<a href="<?=$row[ROW_LINK]?>" target="_newWindow"><?=urldecode($row[ROW_TITLE])?></a><br />
								<small><?=justHostName($row[ROW_LINK])?></small>
							</strong>
						</td>
					</tr>
					<!--
						
						<tr id="row<?=$row[ROW_ID]?>" >
							<td>
								<p>
									<a href="<?=$row[ROW_LINK]?>" target="_newWindow" class="h3"><?=urldecode($row[ROW_TITLE])?></a><br />
									<small><?=justHostName($row[ROW_LINK])?></small>
								</p>
								<br />
							
								<button class="btn-lg btn-warning" onClick="tagLink(<?=$row[ROW_ID]?>, 'later');">
									<span class="glyphicon glyphicon-cog"> </span>
								</button>
							
								<span style="padding: 5%"> </span>
							
								<button class="btn-lg btn-info" onClick="tagLink(<?=$row[ROW_ID]?>, 'later2');">
									<span class="glyphicon glyphicon-cog"> </span>
								</button>
							
								<span style="padding: 5%"> </span>
							
								<button class="btn-lg btn-success" onClick="tagLink(<?=$row[ROW_ID]?>, 'later3');">
									<span class="glyphicon glyphicon-cog"> </span>
								</button>
							
								<span style="padding: 10%"> </span>
							
								<button class="btn-lg btn-danger right" onClick="deleteLink('<?=$row[ROW_ID]?>');">
									<span class="glyphicon glyphicon-remove"> </span>
								</button>

								<br />
							</td>

						</tr>
						
						-->
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
