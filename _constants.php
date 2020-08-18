<?php
date_default_timezone_set('US/Eastern');
define('APP_ROOT','/linklib/');
define('APP_TITLE','linkLIB webapp');

require_once('config/vars');

$skiptagList = Array(
	'og:image:height',
	'og:image:width',
	'msapplication-TileColor',
	'fb:app_id',
	'fb:pages',
	'og:locale',
	'og:site_name',
	'og:image:secure_url',
	'og:image',
	'twitter:site',
	'twitter:card',
	'og:type',
	'twitter:image',
	'rating',
	'apple-mobile-web-app-title',
	'bt:body'
);



