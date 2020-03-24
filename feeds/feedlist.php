<?php

$allfiles = glob('Feeds/archive/feed*.xml');
$feedItems = Array();

function parsefile($fname) {
  global $feedItems;
  $obj = new SimpleXMLElement(file_get_contents($fname));

  $i['title'] = $obj->channel->item->title.'';
  $i['link'] = $obj->channel->item->link.'';
  $i['last_updated'] = $obj->channel->item->pubDate;
  $i['tags'] = print_r($obj->channel->item->category, true);

  $arrKey = strtotime($obj->channel->item->pubDate).$obj->channel->item->link;
  $feedItems[$arrKey] = $i;
}

$feednameList = Array();
foreach($allfiles AS $filename) {
  $a = explode('_', $filename);
  $feednameList[$a[1]] = 1;
}
?>
<form method="GET" action="" >
  <input type="hidden" name="logic" value="feedlist.php" />
  <select name="name" onchange='this.form.submit()'>
  <?php
  foreach($feednameList AS $k=>$v) {
    echo '<option value="'.$k.'">'.$k.'</option>';
  }
  ?>
  </select>
</form>
<h2>Feed: <?php echo $_REQUEST['name'];?></h2>
<?php

if (isset($_REQUEST['name'])) {
	$feedItems = Array();
  $searchfilePattern = 'Feeds/archive/feed*'.$_REQUEST['name'].'*.xml';
  echo "SearchFilePattern: ${searchfilePattern}<br />";
  $fileList = glob($searchfilePattern);
  echo 'There are '.count($fileList).' file list to parse.<br />';

	foreach($fileList AS $filename) {
    parsefile($filename);
	}
  echo 'There are '.count($feedItems).' items now.';
	ksort($feedItems);
}
echo '----';
echo 'There are '.count($feedItems).' items produced.<br />';
?>
<table class="table">
<?php
foreach($feedItems as $item) {
  ?>
  <tr>
    <td>
      <?php echo date('Y-m-d', strtotime($item['last_updated']));?>
    </td>
    <td>
      <a href="<?php echo $item['link'];?>" target="_newWin"><?php echo $item['title'];?></a><br />
    </td>
    <td>
      <button class="btn btn-sm" onclick="$(this).parent().parent().hide();"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
    </td>
    <td>
      <button class="btn btn-sm"
        onclick="savefeeditem('<?php echo $item['link'];?>','<?php echo str_replace("'"," ", $item['title']);?>','<?php echo date('Y-m-d', strtotime($item['last_updated']));?>');"><span class="glyphicon glyphicon-check" aria-hidden="true"></span></button>
    </td>
  </tr>
  <?php
}
?>
</table>
<script>
function savefeeditem(link,title,last_updated) {
  $.ajax({
    type: "POST",
    url: "_functions.php",
    data: {'method': 'savefeeditem', 'link':link, 'title':title, 'last_updated':last_updated},
    success: successFunction
  });
}

function successFunction(data) {
  console.log(data);
}
</script>
