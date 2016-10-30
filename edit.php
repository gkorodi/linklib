<?php
require_once('_includes.php');
?>
<html>
<head>

	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

	<script   src="http://code.jquery.com/jquery-2.2.4.min.js"   integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="   crossorigin="anonymous"></script>

	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

	<style>
	tr.stat200 { background-color: lightGreen;}
	tr.stat301 { background-color: pink;}
	</style>

</head>
<body>

	<div class="container">
		<?php
		if (isset($_REQUEST['id'])) {
			$link = new Link($_REQUEST['id']);
		} else {

		}

		if (isset($_POST['id'])) {
			echo "Updating link!";

			$link->link = $_POST['link'];
			$link->title = $_POST['title'];
			$info = $link->getURLInfo();
			$link->status = $info['http_code'];
			$link->tags = $_POST['tags'];

			if ($link->update()) {

			} else {
				errorMessage('Could not update link '.$link->id);
				errorMessage(implode('<br />', $link->debugs));
			}
		} else {
			// This is just to look up the link, not to change the
			// details on the database.
		}

		if ($link->refresh()) {
			debugMessage('Refreshed link details for id '.$link->id);
		} else {
			errorMessage('Could not re-query the link for id '.$link->id);
			errorMessage(implode('<br />', $link->debugs));
		}
		?>
		<form method="POST">
			<input type="hidden" name="id" value="<?php echo $link->id;?>" />
			<table class='table'>
				<?php

				$linktopage = ' <a class="btn btn-info" href="'.$link->link.'" target="_newWindow"><b>...</b></a>';
				$linktolookup = '<a href="https://duckduckgo.com/?q='.urlencode($link->title).'&t=ffsb&ia=web" target="_srchWindow"> duckduckgo </a>';
				?>
				<tr>
					<th>link</th>
					<td><input type="text"  id="fldlink" name="link" value="<?php echo $link->link;?>" size="100%"/></td>
				</tr>
				<tr>
					<th>title</th>
					<td><input type="text"  id="fldtitle" name="title" value="<?php echo $link->title;?>" size="100%"/></td>
				</tr>
				<tr>
					<th>status</th>
					<td><input type="text"  id="fldstatus" name="status" value="<?php echo $link->status;?>" size="100%"/></td>
				</tr>
				<tr>
					<th>last updated</th>
					<td><input type="text"  id="fldlastupdate" name="last_updated" value="<?php echo $link->last_updated;?>" size="100%"/></td>
				</tr>
				<tr>
					<th>tags</th>
					<td><input type="text"  id="fldtags" name="tags" value="<?php echo $link->tags;?>" size="100%"/></td>
				</tr>
			</table>
			<button class="btn btn-success">Update</button>
		</form>

		<?php
		$info = $link->getURLInfo();
		?>
		<table class="table">
			<tr>
				<th>URL</th>
				<td><?=$info['url']?></td>
			</tr>
			<tr>
				<th>HTTPCode</th>
				<td><?=$info['http_code']?></td>
			</tr>
			<tr>
				<th>RedirectCount</th>
				<td><?=$info['redirect_count']?></td>
			</tr>
			<tr>
				<th>RedirectURL</th>
				<td><?=$info['redirect_url']?></td>
			</tr>
		</table>
		<button id="btnUserThisURL" onClick="useURL('<?php echo $info['url'];?>');">Use URL</button>
		<hr />

		<a class="btn btn-danger text-center" onClick="deleteLink(<?php echo $link->id;?>);">Delete</a>
		<a class="btn btn-info pull-right" href="edit.php?id=<?php echo $nextLinkId;?>">Next</a><br />
	</div>



	<script>
	function useURL(url) {
		$('#fldlink').val(url);
	}

	function deleteLink(id) {
		$.getJSON( "delById.php?id="+id, function( data ) {
			console.log(data.status+" Deleting <?=$params['id']?>");
			return false;
		});
	}

	function advanceTo(id) {
		window.location='?id='+id;
	}
	</script>
</body>
</html>
