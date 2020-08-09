<?php
require_once('_includes.php');

$sql="SELECT title, count(*) as counter FROM links GROUP BY title";
$raw = query($sql);

?><!DOCTYPE html>
<html lang="en">
  <head>
	  <?php require_once('_metatags.php');?>
		
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
    <title><?php echo APP_TITLE;?> - Dupes</title>
    
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
				<h3>Dupes <?=count($raw['rows'])?> rows</h3>
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
					
					if ($row[1] < 2) { continue; }
					
					if ($row[1] > 2) { $bgcolor="red"; } else { $bgcolor = "grey"; }
					?>
					<tr style="bg-color: <?=$bgcolor?>">
						<td>
							<a href="dupes_details.php?title=<?=$row[0]?>"><?=$row[0]?><sup><?=$row[1]?></sup></a>
						</td>
					</tr>
					<?php
					$idx++;
				}
				?>
				</table>
				There are <?=$idx?> duplicates
			</div>
		</div>
	</div>

	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
</body>
</html>
