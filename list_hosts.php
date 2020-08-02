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

    <script src="assets/js/modernizr.js"></script>
  </head>

  <body>
    <!-- Fixed navbar -->
    <?php require_once('_menu.php'); ?>

    <div id="blue">
      <div class="container">
        <div class="row">
          <h3>Host List.</h3>
          <form method="GET"><input type="checkbox" name="errors" 
							onChange="submit();"/> Errors Only?</form>
        </div><!-- /row -->
      </div> <!-- /container -->
    </div><!-- /blue -->

    <div class="container mtb">
      <div class="row">

        <div class="col-lg-8">

					<?php
					
					$linklist = query("SELECT link, status, tags FROM links");
					foreach($linklist['rows'] AS $row) {
						
						$pieces = parse_url($row[0]);
						
						$hostname = isset($pieces['host'])?$pieces['host']:'mission.host';
						
						if ($row[1]+1-1 != 200) {
							if (isset($statusNOTOKlist[$hostname])) {
								$statusNOTOKlist[$hostname]++;
							} else {
								$statusNOTOKlist[$hostname] = 1;
							}
						}
						
						if (empty($row[2])) {
							if (isset($statusTAGLESS[$hostname])) {
								$statusTAGLESS[$hostname]++;
							} else {
								$statusTAGLESS[$hostname] = 1;
							}
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
						<th>Tagless</th>
						<th>NotOK</th>
						<th>Total</th>
					</tr>

					<?php
					foreach($hostlist AS $hname => $hosttotal) {
			      if (isset($_REQUEST['errors']) && !isset($hostntlist[$hname])) {
			        continue;
			      }
						
						if (!isset($statusTAGLESS[$hname])) {
							continue;
						}
						?>
						<tr>
			        <th>
								<a href="search_byhost.php?host=<?=$hname?>" target="_newEditLinkWindow"><?=$hname;?></a>
								&nbsp; <a href="magazine.php?host=<?=$hname?>" target="magazineTab">&#x1F517;</a>
							</th>
							<td>
								<?=isset($statusTAGLESS[$hname])?$statusTAGLESS[$hname]:'n/a'?>
							</td>
							<td>
								<?=isset($statusNOTOKlist[$hname])?$statusNOTOKlist[$hname]:''?>
							</td>
							<td>
								<?=$hosttotal?>
							</td>
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
