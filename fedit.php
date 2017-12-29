<?php require_once('_includes.php');?><!DOCTYPE "html">
<html>
<head>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
	<script src="http://code.jquery.com/jquery-2.2.4.min.js"  crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" crossorigin="anonymous"></script>
</head>
<body>
	<div class="container">
		<?php
		date_default_timezone_set('US/Eastern');

		$files = glob("/Users/gaborkorodi/Desktop/*.webloc");
		$rec['stats'] = date('Y-m-d H:i:s', stat($files[0])[9]);
		$rec['title'] = urlencode(basename($files[0],'.webloc'));
		$obj = simplexml_load_string(file_get_contents($files[0]));
		$rec['link'] = $obj->dict->string.'';
		$rec['status'] = '200';
		$rec['nextid'] = '-1';
		?>
		<form id="frmFileUpdate" method="POST">
			<input type="hidden" name="filename" value="<?php echo urlencode($files[0]);?>" />
<table class="table">
	<tbody>
	<tr>
		<th>id</th>
		<td>NULL</td>
	</tr>
	<tr>
		<th>link</th>
		<td><input size="100%" type="text" value="<?php echo $rec['link'];?>" name="link" /></td>
	</tr>
	<tr>
		<th>title</th>
		<td><input size="100%" type="text" value="<?php echo $rec['title'];?>" name="title" /></td>
	</tr>
	<tr>
		<th>status</th>
		<td><input size="100%" type="text" value="<?php echo $rec['status'];?>" name="status" /></td>
	</tr>
	<tr>
		<th>last_updated</th>
		<td><input size="100%" type="text" value="<?php echo $rec['stats'];?>" name="last_updated" /></td>
	</tr>
	<tr>
		<th>tags</th>
		<td><input size="100%" type="text" name="tags" /></td>
	</tr>
	<tr>
		<th>priority</th>
		<td><input size="100%" type="text" value="0" name="priority" /></td>
	</tr>
</tbody>
</table>
<input type="submit" value="Update" class="btn btn-info" />
</form>
</div>

<script>
$('#frmFileUpdate').on('submit', function() {

	// Assign handlers immediately after making the request,
	// and remember the jqxhr object for this request
	var jqxhr = $.post( "update_file.php", $(this).serialize(), function(data) {

	})
	  .done(function(data) {
	  })
	  .fail(function(data) {
	  })
	  .always(function(data) {
	});

	// Perform other work here ...

	// Set another completion function for the request above
	jqxhr.always(function(data) {
	});
});
</script>
</body>
</html>
