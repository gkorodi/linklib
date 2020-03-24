<?php
require_once('_includes.php');

if (isset($_REQUEST['id']) && $_REQUEST['id']!='' ) {
	$link = new Link($_REQUEST['id']);
} else {
	$linklist = query("SELECT * FROM links WHERE tags = '' OR created_at IS NULL");
	$idx = rand(0,count($linklist['rows'])-1);
	header("Location: ?id=".$linklist['rows'][$idx][0]);
}
?>
<html>
<head>
	<?php require_once('_metatags.php');?>
	<!-- Bootstrap core CSS -->
	<link href="assets/css/bootstrap.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="assets/css/style.css" rel="stylesheet">
	<link href="assets/css/font-awesome.min.css" rel="stylesheet">

	<style>
	tr.stat200 { background-color: lightGreen;}
	tr.stat301 { background-color: pink;}
	</style>
</head>
<body>

	<div class="container">
		<?php
		$info = $link->getURLInfo();
		if (isset($_POST['id'])) {
			$link->link = $_POST['link'];
			$link->title = $_POST['title'];
			$link->status = $info['http_code'];
			$link->tags = $_POST['tags'];

			if (!$link->update()) {
				errorMessage('Could not update link '.$link->id);
				errorMessage(implode('<br />', $link->debugs));
			} else {
				echo "Updated";
			}
		} else {
			// This is just to look up the link, not to change the
			// details on the database.
		}
		
		if (!isset($linklist)) {
			$linklist = query("SELECT * FROM links WHERE tags = '' OR updated_at IS NULL");
		}
		$nextlink = $linklist['rows'][rand ( 1 , count($linklist['rows'])-1 )];

		if ($info['http_code'] == 200 && $info['redirect_count']>0) {
			$link->link = $info['url'];
		}

		if (strpos($link->link,'?')) {
			$link->link = substr($link->link,0, strpos('?', $link->link));
		}
		?>

		<div class="row">
			<a class="btn btn-info pull-right" href="edit.php?id=<?=$nextlink[0]?>">Next</a>

			<a class="btn btn-info pull-right"  style="margin:3px; margin-left: 20px" onClick="repairLink();">Repair</a>

			<a class="btn btn-warning pull-right" style="margin:3px"
			href="https://duckduckgo.com/?q=<?=urlencode($link->title)?>&t=ffsb&ia=web" target="_srchWindow">Duck</a>

			<a class="btn btn-danger pull-right" onClick="deleteLink(<?=$link->id?>);">Delete</a>
		</div>

		<form id="frmEditLink" method="POST">

		        <div class="form-group">
		        <a href="<?=$link->link?>" target="_newWindow"><h3><?=$link->title?></h3></a>
		        </div>

						<input type="hidden" name="id" value="<?=$link->id?>" />

		        <div class="form-group">
		          <label for="link">link</label>
		          <input type="text" class="form-control" id="link" placeholder="URL of the link" name="link" value="<?php echo $link->link;?>" />
		        </div>

		        <div class="form-group">
		          <label for="title">title</label>
		          <input type="text" class="form-control" id="title" placeholder="Title of the link" name="title" value="<?php echo $link->title;?>" />
		        </div>


		        <div class="form-group">
		          <label for="tags">tags</label>
		          <input type="text" class="form-control" id="tags" placeholder="Tags of the link" name="tags" value="<?php echo $link->tags;?>" />
		        </div>

		        <div class="form-group">
		          <label for="status">status</label>
		          <input type="text" class="form-control" id="status" placeholder="Status of the link" name="status" value="<?php echo $link->status;?>" />
		          <!--<small id="statusHelp" class="form-text text-muted">...</small>-->
		        </div>
						<button class="btn btn-success pull-left">Update</button>
		</form>
		
		<br />
			<textarea rows=20 cols=150><?php echo str_replace("\n\n","\n", str_replace('  ',' ',$link->content)); ?></textarea>
	</div>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>


	<script>
	function useURL(url, code) {
		$('input[name="link"]').val(url);
		$('input[name="status"]').val(code);
	}

	function deleteLink(id) {
		$.getJSON( "linkdelete.php?id="+id, function( data ) {
			if (data.status == 'ok') {
				window.location='edit.php?id=<?php echo $nextlink[0];?>';
			} else {
				alert("Could notn delete link: "+data.message);
			}
		});
	}

	function repairLink() {
		$('input[name="tags"]').val('repair');
		$('#frmEditLink').submit();
	}

	function advanceTo(id) {
		window.location='?id='+id;
	}

	</script>
</body>
</html>
