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
	if (isset($_POST['id'])) {
		if (!$link->updateByMap($_POST)) {
			$errorMessage = 'Could not updates link!'.'<br />'.implode('<br />', $link->debugs);
		}
		$linktags_after = $link->tags;
	}
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
			  <?php
			  //$info = $link->getURLInfo();
			  if ($errorMessage != null) {
				  echo '<div style="color:red">'.$errorMessage.'</div>';
			  }
			  ?>
      </div> <!-- /container -->
    </div><!-- /blue -->

    <div class="container mtb">
      <div class="row">
        <div>
	 <form id="frmEditLink" method="POST">
	  <div class="form-group">
	    <label for="link">Link:</label>
	    <input type="text" class="form-control" id="link" name="link" value="<?php echo $link->link;?>" />
	  </div>

	  <div class="form-group">
	    <label for="tags">Tags:</label>
	    <input type="text" class="form-control" id="tags" name="tags" value="<?php echo $link->tags;?>" />
	  </div>

	  <div class="form-group" id="fldLastUpdated">
	    <label for="last_updated">Last Update:</label><br />
	    <input type="text" class="form-controlx" style="padding: 3px"
	    	id="last_updated" name="last_updated" value="<?php echo date('Y-m-d', strtotime($link->row['last_updated']));?>" size="12" />
	  </div>


	<a class="btn btn-info" href="linkedit.php?id=<?php echo $nextid;?>" accesskey="N" id="btnNext"><u>N</u>ext</a>

	<?php
	/*$url = $info['url'];
	if ($info['http_code'] == 200 && $info['redirect_count'] > 0) {
		if (strpos($info['url'],'?')) {
		$url = substr($info['url'],0,strpos($info['url'],'?'));
		}
		echo '  <a class="btn btn-warning"
				href="#" onClick="updateLink(\''.$url.'\', '.$info['http_code'].');">'.
		'Status <sup>'.$info['http_code'].'/'.$info['redirect_count'].'</sup></a>';
	} else {
				echo '  <a class="btn disabled" href="#">Status <sup>-1</sup></a>';
	}*/
	?>

	<a class="btn btn-warning"
		id="btnHeader" href="#">Hdr</a>

		<input type="submit" name="btnUpdate"
		  	id="btnUpdateId" class="btn btn-info" value="Update" />

	<a class="btn btn-warning" href="<?php echo $link->link;?>" target="_newWindow">Show</a>

	<a class="btn btn-warning"
		href="https://duckduckgo.com/?q=<?php echo urlencode($link->title);?>&t=ffsb&ia=web"
		target="_srchWindow">Duck</a>


			<br />
	<br />

	  <div class="form-group">
	    <label for="title">Title:</label>
	    <input type="text" class="form-control"
	    	id="title" name="title" value="<?php echo urldecode($link->title);?>" />
	  </div>

	  <div class="form-group">
	    <label for="status">Status:</label><br />
	    <input type="text" class="form-controlx" style="padding: 3px"
	    	id="status" name="status" value="<?php echo $link->status;?>" size="5"/>
	  </div>
	  <input type="hidden" id="id" name="id" value="<?php echo $link->id;?>" />


	</form>
	<button id="btnDelete" class="btn btn-danger">Delete</button><br />
	<br />
	<br />


	<br />
	<br />
	</div>

      </div><! --/row -->
    </div><! --/container -->


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

	$('#btnHeader').on('click', function(event) {
		var getHeaderURL = '_functions.php?method=getheader&id=<?php echo $link->id;?>';
		$('#fldLastUpdated').css('background-color','pink');
		$.get(getHeaderURL, function(data) {

			$('#title').val(data.meta.og_title?data.meta.og_title:$('#title').val());
			var newDate = getDateMetaTag(data.meta);
			if (newDate != '') {
				$('#last_updated').val(newDate);
			}
			$('#fldLastUpdated').css('background-color','lightGreen');
			if (data.status == 'ok') {
				$('#status').val('200');
			}
			$('#fldLastUpdated').css('background-color','white');
		});
		
		$('#blue').css('background-color',baseColor);
	});
	</script>
</body>
</html>
