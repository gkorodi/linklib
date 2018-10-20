<?php
require_once('_includes.php');

function getLinkTitle($linkURL) {
	$sql="SELECT title FROM tobecurated WHERE link LIKE '".$linkURL."%'";
	$query_response = query($sql);
	if (count($query_response['rows'])<1) {
		return 'No title for '.$linkURL;
	} else {
		return $query_response['rows'][0][0];
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

    <title><?php echo APP_TITLE;?> - Curation</title>

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

	    <style>
	    TR.stat500 {
		    background-color: red;
		    color: white;
	    }
	    TR.stat200 {
		    background-color: lightGreen;
	    }
	    </style>
  </head>

  <body>

	  <?php require_once('_menu.php'); ?>

	<!-- *****************************************************************************************************************
	 BLUE WRAP
	 ***************************************************************************************************************** -->
	<div id="blue">
	    <div class="container">
			<div class="row">
				<div class="col-md-8">
					<h3>To Be Curated</h3>
				</div>
				<div class="col-md-4 text-right">
					<form>Oldest first? <input type="checkbox" value="true" name="older_first" onClick="submit();" <?=(isset($_REQUEST['older_first'])?"checked":"")?>/></form>
				</div>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->


	<!-- *****************************************************************************************************************
	 BLOG CONTENT
	 ***************************************************************************************************************** -->

	 <div class="container">
	 	<div class="row">
	 		<! -- SINGLE POST -->
	 		<div class="col-md-12">
				<table class="table">
				<?php
				$linktag = (isset($_REQUEST['link'])?' link LIKE '."'%".$_REQUEST['link']."%' AND ":'');
				$orderby = (isset($_REQUEST['older_first'])?'ORDER BY timestamp':'ORDER BY timestamp DESC');
				$sql="SELECT * FROM tobecurated WHERE ".$linktag." tags IS NULL ".$orderby." LIMIT 100";
				$query_response = query($sql);
				$idx = 1;
				foreach ($query_response['rows'] AS $row) {
					$hn = explode('/', $row[1]);
					$tags = (empty($row[4])?'':strtolower($row[4]).'<br />');
					?>
					<tr id="row<?=$row[0]?>">
						<td><b><a href="<?=$row[1]?>" target="_newWin"><?=$row[2]?></a></b><br /><?=$tags?><small><?=$hn[2]?> - <?=date("Y-m-d", strtotime($row[3]))?></small></td>
						<td><button class="btn btn-sm btn-success" onClick="linkMaintenance('sav', '<?=$row[1]?>', '<?=$row[2]?>', <?=$row[0]?>);">Save</button></td>
						<td><button class="btn btn-sm btn-warning" onClick="linkMaintenance('curate', '', '', <?=$row[0]?>);">Curate</button></td>
						<td><button class="btn btn-sm btn-danger" onClick="linkMaintenance('del', '<?=$row[1]?>', '', <?=$row[0]?>);">Delete</button></td>
					</tr>
					<?php
					$idx++;
				}
				?>
				</table>
			</div><! --/col-lg-8 -->
	 	</div><! --/row -->
	 </div><! --/container -->

	 <?php require_once('_footer.php'); ?>


	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/retina-1.1.0.js"></script>
	<script src="assets/js/jquery.hoverdir.js"></script>
	<script src="assets/js/jquery.hoverex.min.js"></script>
	<script src="assets/js/jquery.prettyPhoto.js"></script>
	<script src="assets/js/jquery.isotope.min.js"></script>
	<script src="assets/js/custom.js"></script>

		<script>
			function linkMaintenance(func, lnk, ttl, id) {

				if (func == 'sav') {
					console.log("saving link "+lnk);
					$.getJSON( '_functions.php?method=delcuratelink&id='+id, function( data ) {
					  if (data.status == "ok") {
							console.log("deleted link");
							$('#row'+id).hide();

							if (lnk.indexOf("#")>=0) {
							  lnk = lnk.substring(0,lnk.indexOf('#'));
							}
							console.log("Link:"+lnk);
							window.location = 'addnew.php?link='+lnk+'&title='+ttl;
						} else {
							alert(data.message);
						}
					});
				}

				if (func == 'del') {
					$.getJSON( '_functions.php?method=delcuratelink&id='+id, function( data ) {
					  if (data.status == "ok") {
							console.log(data.message);
							$('#row'+id).hide();
						} else {
							alert(data.message);
						}
					});
				}

				if (func == 'curate') {
					$.getJSON( '_functions.php?method=curatelink&id='+id, function( data ) {
						console.log(data);

					  if (data.status == "ok") {
							console.log(data.message);
							$('#row'+id).hide();
						} else {
							alert(data.message);
						}
					});
				}
			}
			</script>


  </body>
</html>
