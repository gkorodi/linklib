<?php
require_once('_includes.php');
$debugs = Array();
$handed = "left";

$randomQuery = 'SELECT * FROM links WHERE tags IS NULL LIMIT 1';
$randomQuery = 'SELECT * FROM links AS r1 JOIN (SELECT CEIL(RAND() * (SELECT MAX(id) FROM links WHERE tags IS NULL)) AS id) AS r2 WHERE r1.id >= r2.id AND tags IS NULL ORDER BY r1.id ASC LIMIT 1';
if (!isset($_REQUEST['id']) || $_REQUEST['id'] == '') {
	$links = Array();
	try {
		$links = query($randomQuery);
	} catch (Exception $e) {
		throw new Exception('Could not run full SQL query:');
	}

	if (count($links['rows']) == 0) {
		throw new Exception('There is no link available.');
	}
	
	$link = new Link($links['rows'][0]);
	
} else {
	$debugs[] = 'Creating new link object.';
	$link = new Link($_REQUEST['id']);
	$debugs[] = 'Freshly minted tags.'.$link->tags;
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
	$link->created_at = empty($_POST['created_at'])?date('Y-m-d'):date('Y-m-d', strtotime($_POST['created_at']));
	$link->updated_at = date('Y-m-d');
	$link->description = empty($_POST['description'])?'{"pagetitle":"'.$link->title.'"}':$_POST['description'];
	
	$msgs = [];
	if (!$link->update()) {
		if (count($link->errors)>0) {
			foreach($link->errors AS $msg) {
				$msgs[] = '<h4>ERROR:'.$msg.'</h4>';
			}
		}
		// if (count($link->debugs)>0) {
		// 	foreach($link->debugs AS $msg) {
		// 		$msgs[] = 'DEBUG:'.$msg;
		// 	}
		// }
		$errorMessage = 'Could not updates link!'.'<br />'.implode('<br />', $msgs);
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
				  <div class="col-2">
						<a class="btn-lg btn-info" href ="linkedit.php" id="btnNext" accesskey="n"><u>N</u>ext</a>
					</div>
				</div>
				
        <div class="row">
					<div class="col">
						<h3>
							<a href="<?=$link->link?>" target="_newLinkWin"><?=$link->title?></a>
						</h3>
						
						<div style="color:red">
							<?=(isset($errorMessage) && !empty($errorMessage))?$errorMessage:''?>
						</div>
						
						<p class="lead">
							<?php
							if (!empty($link->description)) {
								try {
									$metatags = json_decode($link->description);
									if (isset($metatags->description) && !empty($metatags->description)) {
										echo $metatags->description;
									}
								} catch (Exception $ex) {
									echo 'Could not parse the "description" field.'.$ex->getMessage();
								}
							}	
							?>
						</p>
					</div>
        </div><!-- /row -->
				
				<div class="row">
					<div class="col">
						<a class="btn btn-lg btn-danger" id="btnDelete" accesskey="D"><u>D</u>elete</a>
						<a class="btn btn-lg btn-warning" id="btnFix" accesskey="x">Fi<u>x</u></a>
					</div>
				</div>
      </div> <!-- /container -->
    </div><!-- /blue -->

<form id="frmEditLink" class="form-horizontal" method="POST">
    	<div class="container">
					<div class="row">
						<a style="margin-right: 10px" class="btn btn-warning"
						href="https://duckduckgo.com/?q=<?php echo urlencode($link->title);?>&t=ffsb&ia=web"
						target="_srchWindow">Duck</a>

						<a style="margin-right: 10px" class="btn btn-warning" href="<?php echo $link->link;?>" target="_newWindow">Show</a>
					
						<?php
						if (isset($_SESSION['uid'])) {
						?>
							<input type="submit" name="btnUpdate" id="btnUpdateId" class="btn btn-info " value="Update" /><br />
						<?php
						}
						?>
						<br/>
						<br />
					</div>
					
					<div class="row">
						<div class="form-group col-lg-4">
							<label for="tags">Tags:</label>
							<input type="text" class="form-control" id="tags" name="tags" value="<?=$link->tags?>" />
						</div>
					</div>
				
					<div class="row">
						<div class="form-group col-lg-4">
							<label for="created_at">Created At:</label>
							<input type="text" class="form-control" id="created_at" name="created_at" size="12" value="<?=empty($link->created_at)?date('Y-m-d'):date('Y-m-d', strtotime($link->created_at))?>" />
						</div>
					</div>
					
					<div class="row left">
						<div class="form-group col-lg-4">
							<label for="link">Link:</label>
							<input type="text" class="form-control" id="link" name="link" value="<?=$link->link?>" />
						</div>
					</div>
				
					<div class="row">
						<textarea name="description" id="description" cols="100" rows="10"><?=$link->description?></textarea>
					</div>
				
					<div class="row">
						<div class="form-group">
							<label for="title">Title:</label>
							<input type="text" class="form-control" id="title" name="title" value="<?php echo isset($link->title)?$link->title:'Missing Title';?>" />
						</div>
						
					
						<input type="hidden" id="id" name="id" value="<?=$link->id?>" />

						<div class="form-group">
							<label for="status">Status:</label>
							<input type="text" class="form-control" id="status" name="status" value="<?=empty($link->status)?'200':$link->status?>" size="10" />
						</div>

						<div class="form-group">
							<label for="fld_updated_at">Updated At:</label>
							<input type="text" readonly class="form-control" id="fld_updated_at" value="<?=date('Y-m-d', strtotime($link->updated_at))?>" size="12">
						</div>
						
					</div>
    </div><!--/container -->
		</form>

	<?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>

	<script>
	$('#btnNext').on('click', function() {
		console.log('going to next records.')
		$('#blue').css('background-color','gray');
	});
	
	$('#btnCurate').on('click', function() {
		tagCurate(<?=$link->id?>);
		window.location='linkedit.php';
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
		var baseColor = $('#blue').css('background-color');
		$('#blue').css('background-color','yellow');
		$('#lblTitle').html("Loading ...");
		if($(this).val() == 'Update') {
			$('#blue').css('background-color', baseColor);
		 return true;
		}
	});

	$('#btnFix').on('click', function(event) {
		
		var baseColor = $('#blue').css('background-color');
		$('#blue').css('background-color','yellow');

		
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
			$('#description').html(JSON.stringify(data.meta));
			
			if (data.meta.description != undefined) {
				$('#link-description').html(data.details.description);
			}
		});
		
		$('#blue').css('background-color',baseColor);
	});
	</script>
</body>
</html>
