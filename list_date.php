<?php

require_once('_inc.php');
require_once(__DIR__.'/vendor/autoload.php');

//$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/templates');
//$twig = new \Twig\Environment($loader); //, [ 'cache' => '/path/to/compilation_cache' ]);
//$twig = new \Twig\Environment($loader, array('debug' => true));

$app['title'] = "LinkLib";

$sql="SELECT * FROM links WHERE tags = 'curate' ORDER BY updated_at DESC LIMIT 200";
$rs = queryX($sql);

$links = Array();
foreach($rs AS $r) {
	$r['hostname'] = justHostName($r['link']);
	$links[] = $r;
}

if ($_REQUEST['format'] == 'json') {
	header('Content-type: application/json');
	echo json_encode($links);
	exit;
}

$loader = new \Twig\Loader\ArrayLoader([
    'indexTemplate' => file_get_contents('templates/fullpage.html'),
]);
$twig = new \Twig\Environment($loader);

echo $twig->render('indexTemplate', ['name' => 'Fabien']);
exit;

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    {{ include('templates/parts/metatags.html') }}
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
    <title>{{ app.title }}</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <script src="assets/js/modernizr.js"></script>
  </head>

  <body>
    <!-- Fixed navbar -->
    <?php require_once('_menu.php'); ?>

    <div id="blue">
      <div class="container">
        <div class="row">
          <h3>LIST BY DATE.</h3>
        </div><!-- /row -->
      </div> <!-- /container -->
    </div><!-- /blue -->

    <div class="container mtb">
      <div class="row">

        <div class="col-lg-8">
		<div id="alltags">
			<table class="table">
			<?php
			
			$r = query("SELECT YEAR(created_at), MONTH(created_at), count(*) "
				."FROM links "
				."GROUP BY YEAR(created_at), MONTH(created_at) "
				."ORDER BY YEAR(created_at), MONTH(created_at)"
			);
			foreach ($r['rows'] AS $row) {
				echo '<tr><td>'.$row[0].'-'.$row[1].'</td><td>'.$row[2].'</td></tr>';
			}
			?>
			</table>
		</div>
  </div>
  <div class="col-lg-4">
		<h4>Search</h4>
		<div class="hline"></div>
		<p>
		<br/><form action="search.php">
		<input type="text" class="form-control" name="q" placeholder="Search something">
		</form>
		</p>
		<div class="spacing"></div>

		<h4>Categories</h4>
		<div class="hline"></div>
    <a href="search_bytag.php?tag=business" >Business</a><br />
    <a href="search_bytag.php?tag=technology" >Technology</a><br />
    <a href="search_bytag.php?tag=thinking" >Thinking</a><br />
    <a href="search_bytag.php?tag=science" >Science</a><br />
    <a href="search_bytag.php?tag=travel" >Travel</a><br />

		<div class="spacing"></div>
		<div id="recent_posts" ></div>
		<div class="spacing"></div>

		<h4>Special Tags</h4>
		<div class="hline"></div>
		<p id="popular_tags">
      <a href="search_bytag.php?tag=repair" >Repair</a><br />
      <a href="search_bytag.php?tag=fluff" >Fluff</a><br />
      <a href="search_bytag.php?tag=maybe" >Maybe</a><br />
      <a href="search_bytag.php?tag=now" >now</a><br />
		</p>
	</div>
</div><! --/row -->
</div><! --/container -->

	<?php require_once('_footer.php'); ?>

	<?php require_once('_scripts.php'); ?>

	<script>
	</script>

  </body>
</html>
