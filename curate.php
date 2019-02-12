<?php
require_once('_includes.php');
$sql="SELECT * FROM links WHERE tags = 'curate'";
$raw = query($sql);
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
				<h3>Curate <?php echo count($raw['rows']);?></h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->


	<div class="container mtb">
		<div class="row">
			<div id="randomlist" class="col-lg-12">
				<table class="table">
				<?php
				$idx = 1;
				foreach ($raw['rows'] AS $row) {
					?>
					<tr id="row<?php echo $row[0];?>" >
						<td> </td>
						<td>
							<a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a><br />
							<small><?php echo justHostName($row[1]);?></small><br />
							<small><?php echo $row[4];?></small>
						</td>
						<td>
							<input type="text" id="tags<?php echo $idx;?>"
								value="<?php echo $row[5];?>" onchange="tagLink('<?php echo $idx;?>');" />
							<input type="hidden" id="link<?php echo $idx;?>"
								value="<?php echo $row[1];?>" />
							<input type="hidden" id="title<?php echo $idx;?>"
								value="<?php echo $row[2];?>" />
							<input type="hidden" id="published<?php echo $idx;?>"
								value="<?php echo $row[4];?>" />
						</td>
						<td>
							<button class="btn btn-sm btn-danger" onClick="deleteLink('<?php echo $row[0];?>');">
								<span class="glyphicon glyphicon-remove"> </span>
							</button>
						</td>
						<td>
							<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_newWin">
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
