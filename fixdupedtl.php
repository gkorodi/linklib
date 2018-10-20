<?php
require_once('_includes.php');

if (!isset($_REQUEST['link'])) {
  $debugs = Array();
  $errors = Array();

  $mysqli = new mysqli(DB_HOST.(defined('DB_PORT')?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
      $errors[] = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  } else {
    $mysqli->autocommit(true);
    if ($mysqli->query("DELETE FROM links WHERE id IN (".implode(',', $_REQUEST['id']).")") === TRUE) {
    } else {
      $errors[] = "Could not execute delete statement: (" . $mysqli->errno . ") " .$mysqli->error;
    }
    $mysqli->close();
  }
  if (count($errors) > 0) {
    echo '<div style="color: red">'.implode('<br />', $errors).'</div>';
    exit();
  }
  header('Location: fixdupes.php');
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
        <h3><?=$_REQUEST['link']?></h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container">
	 	<div class="row">

			<div class="col-lg-8">
        <form>
				<table class="table" id="tableLinks">
					<tbody>
						<?php
							$sql="select * from links where link = '".$_REQUEST['link']."' ORDER BY last_updated";
							$searchresults = query($sql);
							foreach($searchresults['rows'] AS $row) {
                ?>
                <tr>
                  <td><input type="checkbox" name="id[]" value="<?=$row[0]?>" /> <a href="linkedit.php?id=<?=$row[0]?>" target="newWindow"><?=$row[0]?></a></td>
                  <td>
                    <b><?=$row[2]?></b>
                  </td>
                  <td>
                    <?=$row[4]?>
                  </td>
                  <td>
                    <?=$row[5]?>
                  </td>
                </tr>
                <?php
                }
						?>
					</tbody>
				</table>
        <input class="btn btn-danger" type="Submit" value="Del" /><br /><br />
      </form>
       </div>

			 <div class="col-lg-4">
			 </div>
	 	</div><!--/row -->
	 </div><!--/container -->

	<?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>
  </body>
</html>
