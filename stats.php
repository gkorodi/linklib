<?php
require_once('_includes.php');
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
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

    <div class="container mtb">
      <div class="row">
        <! -- BLOG POSTS LIST -->

        <div class="col-lg-8">
		<h4>Metrics</h4>
		<table class="table">
		<?php
		$querylist = Array(
			"Total Link Count"=>"SELECT count(*) total_link_count FROM links;",
			"OK Status" => "SELECT count(*) status_ok_count FROM links WHERE status = 200;",
      "Missing Tags <small><a href='query.php?q=".urlencode('SELECT * FROM links WHERE tags IS NULL LIMIT 100')."' target='_newQ'>(tags IS NULL)</a></small>" => "SELECT count(*) tags_missing_count FROM links WHERE tags IS NULL;",
      "Empty Tags" => "SELECT count(*) tags_missing_count FROM links WHERE tags = '';",
      "Missing Status <small><a href='' target=''>(status IS NULL)</a></small>" => "SELECT count(*) status_missing_count FROM links WHERE status IS NULL;",
      "Empty Status <small><a href='query.php?q=".urlencode("SELECT * FROM links WHERE status = '' LIMIT 100")."' target='_newQ'>(status = '')</a></small>" => "SELECT count(*) status_missing_count FROM links WHERE status = '';",
      "Missing Dates <small><a href='query.php?q=".urlencode("SELECT * FROM links WHERE updated_at IS NULL LIMIT 100")."' target='_newQ'>(updated_at IS NULL)</a></small>" => "SELECT count(*) date_missing_count FROM links WHERE updated_at IS NULL;",
      "Empty Dates <small><a href='query.php?q=".urlencode("SELECT * FROM links WHERE updated_at = '' LIMIT 100")."' target='_newQ'>(updated_at = '')</a></small>" => "SELECT count(*) date_missing_count FROM links WHERE updated_at = '';",
	    "Wrong Dates <small><a href='query.php?q=".urlencode("SELECT * FROM links WHERE updated_at < '2000-01-01'")."' target='_mnewQ'>(updated_at < '2000-01-01')</a></small>" => "SELECT count(*) wrong_updated_date FROM links WHERE updated_at < '2000-01-01';"
		);
		$tlc = -1;
		foreach($querylist AS $k=>$query_sql) {
			$resultset = query($query_sql);

			if ($k === "Total Link Count") {
				$tlc = $resultset['rows'][0][0];
			}
			?>
			<tr>
			<th><?php echo $k;?></th>
			<td><?php echo $resultset['rows'][0][0];?></td>
			<td><?php echo ($k!="Total Link Count"?' '.number_format(($resultset['rows'][0][0]/$tlc)*100,2).' %':''); ?></td>
			</tr>
			<?php
		}
		?>
		</table>
        </div>

        <! -- SIDEBAR -->
        <div class="col-lg-4">
		<h4>Search</h4>
		<div class="hline"></div>
		<p>
			<form action="search.php">
			<input type="text" class="form-control" name="q" placeholder="Search something">
			</form>
		</p>
		<div class="spacing"></div>

		<h4>Statuses (other than OK)</h4>
		<div class="hline"></div>
		<table class="table"><?php
		$resultset = query("SELECT status,count(*) FROM links WHERE status != 200 GROUP BY status");
		foreach ($resultset['rows'] AS $row) {
			echo '<tr><th><a href="list_status.php?status='.$row[0].'">'.$row[0].'</a></th><td>'.$row[1].'</td></tr>';
		}
		?></table>

		<div class="spacing"></div>
		<h4>Recent Posts</h4>
		<div class="hline"></div>

		<div class="spacing"></div>

		<h4>Popular Tags</h4>
		<div class="hline"></div>
		<p id="popular_tags"></p>
	</div>
      </div><! --/row -->
    </div><! --/container -->


	<?php require_once('_footer.php'); ?>

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
