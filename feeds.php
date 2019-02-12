<?php
require_once('_includes.php');

function getFeedTypeAndVersion($filename) {
  $xmlstr = file_get_contents($filename);
  if (substr($xmlstr,0,1) != '<') {
    return "ERR";
  }
  $feedObj = new SimpleXMLElement($xmlstr);

  return (isset($feedObj['version'])?$feedObj['version']:'N/A');
}

function getFeedStructure($filename) {
  $basename = basename($filename, '.xml');
  list($isFeed, $feedName, $feedDate) = explode('_', $basename);
  $xmlstr = file_get_contents($filename);
  if (substr($xmlstr,0,1) != '<') {
    return "ERR";
  }
  $feedObj = new SimpleXMLElement($xmlstr);
  $structure = Array();
  $structure['name'] = $feedName;
  $structure['filename'] = $filename;

  if (isset($feedObj->channel)) {
    $structure['title'] = $feedObj->channel->title.'';
    $structure['items'] = count($feedObj->channel->item);
  } else {
    $structure['title'] = (isset($feedObj->title)?$feedObj->title.'':'No title');
    $structure['items'] = count($feedObj->entry);
  }
  return $structure;
}

?><!DOCTYPE html>
<html lang="en">
  <head>
	  <?php require_once('_metatags.php');?>
    <link rel="shortcut icon" href="assets/ico/favicon.ico">

    <title><?php echo APP_TITLE;?></title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">


    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="assets/js/modernizr.js"></script>
  </head>

<body>

	<?php require_once('_menu.php'); ?>
	<div id="blue">
		<div class="container">
			<div class="row">
				<h3>Feeds</a></h3>
			</div><!-- /row -->
		</div> <!-- /container -->
	</div><!-- /blue -->

	<div class="container">
		<div class="row">
		<table class="table">
		<tbody>
		<?php
			$feedList = Array();
			foreach(glob('Feeds/feed_*.xml') AS $filename) {
				$feedStructure = getFeedStructure($filename);
				?>
				<tr>
					<td><?php echo $feedStructure['title'];?></td>
					<td><?php echo $feedStructure['items'];?></td>
					<td><a href="feed.php?name=<?php echo $feedStructure['name'];?>"><?php echo $feedStructure['filename'];?></a></td>
				</tr>
			<?php
			}
		?>
		</tbody>
		</table>
		</div><!--/row -->
	</div><!--/container -->

	<?php require_once('_footer.php'); ?>
	<?php require_once('_scripts.php'); ?>
</body>
</html>
