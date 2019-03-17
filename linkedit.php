<?php
require_once('_includes.php');

$handed = "right";

$getRandomIdSQL = 'SELECT r1.id FROM links AS r1 '.
	'JOIN '.
	'(SELECT CEIL(RAND() * '.
			'(SELECT MAX(id) FROM links)'.
		') as id) AS r2 '.
	'WHERE '.
		'r1.id >= r2.id '.
	'AND '.
		'r1.tags IS NULL '.
	'LIMIT 1';

if (!isset($_REQUEST['id']) || $_REQUEST['id'] == '') {
	$links = Array();
	try {
		$links = query($getRandomIdSQL);
	} catch (Exception $e) {
		throw new Exception('Could not run SQL query:'+$getRandomIdSQL);
	}

	if (count($links['rows']) == 0) {
		throw new Exception('There is no link available.'+$getRandomIdSQL);
	}
	$link = new Link($links['rows'][0][0]);
} else {
	$link = new Link($_REQUEST['id']);
}

if (!$link->id) {
	throw new Exception('There is no link->id field!'.print_r($debug, true));
}

$errorMessage = null;
if (isset($_POST['id'])) {
	
	$link->title = $_POST['title'];
	$link->link = $_POST['link'];
	$link->tags = implode(',', explode(',', str_replace(' ','', strtolower($_POST['tags']))));
	$link->status = $_POST['status'];
	
	$link->updated_at = date('Y-m-d', strtotime($_POST['updated_at']));
	$link->created_at = date('Y-m-d', strtotime($_POST['created_at']));
	if (!$link->update()) {
		$errorMessage = 'Could not updates link!'.'<br />'.implode('<br />', $link->debugs);
	}
}

?><!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once('_metatags.php');?>
	<link rel="shortcut icon" href="assets/ico/favicon.ico">
	<title><?=APP_TITLE?></title>
	<link href="assets/css/bootstrap.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="assets/css/style.css" rel="stylesheet">
	<link href="assets/css/font-awesome.min.css" rel="stylesheet">
	<script src="assets/js/modernizr.js"></script>
</head>
<body>
    <!-- Fixed navbar -->
    <?php require_once('_menu.php'); ?>

    <!-- *****************************************************************************************************************
    BLUE WRAP
    ***************************************************************************************************************** -->
    <div id="blue">
      <div class="container">
        <div class="row">
          <h3 id="lblTitle"><?=$link->title?></h3>
        </div><!-- /row -->
				<div style="color:red"><?=(isset($errorMessage) && !empty($errorMessage))?$errorMessage:''?></div>
      </div> <!-- /container -->
    </div><!-- /blue -->

    	<div class="container">
      	<div class="row">

					<a class="btn btn-info pull-<?=$handed?>" href="linkedit.php" id="btnNext" accessKey="n"><u>N</u>ext</a>

					<a style="margin-right: 10px" class="btn btn-warning"
						href="https://duckduckgo.com/?q=<?php echo urlencode($link->title);?>&t=ffsb&ia=web"
							target="_srchWindow">Duck</a>

					<a style="margin-right: 10px" class="btn btn-warning" href="<?php echo $link->link;?>" target="_newWindow">Show</a>

					<a style="margin-right: 10px" class="btn btn-warning" id="btnFix" accesskey="x">Fi<u>x</u></a>

					<br/>
					<br />
					<div class="col-sm">
						<form id="frmEditLink" method="POST">
							<div class="form-group">
								<label for="title">Title:</label>
								<input type="text" class="form-control" id="title" name="title" value="<?php echo isset($link->title)?$link->title:'Missing Title';?>" />
							</div>

							<div class="form-group">
								<label for="link">Link:</label>
								<input type="text" class="form-control" id="link" name="link" value="<?=$link->link?>" />
							</div>

							<div class="form-group">
								<label for="tags">Tags:</label>
								<input type="text" class="form-control" id="tags" name="tags" value="<?=$link->tags?>" />
							</div>

							<div class="form-group">
								<label for="status">Status:</label>
								<input type="text" class="form-control" id="status" name="status" value="<?=empty($link->status)?'200':$link->status?>" />
							</div>

							<div class="form-group">
								<label for="created_at">Created At:</label>
								<input type="text" class="form-control" id="created_at" name="created_at" value="<?=empty($link->created_at)?date('Y-m-d'):$link->created_at?>" />
							</div>

							<div class="form-group">
								<label for="updated_at">Updated At:</label>
								<input type="text" class="form-control" id="updated_at" name="updated_at" value="<?=empty($link->updated_at)?date('Y-m-d'):$link->updated_at?>" />
							</div>
							<input type="hidden" id="id" name="id" value="<?=$link->id?>" />
							
							<a class="btn btn-lg btn-danger pull-><?=$handed?>" accesskey="D" id="btnDelete"><u>D</u>elete</a>
							<input type="submit" name="btnUpdate" id="btnUpdateId" class="btn btn-lg btn-info" value="Update" /><br />
						</form>
					</div>
      </div><!--/row -->
			<br />
			<br />
    </div><!--/container -->

	<?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>

	<script>
	$('#btnNext').on('click', function() {
		$('#blue').css('background-color','gray');
	});

	$('#btnDelete').on('click', function() {
		$('#blue').css('background-color','orange');
		$.get('_functions.php?method=deletelink&id=<?=$link->id?>', function(data) {
			if (data.status == 'ok') {
				$('#blue').css('background-color','lightGreen');
				window.location='linkedit.php';
			} else {
				alert('Failed to delete link <?=$link->id?>');
				$('#blue').css('background-color','red');
			}
		});
	});

	$("form input[type=submit]").click(function() {
		$('#lblTitle').html("Loading ...");
		$('#btnNext').focus();
	        if($(this).val() == 'Update') {
	        	return true;
	        }
	});

	$('#btnFix').on('click', function(event) {
		
		var baseColor = $('#blue').css('background-color');
		$('#blue').css('background-color','#f0f0f0');
		
		console.log("Calling URL:"+'_functions.php?method=getheader&id=<?=$link->id?>');
		$.get('_functions.php?method=getheader&id=<?=$link->id?>', function(data) {
			console.log(data);
			$('#blue').css('background-color','lightGreen');

			// This is just for example
			//for (var key in data.meta){
			//  console.log('Key:' + key + " -> Value:" + data.meta[key]);   
			//}
			$('#link').val(data.link);
			$('#title').val(data.title);
			$('#lblTitle').html(data.title);
			$('#tags').val(data.tags);
			$('#created_at').val(data.created_at);
			$('#updated_at').val(data.updated_at);
			$('#status').val(data.status);
		});
		
		$('#blue').css('background-color',baseColor);
	});
	</script>
</body>
</html>
