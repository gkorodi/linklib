<?php require_once('_includes.php'); ?><!DOCTYPE "html">
<html>
<head>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
	<script src="http://code.jquery.com/jquery-2.2.4.min.js" ></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

	<style>
	tr.stat200 { background-color: lightGreen;}
	tr.stat301 { background-color: pink;}
	</style>
</head>
<body>
	<div class="container">
		<?php
		$errors = query('SELECT * FROM links WHERE status != 200 LIMIT 100');
		?>
		<table class='table'>
			<?php
			foreach ($errors['rows'] AS $row) {
				?>
				<tr class="stat<?php echo $row[3];?>">
					<td><a href="<?php echo $row[1];?>" target="_newWin"><?php echo $row[2];?></a></td>
					<td><?php echo $row[4];?></td>
					<td><button onclick="window.location='delentry.php?id=<?php echo $row[0];?>';" class="button">Delete</button> </td>
					<td><a href="edit.php?id=<?php echo $row[0];?>"
						target="_editWin">...</a></td>
				</tr>
				<?php
			}
			?>
		</table>
	</div>
</body>
</html>
