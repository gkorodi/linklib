<?php require_once('_includes.php');?><!DOCTYPE "html">
<html>
<head>
	<link href="assets/css/bootstrap.css" rel="stylesheet">
	<script src="assets/js/jquery-1.12.4.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container">
		<?php
		date_default_timezone_set('US/Eastern');

		$files = glob("/Users/gabor/Desktop/*.webloc");
		if (count($files)===0) {
			echo 'There are no files to process.';
		}
		foreach($files as $fileName) {

			$rec['stats'] = date('Y-m-d H:i:s', stat($files[0])[9]);
			$rec['title'] = urlencode(basename($files[0],'.webloc'));
			$obj = simplexml_load_string(file_get_contents($files[0]));
			$rec['link'] = $obj->dict->string.'';
			$rec['status'] = '200';
			$rec['nextid'] = '-1';
			?>
			<form id="frmFileUpdate" method="post" name="frmFileUpdate">
				<input type="hidden" name="filename" value="<?php echo urlencode($files[0]);?>">
				<table class="table">
					<tbody>
						<tr>
							<th>
								link
							</th>
							<td>
								<input size="100%" type="text" value="<?php echo $rec['link'];?>" name="link">
							</td>
						</tr>
						<tr>
							<th>
								title
							</th>
							<td>
								<input size="100%" type="text" value="<?php echo $rec['title'];?>" name="title">
							</td>
						</tr>
						<tr>
							<th>
								status
							</th>
							<td>
								<input size="100%" type="text" value="<?php echo $rec['status'];?>" name="status">
							</td>
						</tr>
						<tr>
							<th>
								last_updated
							</th>
							<td>
								<input size="100%" type="text" value="<?php echo $rec['stats'];?>" name="last_updated">
							</td>
						</tr>
						<tr>
							<th>
								tags
							</th>
							<td>
								<input size="100%" type="text" name="tags">
							</td>
						</tr>
					</tbody>
				</table><input type="submit" value="Update" class="btn btn-info">
			</form>
			<?php
		}
		?>
	</div>

<script>
$('#frmFileUpdate').on('submit', function() {

	// Assign handlers immediately after making the request,
	// and remember the jqxhr object for this request
	var jqxhr = $.post( "update_file.php", $(this).serialize(), function(data) {
		console.log('pre');
		console.log(data);

	})
	  .done(function(data) {
			console.log('done');
			console.log(data);
	  })
	  .fail(function(data) {
			console.log('fail');
			console.log(data);
	  })
	  .always(function(data) {
			console.log('always');
			console.log(data);
	});

	// Perform other work here ...

	// Set another completion function for the request above
	jqxhr.always(function(data) {
		console.log('ALWAYS');
		console.log(data);
	});
});
</script>
</body>
</html>
