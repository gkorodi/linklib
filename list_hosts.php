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

        <div class="col-lg-8">

			<?php
			$keys = Array();

			$linklist = query("SELECT link, status, tags FROM links");
			foreach($linklist['rows'] AS $row) {

				$urlarr = explode('/', $row[0]);

				$hostname = $urlarr[2];

				if (isset($hostlist[$hostname])) {
					$hostlist[$hostname]++;
				} else {
					$hostlist[$hostname] = 1;
				}

				if ($row[2] != null && $row[2] != '') {
					if (isset($hosttlist[$hostname])) {
						$hosttlist[$hostname]++;
					} else {
						$hosttlist[$hostname] = 1;
					}
				} else {
					if (isset($hostntlist[$hostname])) {
						$hostntlist[$hostname]++;
					} else {
						$hostntlist[$hostname] = 1;
					}
				}

				$k  = $row[1].'';
				if (!in_array($k,$keys)) {
					array_push($keys,$k);
				}

				if (isset($hostslist[$urlarr[2]])) {
					$a = $hostslist[$urlarr[2]];
					if (isset($a[$k])) {
						$a[$k]++;
					} else {
						$a[$k] = 1;
					}
					$hostslist[$urlarr[2]] = $a;
				} else {
					$hostslist[$urlarr[2]] = Array($k=>1);
				}
			}
			arsort($hostlist);
			?>


		<table class="table">
		<tr>
			<th>Host</th>
			<?php
			foreach($keys AS $key) {
				echo "<td colspan='2'>".$key."</td>";
			}
			?>
			<th>Tags</th>
			<th>NoTags</th>
			<th>Total</th>
		</tr>

		<?php
		foreach($hostlist AS $hname => $hosttotal) {
      if (isset($_REQUEST['errors']) && !isset($hostntlist[$hname])) {
        continue;
      }
			?>
			<tr>
        <th><a href="search_byhost.php?host=<?php echo $hname;?>" target="_newEditLinkWindow"><?php echo $hname;?></a></th>
			<?php
			$statusarr = $hostslist[$hname];

			foreach($keys AS $key) {
				if (isset($statusarr[$key])) {
					echo '<td>'.$statusarr[$key].'</td>';
					echo '<td>'.number_format(($statusarr[$key]/$hosttotal)*100,2).'%'.'</td>';
				} else {
					echo '<td></td><td></td>';
				}
			}
			echo '<td>'.$hosttlist[$hname].'</td>';
			echo '<td>'.$hostntlist[$hname].'</td>';

			echo '<th>'.$hosttotal.'</th>';
			echo '</tr>';
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
