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
				<h3>Adding new link.</h3>
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
				<form role="form" id="frmEdit" method="POST">
					        <div class="row">
					            <div class="form-group col-lg-12">
					                <label for="code">Link</label>
					                <input type="text" class="form-control input-normal" name="fldLink" value="<?php echo $link[1];?>" />
					            </div>
					        </div>
					
					        <div class="row">
					            <div class="form-group col-lg-12">
					                <label for="code">Title</label>
					                <input type="text" class="form-control input-normal" name="fldTitle" value="<?php echo $link[2];?>" />
					            </div>
					        </div>
					
					        <div class="row">
					            <div class="form-group col-lg-2">
					                <label for="code">Status</label>
					                <input type="text" class="form-control input-normal" name="fldStatus" value="<?php echo $link[3];?>" />
					            </div>
					        </div>
					
					        <div class="row">
					            <div class="form-group col-lg-4">
					                <label for="code">Update Date</label>
					                <input type="text" class="form-control input-normal" name="fldUpdateDate" value="<?php echo $link[4];?>" />
					            </div>
					        </div>
					
					        <div class="row">
					            <div class="form-group col-lg-12">
					                <label for="code">Tabs</label>
					                <input type="text" class="form-control input-normal" name="fldTabs" value="<?php echo $link[5];?>" />
					            </div>
					        </div>
					
					
					<button class="btn" id="btnAdd">Add</button>
					</form>
			</div>

			<! -- SIDEBAR -->
			<div class="col-lg-4">
				<h4>Extras</h4>
				<div class="hline"></div>
				<br />
				<a class="btn btn-success" href="<?=$link[1]?>" target="_newWindow">...</a>
				<br />
				<a class="btn btn-success pull-right" href="random-single.php">Test</a>
		 			
		 		<div class="spacing"></div>
		 		<h4>Analysis</h4>
		 		<div class="hline"></div>
		 		<div id="areaTestResults">
				</div>
		 		<div class="spacing"></div>
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
    
    <script>
    $('btnTest').on('click', function(e,o) {
    	    $('#areaTestResults').load('_functions.php?method=testlink&link=cnn.com');
    });
    </script>
  </body>
</html>
