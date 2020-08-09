<?php
require_once('../_inc.php');

$categories['empty'] = 0;
$categories['NULL'] = 0;
$r = query("SELECT tags FROM links WHERE tags != ''");
foreach ($r['rows'] AS $row) {
  if ($row[0]===null) { $categories['NULL']++; continue;}
  if ($row[0]==='') { $categories['empty']++; continue;}

  $cats = explode(',', $row[0]);
  foreach($cats AS $category) {
    $c = trim($category);
    if (isset($categories[$c])) {
      $categories[$c]++;
    } else {
      $categories[$c] = 1;
    }
  }
}

arsort($categories);
if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
  header('Content-type: application/json');
  echo print_r(json_encode($categories),true);
} else {
  header('Content-type: text/plain');
  echo 'Category,Count'.PHP_EOL;
  foreach($categories AS $category => $count) {
    echo $category.','.$count.PHP_EOL;
  }
}
?>
