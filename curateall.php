<?php
require_once('_includes_cli.php');

$rows = query('SELECT * FROM links WHERE tags = "curate" ORDER BY updated_at ASC LIMIT 10');
echo "<h2>CurateList</h2>";

foreach(range(0,9) AS $idx) {
	$link = $rows['rows'][$idx];
	
	$linkDetails = getDetails($link);
	
	$description = getDescription($linkDetails);
	
	if (getNewStatus($linkDetails)!=200) {
		echo 'ALERT! Status has changed from previous! '.
			'Past:'.print_r($link[ROW_STATUS], true).' '.
			'Current:'.print_r(getNewStatus($linkDetails), true).PHP_EOL;
		continue;
	}
	
	?>
	<div class="entryItem">
		<b><?=$link[ROW_TITLE]?></b><br />
		<small><?=$description?></small>
		<hr />
	</div>
	<br />
	<?php
}
?>
