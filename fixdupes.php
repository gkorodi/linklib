<?php
require_once('_includes.php');
?><!DOCTYPE html>
<html lang="en">
  <head>
	  <?php require_once('_metatags.php');?>
    <link rel="shortcut icon" href="assets/ico/favicon.ico">

    <title><?php echo APP_TITLE;?> - FixDuplicates</title>

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

			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container">
	 	<div class="row"> Counter:<div id="countOfLinks"></div><br />

<form method="GET" action="fixdupedtl.php">
  <input type="submit" class="btn btn-danger" value="Del" /><br />
<br />
			<div class="col-lg-8">
				<table class="table" id="tableLinks">
					<tbody>
						<?php
							$sql="select count(*) as counter, link from links group by link order by counter desc LIMIT 200";
							$searchresults = query($sql);

							foreach($searchresults['rows'] AS $row) {
                if ($row[0] < 2) { continue; }
                  $sqll="select * from links WHERE link LIKE '".$row[1]."%' ORDER BY last_updated";
                  $linkresults = query($sqll);
                  ?>
                  <tr>
                    <td><?=$row[0]?></td>
                    <td>
                      <a href="fixdupedtl.php?link=<?=$row[1]?>"><?=$row[1]?></a>
                    </td>
                  </tr>
                  <?php
                  foreach($linkresults['rows'] as $r) {
                    ?>
                    <tr>
                      <td><input type="checkbox" name="id[]" value="<?=$r[0]?>" /></td>
                      <td>
                        <a href="linkedit.php?id=<?=$r[0]?>" target="newWindow"><?=$r[0]?></a>
                        [<i><?=$r[4]?></i>]
                        <?=$r[2]?>
                        <small><b><?=$r[5]?></b></small>
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
			 </div>
     </form>

	 	</div><!--/row -->
	 </div><!--/container -->

	<?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>
  <script>
  $('#countOfLinks').html(<?=count($searchresults['rows'])?>);
  </script>
  </body>
</html>
