<?php
require_once('_includes.php');
?><!DOCTYPE html>
<html lang="en">
  <head>
	  <?php require_once('_metatags.php');?>
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
				<h3>Settings.</h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->

	<div class="container">
		<div class="row">
			<div class="col-lg-7">
        <table class="table">
				<?php
        $configvars = Array('DB_HOST','DB_PORT','DB_USER','DB_PASSWORD', 'DB_NAME');
        foreach ($configvars AS $configvar) {
          ?>
          <tr>
            <th>
              <?php echo $configvar;?>:
            </th>
            <td>
              <input type="text" name="<?php echo $configvar;?>"
              value="<?php (defined($configvar)?eval('$var = '.$configvar.';'):eval('$var = "N/A";')); echo $var;?>" />
            </td>
            <td>
              <button class="btn btn-success">Save</button>
            </td>
          </tr>
          <?php
        }
        ?>
        </table>
			</div>

			<! -- SIDEBAR -->
			<div class="col-lg-5">
        <h4>Sidebar</h4>
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
