<?php
require_once('_includes.php');
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

    <!-- Fixed navbar -->
	<?php require_once('_menu.php'); ?>

	<!-- *****************************************************************************************************************
	 BLUE WRAP
	 ***************************************************************************************************************** -->
	<div id="blue">
	    <div class="container">
			<div class="row">
				<h3>STATS.</h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->


	<!-- *****************************************************************************************************************
	 BLOG CONTENT
	 ***************************************************************************************************************** -->

	 <div class="container mtb">
	 	<div class="row">

	 		<! -- BLOG POSTS LIST -->
	 		<div class="col-lg-8">

			<table class="table">
				<?php
				$categories['empty'] = 0;
				$categories['NULL'] = 0;

				$r = query("SELECT tags FROM links GROUP BY tags");
				foreach ($r['rows'] AS $row) {
					if ($row[0]===null) { $categories['NULL']++; continue;}
					if ($row[0]==='') { $categories['empty']++; continue;}
					$cats = explode(',', $row[0]);
					foreach($cats AS $category) {
						$c = trim($category);

						if (isset($categories[$c])) {
							$categories[$c]++;
						} else {
							$categories[$c] = 1;
						}
					}
				}
				arsort($categories);
				$idx = 1;
				foreach($categories AS $category => $counter) {
					?><tr><td><?=$category?></td><td><?=$counter?></td></tr><?php
					if ($idx > 10) { break; }
					$idx++;
				}
				?>
			</table>

			<h4>Category Combinations</h4>

			<table class="table">
				<?php
				$r = query("SELECT tags, count(*) AS counter FROM links GROUP BY tags ORDER BY counter DESC LIMIT 20");
				foreach ($r['rows'] AS $row) {
					if ($row[0] === null || $row[0] === '') { continue; }
					?><tr><td><?=($row[0]===null?'NULL':($row[0]===''?'empty':$row[0]))?></td><td><?=$row[1]?></td></tr><?php
				}
				?>
			</table>
			</div>

	 		<! -- SIDEBAR -->
	 		<div class="col-lg-4">
		 		<h4>Search</h4>
		 		<div class="hline"></div>
		 			<p>
		 				<br/><form action="search.php">
		 				<input type="text" class="form-control" name="q" placeholder="Search something">
					</form>
		 			</p>

		 		<div class="spacing"></div>

		 		<h4>Statuses</h4>
		 		<div class="hline"></div>
				<?php
				$query_response = query('SELECT status, count(*) FROM links WHERE status != 200 GROUP BY status ORDER BY count(*) DESC');
				foreach($query_response['rows'] AS $row) {
					?><p><a href="?status=<?=$row[0]?>"><i class="fa fa-angle-right"></i> <?=$row[0]?></a>
						<span class="badge badge-theme pull-right"><?=$row[1]?></span></p><?php
				}
				?>

		 		<div class="spacing"></div>

				<?php require_once('_recent_posts.php');?>

		 		<div class="spacing"></div>

		 		<h4>Popular Tags</h4>
		 		<div class="hline"></div>
		 			<p>
		            	<a class="btn btn-theme" href="#" role="button">Design</a>
		            	<a class="btn btn-theme" href="#" role="button">Wordpress</a>
		            	<a class="btn btn-theme" href="#" role="button">Flat</a>
		            	<a class="btn btn-theme" href="#" role="button">Modern</a>
		            	<a class="btn btn-theme" href="#" role="button">Wallpaper</a>
		            	<a class="btn btn-theme" href="#" role="button">HTML5</a>
		            	<a class="btn btn-theme" href="#" role="button">Pre-processor</a>
		            	<a class="btn btn-theme" href="#" role="button">Developer</a>
		            	<a class="btn btn-theme" href="#" role="button">Windows</a>
		            	<a class="btn btn-theme" href="#" role="button">Phothosop</a>
		            	<a class="btn btn-theme" href="#" role="button">UX</a>
		            	<a class="btn btn-theme" href="#" role="button">Interface</a>
		            	<a class="btn btn-theme" href="#" role="button">UI</a>
		            	<a class="btn btn-theme" href="#" role="button">Blog</a>
		 			</p>
	 		</div>
	 	</div><! --/row -->
	 </div><! --/container -->


<?php require_once('_footer.php'); ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/retina-1.1.0.js"></script>
	<script src="assets/js/jquery.hoverdir.js"></script>
	<script src="assets/js/jquery.hoverex.min.js"></script>
	<script src="assets/js/jquery.prettyPhoto.js"></script>
  	<script src="assets/js/jquery.isotope.min.js"></script>
  	<script src="assets/js/custom.js"></script>


  </body>
</html>
