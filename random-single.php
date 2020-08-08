<?php
require_once('_includes.php');
$all_links = query('SELECT * FROM links WHERE tags IS NULL LIMIT 10');
$link = $all_links['rows'][rand(0,10)];



?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/ico/favicon.ico">

    <title><?=APP_TITLE?></title>

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

	<div id="blue">
		<div class="container">
			<div class="row">
				<h3><a href="<?=$link[ROW_LINK]?>" target="_newCurateWindow"><b><?=$link[ROW_TITLE]?></b></a></h3>
				<div id="errormessage"></div>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->

	<div class="container mtb">
		<div class="row">
			<div class="col-lg-2">
				
				<button class="btn-block btn-warning" onClick="tagLink(<?=$link[ROW_ID]?>, 'curate')">Curate</button><br />
				<button class="btn-block btn-warning" onClick="tagLink(<?=$link[ROW_ID]?>, 'level1')">Level1</button><br />
				<button class="btn-block btn-warning" onClick="tagLink(<?=$link[ROW_ID]?>, 'level2')">Level2</button><br />
				<button class="btn-block btn-warning" onClick="tagLink(<?=$link[ROW_ID]?>, 'level3')">Level3</button><br />
				<button class="btn-block btn-warning" onClick="tagLink(<?=$link[ROW_ID]?>, 'level4')">Level4</button><br />
				<button class="btn-block btn-warning" onClick="tagLink(<?=$link[ROW_ID]?>, 'level5')">Level5</button><br />
				<br />
				
				
				
				<button class="btn btn-info" onClick="window.location='<?=$_SERVER['PHP_SELF']?>'">Next</button><br />
				<br />
				<button class="btn btn-danger" onClick="deleteLink(<?=$link[ROW_ID]?>);">Delete</button><br />
				
			</div>

			<div class="col-10">
				<pre><?php 
					var_dump($link); 
				?></pre>
			</div>

		</div><! --/row -->
	</div><! --/container -->
	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
  </body>
</html>
