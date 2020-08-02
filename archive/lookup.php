<?php

/*

Lookup links, based on the criteria passed in, as a string,
part of the title column. No other columns are consulted in
the query.

*/
require_once('_includes.php');
?><!DOCTYPE html>
<html lang="en">
<head>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
	<script   src="http://code.jquery.com/jquery-2.2.4.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

	<style>
	tr.stat200 { background-color: lightGreen;}
	tr.stat301 { background-color: pink;}
	tr.statempty { background-color: #f0f0f0}
	tr.statNULL { background-color: yellow;}
	</style>
</head>
<body>
	<div class="container">
	<?php
	if (isset($_REQUEST['q'])) {
	// This is when we DO have a criteria

	$criteria = '';
	if (isset($_REQUEST['status'])) {
		$criteria .= ' AND status = '.$_REQUEST['status'];
	}

	if (isset($_REQUEST['notstatus'])) {
		$criteria .= ' AND status != '.$_REQUEST['notstatus'];
	}
	if (isset($_REQUEST['notabs'])) {
		$criteria .= ' AND tabs IS NULL';
	}

	if (isset($_REQUEST['emptytabs'])) {
		$criteria .= " AND tabs = ''";
	}

	if (isset($_REQUEST['tabs'])) {
		$a = Array();
		foreach(explode(',', $_REQUEST['tabs']) AS $tab) {
			array_push($a, "'".str_replace(' ','',$tab)."'");
		}
		$criteria .= " AND INSTR(`column`, '{$needle}') > 0"; "(".implode(',', $a).")";
	}

	$queryString = "SELECT * FROM links WHERE UCASE(title) LIKE '%".strtoupper(urldecode($_REQUEST['q']))."%' ".$criteria." ORDER BY updated_at LIMIT 100";
	?>Query: <pre><?php echo $queryString;?></pre><?php
	$entryList = query($queryString);
} else {
	// This is when we DO NOT have a criteria
	?>
	<form method="GET">
		<input type="text" name="q" /><br /><br />
		<button class="btn btn-info">Submit</button>
	</form>
	<?php
	return;
}

echo "<table class='table'>";
foreach($entryList['rows'] AS $row){
	?>
	<tr class="stat<?php echo ($row[3]===''?'empty':($row[3]==null?'NULL':$row[3]))?>">
		<td><a href="<?php echo $row[1]?>" target="_newWin"><?php echo $row[2]?></a><br />
			<small><?php echo ($row[5] != '' || $row[5] != null?$row[5]:'none')?></small></td>
		<td><?php echo ($row[4] != '' || $row[4] != null?$row[4]:'empty')?></td>

		<td><button onclick="window.location='delentry.php?id=<?php echo $row[0]?>';" class="button">Delete</button> </td>
		<td><a href="edit.php?id=<?php echo $row[0]?>"
			target="_editWin">...</a></td>
	</tr>
	<?php
}
echo "</table>";
?>
</div>
	</body>
		</html>
