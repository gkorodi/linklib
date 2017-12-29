<?php
require_once('_includes.php');

if (isset($_REQUEST['tag'])) {
	if ($_REQUEST['tag'] == 'empty') {
	        $sql = "SELECT * FROM links WHERE tags IS NULL ".
	      	  	(isset($_REQUEST['notstatus'])?' AND status != '.$_REQUEST['notstatus']:'').
	      		(isset($_REQUEST['status'])?' AND status = '.$_REQUEST['status']:'').
		' LIMIT 300';

	} else {
	        $sql = "SELECT * FROM links WHERE UPPER(tags) LIKE '%".strtoupper($_REQUEST['tag'])."%' ".
	      	  	(isset($_REQUEST['notstatus'])?' AND status != '.$_REQUEST['notstatus']:'').
	      		(isset($_REQUEST['status'])?' AND status = '.$_REQUEST['status']:'').
						' ORDER BY last_updated '.(isset($_REQUEST['dateorder'])?'ASC':'DESC');
	}
  $resultset = query($sql);
}
?><!DOCTYPE html>
<html lang="en">
  <head>
	  <?php require_once('_metatags.php');?>
    <link rel="shortcut icon" href="assets/ico/favicon.ico">

    <title><?php echo APP_TITLE;?></title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
		<link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" />


    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="assets/js/modernizr.js"></script>

	    <style>
	    tr.status200 {
		    background-color: white;
	    }

	    tr.status500 {
		    background-color: red;
		    color: white;
	    }

	    tr.status301 {
		    background-color: orange;
	    }

	    tr.status302 {
		    background-color: orange;
	    }

	    </style>
  </head>

  <body>
		<?php require_once('_menu.php'); ?>
		<div id="blue">
	    <div class="container">
			<div class="row">
				<h3>Search Results by tag <b><?php echo (isset($_REQUEST['tag'])?$_REQUEST['tag']:'Not Set?! Whaaat?');?></b>.</h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container mtb">
	 	<div class="row">
	 		<div class="col-md-12">
				<?php
				if (!isset($_REQUEST['tag'])) {
					?>
					<h3>Really?</h3>
					<p>Give me a tag</p>
					<form method="GET">
					<input type="text" name="tag" />
					<button class="btn btn-success">Search</button>
					</form>
					<?php
				} else {
					?>
					<table id="tableLinks">
					<thead>
						<tr>
							<td>Host</td>
							<td>Link</td>
							<td>Timestamp</td>
							<?php
	            if ($_SESSION['role'] === 'ADMIN') {
	                    echo "<td> </td><td> </td>";
	            }
	            ?>
			    </tr>
					</thead>
					<tbody>
					<?php
					foreach($resultset['rows'] AS $row) {
						$lst = explode('/',  $row[0]);
						?>
						<tr id="row<?php echo $row[0];?>">
							<td>
								<?php echo justHostName($row[1]); ?></small>
							</td>
							<td>
								<h3><a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a></h3>
								<small><?php
								foreach(explode(',', $row[5]) AS $tag) { ?>
									<span class="badge"><?php echo $tag;?></span>
								<?php
								}
								?>
							</td>
							<td>
								<small><?php echo date('Y-m-d', strtotime($row[4]));?></small>
							</td>
							<?php
							if ($_SESSION['role'] === 'ADMIN') {
								?><td>
									<button class="btn btn-danger pull-right" onClick="deleteLink(<?php echo $row[0];?>);">
										<span class="glyphicon glyphicon-remove"> </span>
									</button>
								</td>
								<td>
									<a class="btn btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
										<span class="glyphicon glyphicon-ok"> </span>
									</a>
								</td><?php
							}
							?>
						</tr>
						<?php
					}?>
					</tbody>
					</table><?php
				}
				?>
			</div>
	 	</div>
	 </div>
	 <?php require_once('_footer.php'); ?>
	 <?php require_once('_scripts.php'); ?>
	 <script>
		$(document).ready(function() {
			$('#tableLinks').DataTable({
				"columns": [
					null,
					null,
					null,
					{ "orderable": false },
					{ "orderable": false }
				]
			});
		});
	 </script>
  </body>
</html>
