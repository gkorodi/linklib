<?php
$header = Array();
if (function_exists('apache_request_headers')) {
	$header = apache_request_headers(); 
} 

/*foreach (getallheaders() as $name => $value) {
    switch($name) {
    	case 'Authorization':
		$a = explode(' ', $value);
		if (validToken($a[1])) {
			$_SESSION['uid'] = $a[1];
		}
		break;
	default:
    }
}*/


if (!isset($header['Authorization']) || $header['Authorization'] != 'testToken') {
	if (isset($_SERVER['PHP_SELF'])) {
		session_start();
		if (!in_array(basename($_SERVER['PHP_SELF']), explode(',','login.php,index.php')) && !isset($_SESSION['uid'])) {
			header("Location: login.php");
			exit;
		}
	} else {
	  echo 'DEBUG: No PHP_SELF, this must be CLI'.PHP_EOL;
	}
}

date_default_timezone_set('US/Eastern');
define('APP_ROOT','/linklib/');
define('APP_TITLE','linkLIB');
$context['app_title'] = APP_TITLE;

define('APP_ADDRESS', '<h4>Our Bunker</h4>'.
'<div class="hline-w"></div>'.
'<p>'.
	'Boston, MA<br/>'.
	'London, UK<br />'.
	'Budapest, Hungary'.
'</p>');
define('APP_SOCIAL_LINKS','<h4>Social Links</h4><div class="hline-w"></div>'.
'<p>'.
	'<a href="https://www.linkedin.com/in/gaborkorodi" target="_newWindow"><i class="fa fa-linkedin"></i></a>'.
	'<a href="https://twitter.com/korodigabor" target="_newWindow"><i class="fa fa-twitter"></i></a>'.
	'<a href="https://gaborkorodi.wordpress.com" target="_newWindow"><i class="fa fa-globe"></i></a>'.
'</p>');
define('FEED_DIR','data');

define('ROW_ID',0);
define('ROW_LINK',1);
define('ROW_TITLE',2);
define('ROW_STATUS',3);
define('ROW_TAGS',4);
define('ROW_CREATED_AT',5);
define('ROW_UPDATED_AT',6);
define('ROW_DESCRIPTION',7);

require_once('conf/vars');

$context['server'] = $_SERVER;
$context['session'] = $_SESSION;
$context['request'] = $_REQUEST;

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

function renderView($filename, $pageContentDetails) {
	global $context;
	global $twig;
	echo $twig->render(
		$filename, 
		array_merge(Array('context' => $context), $pageContentDetails)
	);
}

function validToken($token) {
	return true;
}

function getRowDescription($record) {
	$obj = json_decode($record[ROW_DESCRIPTION]);
	if (isset($obj->{'og:description'})) return $obj->{'og:description'};
	return 'No Description';
}


function groupBy($arr) {
	$ret = Array();
	foreach($arr AS $e) {
		if (isset($ret[$e])) {
			$ret[$e]++;
		} else {
			$ret[$e] = 1;
		}
	}
	return $ret;
}

function queryX($sql) {
	$response = Array();
	$conn = new mysqli(DB_HOST.':'.DB_PORT, DB_USER, DB_PASSWORD, DB_NAME);
	if ($conn->connect_errno) {
		$response['status'] = 'error';
		$response['message'] = $mysqli->connect_error;
	} else {
		$rs = $conn->query($sql);
		if($rs && $rs->num_rows>0){
		    $response = $rs->fetch_all(MYSQLI_ASSOC);
		}
	}
	$conn->close();
	return $response;
}

function getLevel($tags) {
	if (empty($tags)) { return 0;}
	if (strpos(strtolower($tags),'evel1')>0) { return 1;}
	if (strpos(strtolower($tags),'evel2')>0) { return 2;}
	if (strpos(strtolower($tags),'evel3')>0) { return 3;}
	if (strpos(strtolower($tags),'evel4')>0) { return 4;}
	if (strpos(strtolower($tags),'evel5')>0) { return 5;}
	return "?";
}


function getLinkStatus($url) {

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	$json = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);

	return $info['http_code'];
}

function showRowSkinny($row) {
	?>
	<tr id="row<?php echo $row[0];?>">
		<td> </td>
		<td>
			<a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a><br />
			<small><?php echo justHostName($row[1]); ?></small>
		</td>
		<td>
			<small><?php echo date('Y-m-d', strtotime($row[4]));?></small>
		</td>
		<td>
			<?php
			foreach(explode(',', $row[5]) AS $tag) { ?>
				<span class="badge"><?php echo $tag;?></span>
			<?php
			}
			?>
			<button class="btn btn-sm" onClick="checkDetails(<?php echo $row[0];?>);">
				<span class="glyphicon glyphicon-plus"> </span>
			</button>
		</td>
		<td>
			<button class="btn btn-sm btn-danger pull-right" onClick="deleteLink(<?php echo $row[0];?>);">
				<span class="glyphicon glyphicon-remove"> </span>
			</button>
		</td>
		<td>

			<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
				<span class="glyphicon glyphicon-ok"> </span>
			</a>
		</td>

	</tr>
	<?php
}
function showRow($row) {
	?>
	<tr id="row<?php echo $row[0];?>">
		<td> </td>
		<td>
			<a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a><br />
			<small><?php echo justHostName($row[1]);?></small>
		</td>
		<td>
			<input type="text" id="tags<?php echo $row[0];?>" onChange="repairLink(<?php echo $row[0];?>, $(this).val());" value="<?php echo $row[5];?>" />
	</td>
	<td>
		<?php echo date('Y-m-d', strtotime($row[4]));?>
	</td>
	<td>
			<button class="btn btn-sm btn-danger pull-right" onClick="deleteLink(<?php echo $row[0];?>);">
				<span class="glyphicon glyphicon-remove"> </span>
			</button>
</td><td>
			<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
				<span class="glyphicon glyphicon-ok"> </span>
			</a>
		</td>

	</tr>
	<?php
}

function showUserRow($row) {
	?>
	<tr id="row<?php echo $row[0];?>">
		<td><?php echo justHostName($row[1]);?></small></td>
		<td>
			<h4><a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a></h4><br />
			<?php
			foreach(explode(',', $row[5]) AS $tag) { echo '<span class="badge">'.$tag.'</span> ';}
			 ?></small>
		</td>
	<td>
		<?php echo date('Y-m-d', strtotime($row[4]));?>
	</td>
	</tr>
	<?php
}


function query($sql) {
	// Examples from: http://www.pontikis.net/blog/how-to-use-php-improved-mysqli-extension-and-why-you-should
	// and some other from: http://www.pontikis.net/blog/how-to-write-code-for-any-database-with-php-adodb
	$errors = Array();
	$response['sql'] = $sql;

	$conn = new mysqli(DB_HOST.(array_key_exists('DB_PORT', get_defined_vars())?':'.DB_PORT:''), DB_USER, DB_PASSWORD, DB_NAME);
	if ($conn->connect_errno) {
		array_push($errors, "Connect failed: %s\n", $mysqli->connect_error);
	} else {
		$rs = $conn->query($sql);
		if($rs === false) {
		  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
		} else {
		  $response['rowcount'] = $rs->num_rows;
		}

		$response['rows'] = Array();
		$rs->data_seek(0);
		while($row = $rs->fetch_row()){
			array_push($response['rows'], $row);
		}
		$rs->free();
		$conn->close();
	}
	if (count($errors)>0) {
		$response['messages'] .= implode('<br />', $errors);
	}
	return $response;
}

require_once('class_DBQueryService.php');
require_once('class_Link.php');

function justHostName($url) {
	$a = explode('/', $url);
	if (count($a)<3) {
		return 'Invalid url: '.$url;
	}
	return $a[2];
}
?>
