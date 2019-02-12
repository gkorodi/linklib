<?php
require_once('_includes.php');

if (isset($_REQUEST['host'])) {
  $sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".strtoupper($_REQUEST['host'])."%'".
	  	(isset($_REQUEST['notags'])?" AND (tags IS NULL OR tags = '')":'').
		(isset($_REQUEST['nostatus'])?' AND status != '.$_REQUEST['nostatus']:'').
		(isset($_REQUEST['status'])?' AND status = '.$_REQUEST['status']:'').
		' ORDER BY last_updated '.(isset($_REQUEST['olderfirst'])?' ASC':' DESC').
				', link, title';
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
	<link href="assets/css/style.css" rel="stylesheet">


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
				<h3>Search Results by host <a href="http://<?php echo $_REQUEST['host'];?>" target="_newWindow"><?php echo $_REQUEST['host'];?></a>
				</h3>
				<form method="GET" id="frmRefine">
					<input type="checkbox" id="fldOlderFirst" name="olderfirst" <?php echo (isset($_REQUEST['olderfirst'])?'checked':'');?>/> OlderFirst
					<input type="hidden" id="fldHost" name="host" value="<?php echo $_REQUEST['host'];?>" />
				</form>
				<small>There are <?php echo count($resultset['rows']); ?> entries</small>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container mtb">
     <div class="row">
	     <div class="col-lg-12">
			<?php
			if (!isset($_REQUEST['host'])) {
				?>
				<h3>Really?</h3>
				<p>Give me a host</p>
				<form method="GET">
					<input type="text" name="host" />
					<button class="btn btn-success">Search</button>
				</form>
				<?php
			} else {
        			
							?>
							<table class="table">
								<thead>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</thead>
								<tbody>
							<?php
				foreach($resultset['rows'] AS $row) {
          ?>
          <tr id="row<?php echo $row[0];?>">
            <td>
              <button class="btn btn-sm btn-danger pull-right" onClick="deleteLink(<?php echo $row[0];?>);">
                <span class="glyphicon glyphicon-remove"> </span>
              </button>
            </td>
            <td>
              <b><a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a></b><br />
              <small><?php
		      if (strpos(strtolower($row[5]), 'repair')>-1 || isset($_REQUEST['fullurl'])) {
		      	echo $row[1];
		      }
              ?>
	      
              <?php
              foreach(explode(',', $row[5]) AS $tag) {
                echo '<span class="badge">'.$tag.'</span>';
              }
              ?>
	      
	      			</small>
            </td>
            <td>
		    			<small><?php echo date('Y-m-d', strtotime($row[4]));?></small>
            </td>

            <td>

              <a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
                <span class="glyphicon glyphicon-ok"> </span>
              </a>
            </td>
          </tr>
          <?php
				}
        echo '</tbody></table>';
			}
			?>
    </div>
      </div>
	 </div><! --/container -->


	 <?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>

	<script src="assets/js/jquery.toastmessage.js" type="text/javascript"></script>
	<link href="assets/css/jquery.toastmessage.css" rel="stylesheet" type="text/css" />

	<script>
	$(document).ready(function(){
		$('#fldNoTags').on('change', function() {
			$('#frmRefine').submit();
		});

		$('#fldOlderFirst').on('change', function() {
			$('#frmRefine').submit();
		});
	});
	</script>

  </body>
</html>
