 <?php
require_once('_includes.php');

if (isset($_REQUEST['tag'])) {
	if ($_REQUEST['tag'] == 'empty') {
	        $sql = "SELECT * FROM links WHERE tags IS NULL ".
	      	  	(isset($_REQUEST['notstatus'])?' AND status != '.$_REQUEST['notstatus']:'').
	      		(isset($_REQUEST['status'])?' AND status = '.$_REQUEST['status']:'').
								' ORDER BY created_at '.($_REQUEST['olderfirst']?'ASC':'DESC')
		.' LIMIT 300';

	} else {
		$criteria = [];
		foreach(explode(',', $_REQUEST['tag']) AS $t) {
			$criteria[] = " UPPER(tags) LIKE '%".strtoupper($t)."%' ";
		}
	        $sql = "SELECT * FROM links WHERE ".
				implode(' AND ', $criteria)." ".
	      	  	(isset($_REQUEST['notstatus'])?' AND status != '.$_REQUEST['notstatus']:'').
	      		(isset($_REQUEST['status'])?' AND status = '.$_REQUEST['status']:'').
						' ORDER BY created_at '.(isset($_REQUEST['olderfirst'])?'ASC':'DESC')
							.' LIMIT 300'	;
	}
  $resultset = query($sql);
}
?><!DOCTYPE html>
<html lang="en">
  <head>
	  <?php require_once('_metatags.php');?>
    <link rel="shortcut icon" href="assets/ico/favicon.ico">

    <title><?=APP_TITLE?> - Search By Tag</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
		<link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" />


    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="assets/js/modernizr.js"></script>

	    <style>
	    tr.status200 {
		    background-color: white;
	    }

	    tr.status500 {
		    background-color: red;
		    color: white;
	    }

	    tr.status301 {
		    background-color: orange;
	    }

	    tr.status302 {
		    background-color: orange;
	    }

	    </style>
  </head>

  <body>

	  <?php require_once('_menu.php'); ?>

	<!-- *****************************************************************************************************************
	 BLUE WRAP
	 ***************************************************************************************************************** -->
	<div id="blue">
	    <div class="container">
			<div class="row">
				<h3>Search Results by tag <b><?php echo (isset($_REQUEST['tag'])?$_REQUEST['tag']:'Not Set?! Whaaat?');?></b>.</h3>
				<form><input type="hidden" name="tag" value="<?=$_REQUEST['tag']?>"/>
					<input type="checkbox" <?=isset($_REQUEST['olderfirst'])?'checked':''?> name="olderfirst" onChange="this.form.submit()"> Older First?</form>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container mbt">
	 	<div class="row">
	 		<div class="col-lg-8">
				<?php
				if (!isset($_REQUEST['tag'])) {
					?>
					<h3>Really?</h3>
					<p>Give me a tag</p>
					<form method="GET">
					<input type="text" name="tag" />
					<button class="btn btn-success">Search</button>
					</form>
					<?php
				} else {
					?>
					<table id="tableLinks" class="table">
					<thead>
						
					</thead>
					<tbody>
					<?php
					foreach($resultset['rows'] AS $row) {
						$lst = explode('/',  $row[0]);
						$link = new Link($row[0]);
						?>
						<tr align="top" id="row<?=$link->id?>">
							<td>
								<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN'){?>
									<button class="btn btn-danger" onclick="deleteLink(<?=$link->id?>);">
										<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
									</button>
								<?php }?>
							</td>
							
							<td>
								
							</td>
							<td>
								<b><a href="<?=$link->link?>" target="_newWindow"><?=urldecode($link->title)?></a></b><br />
								<small><?=justHostName($row[ROW_LINK])?></small>
							</td>
							<td>
								<?=empty($link->created_at)?'no date':date("M, Y", strtotime($link->created_at))?>
							</td>
							<?php
							if (isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN') {
								?>
								<td>
									<a class="btn btn-info" href="linkedit.php?id=<?=$link->id?>" target="_winEditLink">
										<span class="glyphicon glyphicon-ok"> </span>
									</a>
								</td><?php
							}
							?>
						</tr>
						<?php
					}
					?>
					</tbody>
					</table><?php
				}
				?>
			</div>
			<div class="col-lg-4">
				<br /><a href="?tag=<?=explode(',', $_REQUEST['tag'])[0]?>">Back</a><br />
				
				<h2>Related Tags</h2>
				<?php
				$tagList = [];
				foreach($resultset['rows'] AS $row) {
					if (empty($row[ROW_TAGS])) { $tagList[] = 'empty'; }
					foreach(explode(',', $row[ROW_TAGS]) AS $tag) {
						$tagList[] = $tag;
					}
				}
				$result = groupBy($tagList);
				ksort($result);
				foreach($result AS $k=>$v) {
					if ($k === $_REQUEST['tag']) { continue; }
					echo '<a href="?tag='.$_REQUEST['tag'].','.$k.'">'.$k.'</a><sup>'.$v.'</sup><br />';
				}
				?>
			</div>
	 	</div>
	 </div>
	 
	 <?php require_once('_footer.php'); ?>
	 <?php require_once('_scripts.php'); ?>
	 
	 <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
  </body>
</html>
