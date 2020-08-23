<?php
require_once(__DIR__.'/../_includes.php');

$categories['empty'] = 0;
$categories['NULL'] = 0;

$rows = queryX("SELECT tags FROM links");
foreach ($rows AS $row) {
  if ($row['tags']===null) { $categories['NULL']++; continue;}
  if (empty($row['tags'])) { $categories['empty']++; continue;}

  foreach(explode(',', $row['tags']) AS $category) {
    $c = trim($category);
	
    if (isset($categories[$c])) {
      $categories[$c]++;
    } else {
      $categories[$c] = 1;
    }
  }
}

var_dump($categories);
exit;

arsort($categories);
if (isset($_REQUEST['format']) && $_REQUEST['format'] === 'json') {
  
} else {

  header('Content-type: text/plain');
  echo 'Category,Count'.PHP_EOL;
  foreach($categories AS $category => $count) {
    echo $category.','.$count.PHP_EOL;
  }
}

