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
    <link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" />

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
				<!-- <h3>Search Results for <b><?php echo $_REQUEST['q'];?></b>.</h3> -->

				 <form id="frmSearchQuery" class="form-inline" method="GET">
				  <div class="form-group">
				    <label for="fldQ">Results for: </label>
				    <input type="text" class="form-control" id="fldQ" name="q" value="<?php echo (isset($_REQUEST['q'])?$_REQUEST['q']:'');?>" />
				  </div>

				</form>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container">
	 	<div class="row">
			<div class="col-lg-8">
      <br />
				<table class="table" id="tableLinks">
          <thead>
              <tr>
                <th>Host</th>
                <th>Link</th>
                <th>Date</th>
								<th> </th>
              </tr>
          </thead>
					<tbody>
						<?php
						if (isset($_REQUEST['q'])) {
							$sql="SELECT * FROM links WHERE UCASE(title) LIKE '%".$_REQUEST['q']."%' ".
								(isset($_REQUEST['fldNoTags'])?" AND tags = ''":"").
								" ORDER BY last_updated ".(isset($_REQUEST['fldOldestFirst'])?'ASC':'DESC')." LIMIT 1000";
							$searchresults = query($sql);

							foreach($searchresults['rows'] AS $row) {
                  ?>
                  <tr id="row<?php echo $row[0];?>">
                    <td>
                      <?php echo justHostName($row[1]);?>
                    </td>
                    <td>
                      <b><a href="<?php echo $row[1];?>" 
												target="_newWindow"><?php echo urldecode($row[2]);?></a></b><br />
												<small>
		                      <?php
		                      foreach(explode(',', $row[5]) AS $tag) { echo '<span class="badge">'.$tag.'</span> ';}
		                      ?>
												</small>
                    </td>
                    <td>
                      <?php echo date('Y-m-d', strtotime($row[4]));?>
                    </td>
										<td>
											<a href="linkedit.php?id=<?=$row[0]?>" target="newTab">...</a></td>
										</td>
                  </tr>
                  <?php
                }
						}
						?>
					</tbody>
				</table>
       </div>
			 
			 <div class="col-lg-4">
		 		<h4>Tags</h4>
		 		<div class="hline"></div>
				
				<div class="spacing"></div>
				
		 		<h4>Dates</h4>
		 		<div class="hline"></div>
			 </div>
	 	</div><!--/row -->
	 </div><!--/container -->

	<?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>

	<script>
  	$(document).ready(function() {
      $('#tableLinks').DataTable();
  	});
	</script>
  </body>
</html>
