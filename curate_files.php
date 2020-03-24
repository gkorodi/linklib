<?php
require_once('_includes.php');

$objectCount = count(glob('data/*.json'));

?><!DOCTYPE html>
<html lang="en">
<head>
	<?php require_once('_metatags.php');?>
	<link rel="shortcut icon" href="assets/ico/favicon.ico">
	<title><?php echo APP_TITLE;?></title>
	<!-- Bootstrap core CSS -->
	<link href="assets/css/bootstrap.css" rel="stylesheet">
	<!-- Custom styles for this template -->
	<link href="assets/css/style.css" rel="stylesheet">
	<link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" />

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
	<?php require_once('_menu.php'); ?>

	<div id="blue">
		<div class="container">
			<div class="row">
				<h3>Curate <div id="filecount" style="display: inline"><?php echo $objectCount;?></div> Files</h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->


	<div class="container mtb">
				<?php
				$tmparr = Array();
				foreach (glob('data/*.json') AS $filename) {
					$raw = file_get_contents($filename);
					if (substr($raw,0,1)!='{') { continue;}
					$row = json_decode($raw);
					$row = (object) array_merge( (array)$row, array( 'filename' => basename($filename, '.json') ) );
					$tmparr[$row->last_updated.'-'.$row->link] = $row;
				}
				?>
			<div class="row">
			<table class="table" id="tableLinks">
			<thead>
				<tr>
					<th>Link</th>
					<th>Host</th>
					<th>Last Updated</th>
					<th> </th>
					<th> </th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($tmparr AS $row) {
					?>
					<tr id="row<?php echo $row->id;?>">
						<td><b><a href="<?php echo $row->link;?>" target="_newWin">
							<?php echo $row->title;?></a></b><br />
							<small><?php
							foreach(explode(',', $row->tags) AS $tag) {
								echo '<span class="badge">'.$tag.'</span> ';
							}
							?></small>
						</td>
						<td><?php $a = explode('/',$row->link); echo $a[2];?></td>
						<td><?php echo $row->last_updated;?></td>
						<td>
							<button class="btn btn-info" onclick="savefile('<?php echo $row->filename;?>');">
								<span class="glyphicon glyphicon-ok"> </span>
							</button>
						</td>
						<td>
							<button class="btn btn-danger pull-right" onclick="delfile('<?php echo $row->filename;?>');">
								<span class="glyphicon glyphicon-remove"> </span>
							</button>
						</td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		</div>
	</div>

	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
	<script>
	$(document).ready(function() {
		$('#tableLinks').DataTable();
	});

	function updatecount() {
		$('#filecount').html('...');
		$.ajax({
			type: 'GET',
			url: '_functions.php',
			dataType: 'json',
			data: {
				"method":"getfilecount"
			},
			success: function(respObj) {
				if (respObj.status == 'ok') {
					$('#filecount').html(respObj.value);
				} else {
					$('#filecount').html('??');
				}
			}
		});
	}

		function delfile(filename) {
			console.log('delfile('+filename+')');
			$('#row'+filename).css('background-color','orange');

			$.ajax({
				type: 'GET',
				url: '_functions.php',
				dataType: 'json',
				data: {
					"method":"delfile",
					"filename":'data/'+filename+'.json'
				},
				success: function(respObj) {
					console.log(respObj);
					if (respObj.status == 'ok') {
						$('#row'+filename).hide();
						updatecount();
					} else {
						$('#row'+filename).css('background-color','red');
					}
				}
			});
		}

		function savefile(filename) {

			$('#row'+filename).css('background-color','orange');
			$.ajax({
				type: 'GET',
				url: '_functions.php',
				dataType: 'json',
				data: {
					"method":"savefile",
					"filename":filename
				},
				success: function(respObj) {
					if (respObj.status == 'ok') {
						$('#row'+filename).hide();
						updatecount();
					} else {
						$('#row'+filename).css('background-color','red');
					}
				},
				error: function(respObj) {
					alert(respObj);
				}
			});
		}
	</script>
</body>
</html>
