<?php
require_once('_includes.php');
if (isset($_REQUEST['host'])) {
  $hostcriteria = strtoupper($_REQUEST['host']);
  $sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".$hostcriteria."%' AND status != 200";
  $query_response = query($sql);
  if ($query_response["rowcount"] == 0) {
      header('Location: showhosts.php');
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

    <title><?=APP_TITLE?></title>

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
  </head>

  <body>

	  <?php require_once('_menu.php'); ?>

	<!-- *****************************************************************************************************************
	 BLUE WRAP
	 ***************************************************************************************************************** -->
	<div id="blue">
	    <div class="container">
			<div class="row">
				<h3>Search Results by host <b><?=$_REQUEST['host']?></b>.</h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->


	<!-- *****************************************************************************************************************
	 BLOG CONTENT
	 ***************************************************************************************************************** -->

	 <div class="container mtb">
	 	<div class="row">
	 		<div class="col-md-12">
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

  				$hostcriteria = strtoupper($_REQUEST['host']);
  				$sql = "SELECT * FROM links WHERE UPPER(link) LIKE '%".$hostcriteria."%' AND status != 200";
  				$query_response = query($sql);
  				?>
  				<table class="table">
  				<?php
  				foreach($query_response['rows'] AS $row) {
  					$lst = split('/',  $row[0]);
  					?>
  					<tr class="status<?php echo $row[4];?>" id="row<?=$row[0]?>">
  					<td><a href="https://duckduckgo.com/?q=<?php echo urlencode($row[2]);?>" target="_newWindow"><?php echo $row[2];?></a><br />
  						<input type="text" size="80" value="<?=$row[1]?>"
  							onChange="updateRow(<?php echo $row[0];?>,'link',$(this).val());" /></td>
  					<td onClick="testRow(<?php echo $row[0];?>,'<?=$row[1]?>')"><?php echo $row[3];?></td>
  					<td><input type="text" value="<?php echo $row[5]?>" onChange="updateRow(<?php echo $row[0];?>,'tags',$(this).val());"/></td>
  					<td><button class="btn btn-danger" onClick="deleteRow(<?php echo $row[0];?>);">Del</button></td>
            <td><a class="btn btn-info" href="edit.php?id=<?php echo $row[0];?>" target="_winEdit">Edit</a></td>
  					</tr>
  					<?php
  				}?>
        </table><?php
        }
				?>
			</div><! --/col-lg-12 -->
	 	</div><! --/row -->
	 </div><! --/container -->


<?php require_once('_footer.php'); ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/retina-1.1.0.js"></script>
	<script src="assets/js/jquery.hoverdir.js"></script>
	<script src="assets/js/jquery.hoverex.min.js"></script>
	<script src="assets/js/jquery.prettyPhoto.js"></script>
  	<script src="assets/js/jquery.isotope.min.js"></script>
  	<script src="assets/js/custom.js"></script>
    <script src="assets/js/jquery.toastmessage.js" type="text/javascript"></script>
    <link href="assets/css/jquery.toastmessage.css" rel="stylesheet" type="text/css" />

<script>
    function testRow(rowId, rowLink) {
	  $.get( "_functions.php?method=testlink", { id: rowId, url: rowLink} )
	    .done(function( data ) {
		    if (data.status == 'ok') {
			    $('#row'+rowId).hide();
		    } else {
			    $().toastmessage('showErrorToast', data.debugs);
			    console.log(data);
		    }
	    });
    }

    function updateRow(rowId, field, value) {
	  $.get( "_functions.php?method=updatelink", { id: rowId, column: field, value: value } )
	    .done(function( data ) {
		    if (data.status != 'ok') {
		    	$().toastmessage('showErrorToast', "The row "+rowId+" could not be updated."+data.debugs);
			console.log(data);
		    }
	    });
    }

    function deleteRow(rowId) {
	  $.get( "_functions.php?method=deletelink", { id: rowId } )
	    .done(function( data ) {
		    if (data.status == 'ok') {
			    $('#row'+rowId).hide();
		    } else {
		    	$().toastmessage('showErrorToast', "The row "+rowId+" could not be deleted.");
			console.log(data);
		    }
	    });
    }
    </script>

  </body>
</html>
