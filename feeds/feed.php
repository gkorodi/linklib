<?php
require_once('_includes.php');

function getFeedItems($feedName) {
  $a = Array();
  $debugs = Array();

  foreach(glob('Feeds/feed_'.$feedName.'_*.xml') as $filename) {
    $xmlstr = file_get_contents($filename);
    if (substr($xmlstr,0,1) != '<') {
      array_push($debugs, "No an XML format.");
    } else {
      $feedObj = new SimpleXMLElement($xmlstr);
      if (isset($feedObj->entry)) {
        foreach($feedObj->entry AS $entry) {
          $entry['content'] = '';

          $record['title'] = $entry->title;
          $record['url'] = '';
          $record['link'] = $entry->link['href'];
          $record['published'] = '';
          $record['author'] = print_r($entry->author->name, true);
          //$record['details'] = print_r($entry, true);

          array_push($debugs, $record);
        }
      } elseif (isset($feedObj->item)) {
        array_push($debugs, "simple_item");
      } elseif (isset($feedObj->channel->item)) {
        $record = Array();
        foreach($feedObj->channel->item AS $entry) {
          $record['title'] = $entry->title.'';
          $record['link'] = $entry->link.'';
          $record['published'] = $entry->pubDate.'';
          $record['author'] = $entry->author['name'];
          $record['category'] = count($entry->category);
          //$record['details'] = print_r($entry, true);
          array_push($debugs, $record);
        }

      } else {
        //array_push($debugs, 'Unknown repeating element.'.print_r($feedObj,true));
      }
    }
  }
  return $debugs;
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
				<a href="?">Feeds</a> >> <h3><?php echo $sectionTitle; ?></h3>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /blue -->

	 <div class="container">
	 	<div class="row">
			<table class="table">
			<tbody>
			<?php
        $feedItems = getFeedItems($_REQUEST['name']);
        asort($feedItems);
				foreach($feedItems AS $item) {
          ?>
          <tr>
          <td colspan="2"><?php echo $item['details'];?></td>
          </tr>

          <tr>
          <td><a href="<?php echo $item['link'];?>" target="_newLinkWindow"><?php echo $item['title'];?></a></td>
          <td><?php echo $item['published'];?></td>
					</tr>
					<?php
				}
			?>
			</tbody>
			</table>
	 	</div><!--/row -->
	 </div><!--/container -->

	<?php require_once('_footer.php'); ?>

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>
	<script>

	$(document).ready(function() {

	});
	</script>
  </body>
</html>
