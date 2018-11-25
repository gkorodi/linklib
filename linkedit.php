<?php
require_once('_includes.php');

if (!isset($_REQUEST['id']) || $_REQUEST['id'] == '') {
	try {
		$queryString = "SELECT id FROM links";
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

$nextidSQLQuery = "SELECT * FROM `links` ORDER BY RAND() LIMIT 100";
$nextid_res = query($nextidSQLQuery);
$nextid = $nextid_res['rows'][rand(0,$nextid_res['rowcount']-1)][0];

$errorMessage = null;
?><!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once('_metatags.php');?>
	<link rel="shortcut icon" href="assets/ico/favicon.ico">
	<title><?php echo APP_TITLE;?></title>
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
    <!-- Fixed navbar -->
    <?php require_once('_menu.php'); ?>

    <!-- *****************************************************************************************************************
    BLUE WRAP
    ***************************************************************************************************************** -->
    <div id="blue">
      <div class="container">
        <div class="row">
          <h3 id="lblTitle"><?php echo $link->title; ?>.</h3>
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

	function updateLink(newurl, newstatus) {
		$('#link').val(newurl);
		$('#status').val(newstatus);
		$('#lblTitle').html("Loading ...");
		$('#frmEditLink').submit();
	}

	$('#btnDelete').on('click', function() {
		$('#lblTitle').html('...');

		$.get('_functions.php?method=deletelink&id=<?php echo $link->id;?>', function(data) {
			console.log(data);

			if (data.status == 'ok') {
				window.location="linkedit.php?id=<?php echo $nextid;?>";
			} else {
				alert(data.message);
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
	});
	</script>
</body>
</html>
