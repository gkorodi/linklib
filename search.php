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

          <div class="form-group">
				    <label for="fldNoTags"> NoTags: </label>
				    <input type="checkbox" class="form-control" id="fldNoTags" name="fldNoTags" <?php echo (isset($_REQUEST['fldNoTags'])?'checked':'');?> />
				  </div>

          <div class="form-group">
				    <label for="fldOldestFirst"> OldestFirst: </label>
				    <input type="checkbox" class="form-control" id="fldOldestFirst" name="fldOldestFirst" <?php echo (isset($_REQUEST['fldOldestFirst'])?'checked':'');?> />
				  </div>
				</form>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container">
	 	<div class="row">
      <br />
				<table class="table" id="tableLinks">
          <thead>
            <tr>
              <th>Host</th>
              <th>Link</th>
              <th>Tags</th>
              <th>Date</th>
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
							/*?>
							<tr id="row<?php echo $row[0]; ?>">
								<td>
									<a href="<?php echo $row[1]; ?>" target="_winLinkURL"><?php echo $row[2]; ?></a><br />
									<small>host: <b><?php echo justHostName($row[1]);?></b><br />
									update: <b><?php echo $row[4];?></b></small>
								</td>
								<td>
									<input class="fldTags" type="text"
										data-ref="<?php echo $row[0]; ?>" name="tags"
											value="<?php echo $row[5];?>" />
								</td>
								<td>
									<button class="btn btn-sm btn-danger" onClick="dellink(<?php echo $row[0]; ?>);">Del</button>
									<a class="btn btn-sm btn-info"
										href="linkedit.php?id=<?php echo $row[0]; ?>" target="_newWin">...</a>
								</td>
							</tr>
							<?php*/
                if ($_SESSION['role'] == 'USER') {

                  ?>

                  <tr id="row<?php echo $row[0];?>">
                    <td>
                      <?php echo justHostName($row[1]);?>
                    </td>
                    <td>
                      <a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a>
                    </td>
                    <td>
                      <?php
                      foreach(explode(',', $row[5]) AS $tag) { echo '<span class="badge">'.$tag.'</span> ';}
                      ?>
                    </td>
                    <td>
                      <?php echo date('Y-m-d', strtotime($row[4]));?>
                    </td>
                  </tr>

                  <?php
                } else {
                  showRow($row);
                }
							}
						}
						?>
					</tbody>
				</table>
        <br />
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

      $('input[name=fldNoTags]').on('change', function() {
        $('#frmSearchQuery').submit();
      });
      $('input[name=fldOldestFirst]').on('change', function() {
        $('#frmSearchQuery').submit();
      });
  	});
	</script>
  </body>
</html>
