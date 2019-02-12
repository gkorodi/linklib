<?php
require_once('_includes.php');

$handed = "right";

if (!isset($_REQUEST['id']) || $_REQUEST['id'] == '') {
	try {
		$queryString = "SELECT id FROM links WHERE tags IS NULL";
		$links = query($queryString);
	} catch (Exception $e) {
		throw new Exception('Could not run SQL query:'+$queryString);
	}

	if (count($links['rows']) == 0) {
		throw new Exception('There is no link available.'+$queryString);
	}
	$guessid = rand(1,count($links['rows']));
	$randomid = $links['rows'][$guessid][0];
	$link = new Link($randomid);
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
	$link->tags = str_replace(' ','', strtolower($_POST['tags']));
	$link->status = (isset($_POST['status']) && !empty($_POST['status'])?$_POST['status']:-2);
	$link->last_updated = date('Y-m-d H:i', strtotime($_POST['last_updated']));
	$linktags_before = $link->tags;
	if (!$link->update()) {
		$errorMessage = 'Could not updates link!'.'<br />'.implode('<br />', $link->debugs);
	}
	$linktags_after = $link->tags;
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
					
          <h3 id="lblTitle"><?=$link->title?>.<br />
						<small style="color: yellow" id="last_updated">
							<?=date('Y-m-d', strtotime($link->row['last_updated']))?>
						</small>
					</h3>

					<?php
					$info = $link->getURLInfo();
					$url = $info['url'];

					$btnStatusClass = '';
					$btnStatusAction = '';
					$btnStatusCount = 0;
					
					if ($info['http_code'] == 200 && $info['redirect_count'] > 0) {
						if (strpos($info['url'],'?')) {
							$url = substr($info['url'],0,strpos($info['url'],'?'));
							
						}
						$btnStatusAction = 'onClick="updateLink(\''.$url.'\', '.$info['http_code'].');"';
						
					} else {
						$btnStatusCount = $info['redirect_count'];
						if ($btnStatusCount==0) { $btnStatusClass = 'disabled';}
					}
					?>
        </div><!-- /row -->
				<div style="color:red"><?=(isset($errorMessage) && !empty($errorMessage))?$errorMessage:''?></div>
      </div> <!-- /container -->
    </div><!-- /blue -->

    	<div class="container">
      	<div class="row">

					<a class="btn btn-info pull-<?=$handed?>" href="linkedit.php" accesskey="N" id="btnNext"><u>N</u>ext</a>

					<a style="margin-right: 10px" class="btn btn-warning"
						href="https://duckduckgo.com/?q=<?php echo urlencode($link->title);?>&t=ffsb&ia=web"
							target="_srchWindow">Duck</a>

					<a style="margin-right: 10px" class="btn btn-warning" href="<?php echo $link->link;?>" target="_newWindow">Show</a>

					<a style="margin-right: 10px" class="btn btn-warning" id="btnFix">Fix</a>
					
					<a class="btn btn-warning <?=$btnStatusClass?>" href="#" <?=$btnStatusAction?>>Status <sup><?=$btnStatusCount?></sup></a>

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
								<input type="text" class="form-control" id="link" name="link" value="<?php echo $link->link;?>" />
							</div>

							<div class="form-group">
								<label for="tags">Tags:</label>
								<input type="text" class="form-control" id="tags" name="tags" value="<?php echo $link->tags;?>" />
							</div>

							<input type="hidden" id="id" name="id" value="<?php echo $link->id;?>" />
							<input type="hidden" id="created_at" name="last_updated" value="<?php echo isset($link->last_update)?$link->last_updated:date('Y-m-d');?>" />
							
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

	function updateLink(newurl, newstatus) {
		$('#link').val(newurl);
		$('#status').val(newstatus);
		$('#lblTitle').html("Loading ...");
		$('#frmEditLink').submit();
	}

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
		var getHeaderURL = '_functions.php?method=getheader&id=<?=$link->id?>';
		
		$.get(getHeaderURL, function(data) {
			console.log(data);
			$('#last_updated').css('color','orange');
			if (data.status == 'ok') {
				$('#status').val('200');
			}

			// This is just for example
			//for (var key in data.meta){
			//  console.log('Key:' + key + " -> Value:" + data.meta[key]);   
			//}

			if (data.meta['og:title']) {
				$('#title').val(data.meta['og:title']);
			}

			var TagsField = $('#tags').val();
			if (data.meta.keywords) { TagsField = data.meta.keywords; }
			if (data.meta.news_keywords) { TagsField = data.meta.news_keywords; }
			$('#tags').val(TagsField);

			var CreatedAtDate = $('#created_at').val();
			if (data.meta.datePublished) { CreatedAtDate = data.meta.datePublished; }
			if (data.meta.date) { CreatedAtDate = data.meta.date; }
			if (data.meta.published_at) { CreatedAtDate = data.meta.published_at; }
			if (data.meta['article:published_time']) { CreatedAtDate = data.meta['article:published_time']; }
			
			$('#last_updated').html(CreatedAtDate);
			$('#created_at').val(CreatedAtDate);
			
			$('#last_updated').css('color', 'white');
		});
	});
	</script>
</body>
</html>
