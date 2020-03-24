<?php
require_once('_includes.php');

$errorMessage = null;
if (isset($_POST['link'])) {

	$link = new Link();
	$link->title = $_POST['title'];
	$link->link = $_POST['link'];
	$link->status = $_POST['status'];
	$link->last_updated = $_POST['last_updated'];
	
	$link->tags = $_POST['tags'];
	if ($link->save()) {
		header("Location: linkedit.php?id=".$link->id);
	} else {
		$errorMessage = implode("<br />", $link->debugs);
	}
}

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

			<?php
			if ($errorMessage != null) {
				?><div style="">
					Could not save link!!!
					<b>
						<?php echo $errorMessage; ?>
					</b>
				</div><?php
			}
			?>

	 		<! -- SINGLE POST -->
	 		<div class="col-md-8">
				<form role="form" id="frmAddLink" method="POST">
				        <div class="row">
				            <div class="form-group col-lg-12">
				                <label for="code">Link</label>
				                <input type="text" class="form-control input-normal" name="link"  />
				            </div>
				        </div>

				        <div class="row">
				            <div class="form-group col-lg-12">
				                <label for="code">Title</label>
				                <input type="text" class="form-control input-normal" name="title" />
				            </div>
				        </div>
				        <div class="row">
				            <div class="form-group col-lg-12">
				                <label for="code">Tags</label>
				                <input type="text" class="form-control input-normal" name="tags" />
				            </div>
				        </div>
					<button class="btn" id="btnAdd">Add</button>
				</form>
			</div>

			<! -- SIDEBAR -->
			<div class="col-md-4">
				<h4>Extras</h4>
				<div class="hline"></div>

				<br />
				<a class="btn btn-success pull-right" id="btnTest">Test</a>

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
	<?php require_once('_scripts.php'); ?>

	<script>
		$('#btnTest').on('click', function(e,o) {
			$.getJSON('_functions.php?method=testlink&link='+$('input[name="link"]').val(), function(obj) {
				var fontColor = 'gray';

				if (obj.status == 'ok') {
					fontColor = 'green';
				} else {
					fontColor = 'red';
				}
				$('#areaTestResults').html('<div style="color: '+fontColor+'">'+obj.message+'</div>'+
					'<small>'+obj.debugs+'</small>');
			});
		});
	</script>
</body>
</html>
