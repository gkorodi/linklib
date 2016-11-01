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

	  <?php require_once('_menu.php'); ?>

	<!-- *****************************************************************************************************************
	 BLUE WRAP
	 ***************************************************************************************************************** -->
	<div id="blue">
	    <div class="container">
			<div class="row">
				<h3>Search Results by host <b><?php echo $_REQUEST['host'];?></b>.</h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->


	<!-- *****************************************************************************************************************
	 BLOG CONTENT
	 ***************************************************************************************************************** -->

	 <div class="container mtb">
	 	<div class="row">

	 		<! -- SINGLE POST -->
	 		<div class="col-md-8">
				<?php
				$query_response = query('SELECT link FROM links WHERE status != 200');

				$hostList = Array();
				foreach($query_response['rows'] AS $row) {
					$lst = split('/',  $row[0]);
					if (isset($lst[2])) {
						$hostList[$lst[2]]++;
					} else {
						$hostList[$lst[2]] = 1;
					}
				}
				?>
				<table class="table">
				<?php
				asort($hostList);
				foreach($hostList AS $link => $count) {
				?>
				<tr>
					<td><a href="searchbyhost.php?host=<?php echo $link;?>"><?php echo $link;?></a></td>
					<td><?php echo $count;?></td>
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
		 				<br/>
		 				<input type="text" class="form-control" placeholder="Search something">
		 			</p>

		 		<div class="spacing"></div>

		 		<h4>Statuses</h4>
		 		<div class="hline"></div>

		 		<div class="spacing"></div>
				<?php require_once('_recent_posts.php');?>

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
