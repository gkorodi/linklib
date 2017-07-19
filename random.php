<?php
require_once('_includes.php');

$sql="SELECT * FROM links";
$raw_rs = query($sql);

$sql="SELECT * FROM links ORDER BY last_updated ASC LIMIT 100";
$resultset = query($sql);

$idlist = Array();
for($idx=0;$idx<(count($resultset['rows'])-1);$idx++) {
  $randomIndex = rand(0,count($resultset['rows'])-1);
  while (in_array($randomIndex, $idlist, TRUE)) {
    $randomIndex = rand(0,count($resultset['rows'])-1);
  }
  array_push($idlist, $randomIndex);
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
				<h3>Random 100 of <span id="totalcount"><?php echo count($raw_rs['rows']);?></span>.</h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->
	<div class="container mtb">
		<div class="row">
			<div id="randomlist" class="col-lg-12">
				<table class="table">
				<?php
				foreach ($idlist AS $itemidx) {
					$row = $resultset['rows'][$itemidx];

          if (isset($_SESSION['role']) && $_SESSION['role'] == 'USER') {
            ?>
            <tr id="row<?php echo $row[0];?>">
              <td>
                <?php echo date('Y-m-d', strtotime($row[4]));?>
              </td>
          		<td>
          			<b><a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a></b><br />
          			<small><?php echo justHostName($row[1]);?></small>
          		</td>
              <td>
                <?php foreach(explode(',', $row[5]) AS $tag) {
                    ?><span class="badge"><?php echo $tag;?></span> <?php
                }?>
          	  </td>
              <td>
                <span class="glyphicon glyphicon-ok"> </span>
          	  </td>
              <td>
                <span class="glyphicon glyphicon-remove"> </span>
          	  </td>
          	</tr>
            <?php
          } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN') {
            ?>
            <tr id="row<?php echo $row[0];?>">
          		<td>
          			<a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a><br />
          			<small><?php echo justHostName($row[1]);?></small>
          		</td>
          		<td>
          		    <input type="text" id="tags<?php echo $row[0];?>"
                    onChange="repairLink(<?php echo $row[0];?>, $(this).val());" value="<?php echo $row[5];?>" />
          	  </td>

              <td>
          		  <?php echo date('Y-m-d', strtotime($row[4]));?>
          	  </td>

              <td>
          			<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
          				<span class="glyphicon glyphicon-ok"> </span>
          			</a>
          		</td>
          	</tr>
            <?php
          } else {

          }
				}
				?>
				</table>
			</div>
		</div>
	</div>
	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
  <script>
  function checkDetails(linkid) {
    alert(linkid);
  }
  </script>
</body>
</html>
