<?php
require_once('_includes.php');

if (isset($_POST['id'])) {
	
	$link = new Link($_REQUEST['id']);
	$link->title = $_POST['title'];
	$link->link = $_POST['link'];
	$link->tags = str_replace(' ','', strtolower($_POST['tags']));
	$link->status = $_POST['status'];
	$link->last_updated = ($_POST['last_updated']?$_POST['last_updated']:date('Y-m-d H:i'));
	$linktags_before = $link->tags;

	if (!$link->update()) {
		$errorMessage = 'Could not updates link!'.'<br />'.implode('<br />', $link->debugs);
	}
	$linktags_after = $link->tags;
} else {
	if (!isset($_REQUEST['id']) || $_REQUEST['id'] == '') {
		try {
			// Get all the links with repair in their tags field
			$queryString = "SELECT id FROM links WHERE LOWER(tags) LIKE '%repair%'";
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
	}
}


if (!$link->id) {
	throw new Exception('There is no link->id field!'.print_r($debug, true));
}

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
	  $info = $link->getURLInfo();
	  if ($errorMessage != null) {
		  echo '<div style="color:red">'.$errorMessage.'</div>';
	  }

	  ?>
      </div> <!-- /container -->
    </div><!-- /blue -->

    <div class="container mtb">
      <div class="row">
				
				<?php
				$url = $info['url'];
				
				$btnStatusStyle='btn-success disabled';
				$btnStatusAction = '';
				$btnStatusCounts = '-1';
				
				if ($info['http_code'] == 200 && $info['redirect_count'] > 0) {
					if (strpos($info['url'],'?')) {
						$url = substr($info['url'],0,strpos($info['url'],'?'));
					}
					$btnStatusStyle = 'btn-warning';
					$btnStatusAction = 'onClick="updateLink(\''.$url.'\', '.$info['http_code'].');"';
					$btnStatusCounts = $info['http_code'].'/'.$info['redirect_count'];
				}
				?>
				<a class="btn <?=$btnStatusStyle?>" 
					href="#" <?=$btnStatusAction?>>Status <sup><?=$btnStatusCount?></sup></a>
					
				<a href="" id="btnDelete" class="btn btn-danger" accesskey="D" ><u>D</u>elete</a>
				
				<a class="btn btn-info" href="repair.php" accesskey="N" id="btnNext"><u>N</u>ext</a>
				
				<br />
				<br />
				
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

	  <div class="form-group">
	    <label for="title">Title:</label>
	    <input type="text" class="form-control"
	    	id="title" name="title" value="<?php echo urldecode($link->title);?>" />
	  </div>

	  <div class="form-group">
	    <label for="status">Status:</label>
	    <input type="text" class="form-control"
	    	id="status" name="status" value="<?php echo $link->status;?>" />
	  </div>

	  <div class="form-group">
	    <label for="last_updated">Last Update:</label>
	    <input type="text" class="form-control"
	    	id="last_updated" name="last_updated" value="<?php echo $link->row['last_updated'];?>" />
	  </div>

	  <input type="hidden" id="id" name="id" value="<?php echo $link->id;?>" />

	  <input type="submit" name="btnUpdate"
	  	id="btnUpdateId" class="btn btn-info pull-right" accesskey="U" value="Update" />
	</form>
	

  	<a class="btn btn-warning"
  		id="btnHeader" href="#">Hdr</a>
	<br />
	<br />
  	<a class="btn btn-warning" href="<?php echo $link->link;?>" target="_newWindow">Show</a>
  	<a class="btn btn-warning"
  		href="https://duckduckgo.com/?q=<?php echo urlencode($link->title);?>&t=ffsb&ia=web"
  		target="_srchWindow">Duck</a>
	<br />
	<br />
	</div>

      </div><! --/row -->
    </div><! --/container -->


	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
</body>
</html>

