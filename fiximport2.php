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
	 	<div class="row">

			<div class="col-lg-12">
				<table class="table" id="tableLinks">
					<tbody>
						<?php
							$sql="select * from import2 WHERE status != 666 order by last_updated LIMIT 200 ";
							$searchresults = query($sql);
							foreach($searchresults['rows'] AS $row) {
                if ($row[0] < 2) { continue; }
                  ?>
                  <tr id="row<?=$row[0]?>">
                    <td><button class="btn btn-danger" onClick="dellink(<?=$row[0]?>);"/>Del</button></td>
                    <td><button class="btn btn-info" onClick="savelink(<?=$row[0]?>);"/>Save</button></td>
                    <td>
                      <a href="<?=$row[1]?>" target="newWin"><?=$row[2]?></a><br />
                      <?php
                      $a = explode('/', $row[1]);
                      echo $a[2];
                      ?>
                    </td>
                    <td>
                      <?=$row[4]?>
                    </td>
                  </tr>
                  <?php
              }
						?>
					</tbody>
				</table>
       </div>

	 	</div><!--/row -->
	 </div><!--/container -->

	<?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>
  <script>
  $('#countOfLinks').html(<?=count($searchresults['rows'])?>);

  function dellink(linkid) {
    $.getJSON( "_dellink.php?table=import2&id="+linkid, function( data ) {
      if (data.status == 'ok') {
        $('#row'+linkid).hide();
      } else {
        alert(data.message);
      }
    });
  }

  function savelink(linkid) {
    $.getJSON( "_savelink.php?table=import2&id="+linkid, function( data ) {
      if (data.status == 'ok') {
        $('#row'+linkid).hide();
      } else {
        alert(data.message);
      }
    });
  }
  </script>
  </body>
</html>
