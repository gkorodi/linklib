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
          <h3>Host List.</h3>
          <form method="GET"><input type="checkbox" name="errors" onChange="submit();"/> Errors Only?</form>
        </div><!-- /row -->
      </div> <!-- /container -->
    </div><!-- /blue -->

    <div class="container mtb">
      <div class="row">

        <div class="col-lg-8 col-md-6">

			<?php
			$keys = Array();
			$linklist = query("SELECT link, status, tags FROM links");
			foreach($linklist['rows'] AS $row) {

				$urlarr = explode('/', $row[0]);
				if (substr($row[0],0,1) == '/') {
					$hostname = $urlarr[1];
				} else {
					$hostname = $urlarr[2];
				}
				
				if (isset($hostlist[$hostname])) {
					$hostlist[$hostname]++;
				} else {
					$hostlist[$hostname] = 1;
				}
			}
			arsort($hostlist);
			?>
			<table class="table">
			<tr>
				<th>Host</th>
				<th>Total</th>
			</tr>

			<?php
			foreach($hostlist AS $hname => $hosttotal) {
	      if (isset($_REQUEST['errors']) && !isset($hostntlist[$hname])) {
	        continue;
	      }
				?>
				<tr>
	        <th>
						<a href="search_byhost.php?host=<?php echo $hname;?>" target="_newEditLinkWindow"><?php echo $hname;?></a>
					</th>
					<td>
						<?php echo $hosttotal;?>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
		</div>

		<! -- SIDEBAR -->
		<div class="col-lg-4 col-md-2">
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

			<div class="spacing"></div>
			<div id="recent_posts" ></div>
			<div class="spacing"></div>

			<h4>Popular Tags</h4>
			<div class="hline"></div>
			<p id="popular_tags">
			</p>
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
