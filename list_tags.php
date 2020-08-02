 <?php
require_once('_includes.php');
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
    <!-- Fixed navbar -->
    <?php require_once('_menu.php'); ?>

    <div id="blue">
      <div class="container">
        <div class="row">
          <h3>TAG LIST.</h3>
        </div><!-- /row -->
      </div> <!-- /container -->
    </div><!-- /blue -->

    <div class="container mtb">
      <div class="row">

        <div class="col-lg-8">
		<div id="alltags">
			<?php
			#$categories['empty'] = 0;
			#$categories['NULL'] = 0;
			$r = query("SELECT tags FROM links");
			foreach ($r['rows'] AS $row) {

				if ($row[0]===null) { 
					#$categories['NULL']++; 
					continue;
				}
				if ($row[0]==='') { 
					#$categories['empty']++; 
					continue;
				}

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

			foreach($categories AS $category => $count) {
				?><a class="btn btn-theme" href="search_bytag.php?tag=<?=$category?>" 
						role="button" target="_newTagWindow"><?=$category?><sup><?=$count?></sup></a>
				<?php
			}
			?>
		</div>
  </div>
  <div class="col-lg-4">
		<h4>Search</h4>
		<div class="hline"></div>
		<p>
			<form action="list_selected_tags.php">
				<input type="text" class="form-control" name="q" placeholder="Search for a specific tag">
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
	      <a href="search_bytag.php?tag=Level1" >Level1</a><br />
	      <a href="search_bytag.php?tag=Level2" >Level2</a><br />
	      <a href="search_bytag.php?tag=Level3" >Level3</a><br />
	      <a href="search_bytag.php?tag=Level4" >Level4</a><br />
		</p>
	</div>
</div><! --/row -->
</div><! --/container -->

	<?php require_once('_footer.php'); ?>

	<?php require_once('_scripts.php'); ?>

  </body>
</html>
