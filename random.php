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

    <!-- Fixed navbar -->
	<?php require_once('_menu.php'); ?>

	<div id="blue">
	    <div class="container">
			<div class="row">
				<h3>Random 100.</h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->


	 <div class="container mtb">
	 	<div class="row">

	 		<! -- BLOG POSTS LIST -->
	 		<div class="col-lg-8">
				<?php
				$sql = 'SELECT count(*) AS rowcount FROM links WHERE status != 200';
				$query_response = query($sql);

				?>There are <?echo $query_response['rows'][0][0];?> rows that are not status 200.<?php
				?>
				<table class="table">
				<?php

				$extra_criteria = (isset($_REQUEST['status'])?'AND status='.$_REQUEST['status']:'');

        if (isset($_REQUEST['notags'])) {
          $extra_criteria .= ' AND tags IS NULL ';
        }
				$sql='SELECT * FROM links WHERE status != 200 '.$extra_criteria.' LIMIT 100';
        $query_response = query($sql);

				foreach ($query_response['rows'] AS $row) {
					?>
					<tr class="stat<?php echo $row[3];?>">
						<td><a href="<?php echo $row[1];?>" target="_newWin"><?php echo $row[2];?></a></td>
						<td><?php echo $row[4];?></td>
						<td><button onclick="window.location='delentry.php?id=<?php echo $row[0];?>';" class="button">Delete</button> </td>
						<td><a href="edit.php?id=<?php echo $row[0];?>"
							target="_editWin">...</a></td>
					</tr>
					<?php
				}
				?>
			</table>
			</div><! --/col-lg-8 -->


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
					?><p><a href="?status=<?php echo $row[0];?>"><i class="fa fa-angle-right"></i> <?php echo $row[0];?></a>
						<span class="badge badge-theme pull-right"><?php echo $row[1];?></span></p><?php
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


	<!-- *****************************************************************************************************************
	 FOOTER
	 ***************************************************************************************************************** -->
	 <div id="footerwrap">
	 	<div class="container">
		 	<div class="row">
		 		<div class="col-lg-4">
		 			<h4>About</h4>
		 			<div class="hline-w"></div>
		 			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
		 		</div>
		 		<div class="col-lg-4">
		 			<h4>Social Links</h4>
		 			<div class="hline-w"></div>
		 			<p>
		 				<a href="#"><i class="fa fa-dribbble"></i></a>
		 				<a href="#"><i class="fa fa-facebook"></i></a>
		 				<a href="#"><i class="fa fa-twitter"></i></a>
		 				<a href="#"><i class="fa fa-instagram"></i></a>
		 				<a href="#"><i class="fa fa-tumblr"></i></a>
		 			</p>
		 		</div>
		 		<div class="col-lg-4">
		 			<h4>Our Bunker</h4>
		 			<div class="hline-w"></div>
		 			<p>
		 				Some Ave, 987,<br/>
		 				23890, New York,<br/>
		 				United States.<br/>
		 			</p>
		 		</div>

		 	</div><! --/row -->
	 	</div><! --/container -->
	 </div><! --/footerwrap -->

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
