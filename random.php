<?php
require_once('_includes.php');
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
				<h3>Random 100.</h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->


	 <div class="container mtb">
	 	<div class="row">

	 		<! -- BLOG POSTS LIST -->
	 		<div id="randomlist" class="col-lg-8">
				<h4>Random 100 List</h4>
				<table class="table">
				<?php
				
				$extra_criteria = (isset($_REQUEST['status'])?'AND status='.$_REQUEST['status']:'');
				if (isset($_REQUEST['notags'])) {
					$extra_criteria .= ' AND tags IS NULL ';
				}
				$sql='SELECT * FROM links WHERE '.'status != 200 '.$extra_criteria.' LIMIT 100';
		
				$resultset = query($sql);
				foreach ($resultset['rows'] AS $row) {
					?>
					<tr>
						<td><a href="<?php echo $row[1];?>"><?php echo $row[2];?></a><br />
							<small><?php echo $row[5];?></small></td>
						<td><button class="btn btn-danger">Del</button></td>
						<td><a class="btn btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_newLinkWin">Edit</a></td>
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
				<div id="status_list" >
				</div>
				
		 		<div class="spacing"></div>

				<div id="recent_posts">
				</div>

		 		<div class="spacing"></div>

		 		<h4>Popular Tags</h4>
		 		<div class="hline"></div>
		 		<p id="popular_tags"></p>
	 		</div>
	 	</div><! --/row -->
	 </div><! --/container -->
	 
	 <?php require_once('_footer.php'); ?>


	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>
	

  </body>
</html>
