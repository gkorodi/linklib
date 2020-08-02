<?php
require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
$twig = new \Twig\Environment($loader, array('debug' => true));

$settingsList = Array();
foreach(Array('DB_HOST','DB_PORT','DB_USER','DB_PASSWORD', 'DB_NAME') AS $setting) {
  $settingsList[] = ['name'=>$setting, 'value'=>constant($setting)];
}

if (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($settingsList);
	exit;
}

echo $twig->render('settings.html', ['settings' => $settingsList]);


