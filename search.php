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
				    <label for="fldQ"> Search for: </label>
				    <input type="text" class="form-control" id="fldQ" name="q" value="<?php echo (isset($_REQUEST['q'])?$_REQUEST['q']:'');?>" />
				  </div>
				</form>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container">
	 	<div class="row">
      <div class="col-lg-8">
				<table class="table" id="tableLinks">
          <thead>
              <tr>
                <th>Host</th>
                <th>Link</th>
                <th>Created At</th>
								<th> </th>
								<th> </th>
              </tr>
          </thead>
					<tbody>
						<?php
						if (isset($_REQUEST['q'])) {
							$sql="SELECT * FROM links WHERE UCASE(title) LIKE '%".$_REQUEST['q']."%' ".
								(isset($_REQUEST['fldNoTags'])?" AND tags = ''":"").
								" ORDER BY updated_at  ".(isset($_REQUEST['fldOldestFirst'])?'ASC':'DESC')." LIMIT 1000";
							$searchresults = query($sql);
							foreach($searchresults['rows'] AS $row) {
								$link = new Link($row[0]);
                  ?>
                  <tr id="row<?=$link->id?>">
                    <td>
                      <?php echo justHostName($link->link);?>
                    </td>
                    <td>
                      <a href="<?=$link->link?>"
												target="_newWindow"><?=urldecode($link->title)?></a><br />
	                      <small><?php
	                      foreach(explode(',', $link->tags) AS $tag) {
													echo '<span class="badge">'.$tag.'</span> ';
												}
	                      ?></small>
                    </td>
                    <td>
                      <?php echo date('Y-m-d', strtotime($link->created_at));?>
                    </td>
										<td>
											<button class="btn btn-sm btn-danger" onClick="deleteLink(<?=$link->id?>)"><i class="fa fa-times" aria-hidden="true"></i></button>
										</td>
										<td>
											<a class="btn btn-sm btn-success" href="linkedit.php?id=<?=$link->id?>" target="_newResultWindow"><i class="fa fa-check" aria-hidden="true"></i></a>
										</td>
                  </tr>
                  <?php
                }
						}
						?>
					</tbody>
				</table>
        <br />
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
