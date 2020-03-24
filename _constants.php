<?php
date_default_timezone_set('US/Eastern');

define('APP_ROOT','/linklib/');
define('APP_TITLE','linkLIB');

define('ROW_ID',0);
define('ROW_LINK',1);
define('ROW_TITLE',2);
define('ROW_STATUS',3);
define('ROW_TAGS',4);
define('ROW_CREATED_AT',5);
define('ROW_UPDATED_AT',6);
define('ROW_DESCRIPTION',7);

require_once('/opt/config/vars');

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



