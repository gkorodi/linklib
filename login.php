<?php
require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new \Twig\Environment($loader, array('debug' => true));

$errors = Array();
if (!isset($_SESSION['uid'])) {
	if (!isset($_REQUEST['uid'])) {
		$error[] = 'No UID specified. Need it to verify!';
	} else {
		session_unset();
		$debugs[] = 'Unsetting variables';
		if (in_array($_REQUEST['uid'], explode(',', APP_ADMIN))) {
			$_SESSION['uid'] = $_REQUEST['uid'];
			$_SESSION['role'] = 'ADMIN';
		} elseif (in_array($_REQUEST['uid'], explode(',', APP_USERS))) {
			$_SESSION['uid'] = $_REQUEST['uid'];
			$_SESSION['role'] = 'USER';
		} else {
			$errors[] = 'UID:<b>'.$_REQUEST['uid'].'</b> has no role!';
		}
	}
}

if (isset($_SESSION['uid'])) {
	header('Location: stats.php');
	exit;
}

renderView('login.html', ['errors' => $errors]);
