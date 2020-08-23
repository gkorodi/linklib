<?php
require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

//header('Content-type: application/json');

$rows = queryX("SELECT * FROM links WHERE status = 200 AND tags IS NOT NULL AND level IS NOT NULL AND level > 0 ORDER BY updated_at DESC LIMIT 100");

echo '<table cellspacing="1" cellpadding="5px" width="90%">';
foreach($rows AS $row) {
	
	$bgColor = 'white';
	switch($row['level']) {
		case("1"):
			$bgColor = 'orange';
			break;
		case("2"):
			$bgColor = 'purple';
			break;
		case("3"):
		$bgColor = 'brown';
		break;
		case("4"):
		$bgColor = 'lightGreen';
		break;
		case("5"):
		$bgColor = '#d0d0d0';
		break;
		default:
			$bgColor = 'pink';
	}
	
	
	?>
	<tr style="background-color: <?=$bgColor?>">
	<td>
		<a href="<?=$row['link']?>" target="newLinkWindow"><?=$row['title']?></a>
	</td>
	<td>
		<?=$row['tags']?>
	</td>
	<td>
		<?=date('Y-m-d', strtotime($row['created_at']))?>
	</td>
	<td>
		<a href="" class="btn">Edit</a>
	</td>
	</tr>
	<?php
}
?>
</table>