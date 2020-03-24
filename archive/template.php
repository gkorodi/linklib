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
    <!-- Fixed navbar -->
    <?php require_once('_menu.php'); ?>

    <div id="blue">
      <div class="container">
        <div class="row">
          <h3> </h3>
        </div><!-- /row -->
      </div> <!-- /container -->
    </div><!-- /blue -->

    <div class="container mtb">
      <div class="row">
        <div class="col-lg-8">
          <?php require_once($_REQUEST['logic']);?>
        </div>
        <div class="col-lg-4">
      		<h4>Search</h4>
      		<div class="hline"></div>
      		<p></p>
          <div class="spacing"></div>

      		<h4>Categories</h4>
          <div class="spacing"></div>

          <h4>Special Tags</h4>
      		<div class="hline"></div>
        </div>
      </div><! --/row -->
    </div><! --/container -->
    <?php require_once('_footer.php'); ?>
    <?php require_once('_scripts.php'); ?>

  </body>
</html>
