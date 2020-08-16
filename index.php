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
	  <?php require_once('templates/parts/menu.html'); ?>

	<!-- *****************************************************************************************************************
	 HEADERWRAP
	 ***************************************************************************************************************** -->
	<div id="headerwrap">
	    <div class="container">
			<div class="row">
				<div class="col-lg-8 col-lg-offset-2">
					<h3>Show your work with this beautiful theme</h3>
					<h1>Eyecatching Bootstrap 3 Theme.</h1>
					<h5>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</h5>
					<h5>More Lorem Ipsum added here too.</h5>
				</div>
				<div class="col-lg-8 col-lg-offset-2 himg">
					<img src="assets/img/browser.png" class="img-responsive">
				</div>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /headerwrap -->

	<!-- *****************************************************************************************************************
	 SERVICE LOGOS
	 ***************************************************************************************************************** -->
	 <div id="service">
	 	<div class="container">
 			<div class="row centered">
 				<div class="col-md-4">
 					<i class="fa fa-heart-o"></i>
 					<h4>Handsomely Crafted</h4>
 					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
 					
 				</div>
 				<div class="col-md-4">
 					<i class="fa fa-flask"></i>
 					<h4>Retina Ready</h4>
 					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
 					
 				</div>
 				<div class="col-md-4">
 					<i class="fa fa-trophy"></i>
 					<h4>Quality Theme</h4>
 					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
 					
 				</div>
	 		</div>
	 	</div><! --/container -->
	 </div><! --/service -->

	<?php require_once('templates/parts/footer.html'); ?>
	<?php require_once('templates/parts/scripts.js'); ?>


  </body>
</html>
