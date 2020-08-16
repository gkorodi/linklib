<?php
require_once('_includes.php');

$debugs = Array();
$errors = Array();

if (isset($_SESSION['uid'])) {
	array_push($errors, 'Already logged in, as '.$_SESSION['uid']);
} else {
	if (!isset($_REQUEST['uid'])) {
		array_push($errors, 'No UID specified. Need it to verify!');
	} else {
		session_unset();
		$debugs[] = 'Unsetting variables';

		if (in_array($_REQUEST['uid'], explode(',', APP_ADMIN))) {
			$debugs[] = 'User <b>'.$_REQUEST['uid'].'</b> is in the ADMIN list <i>'.APP_ADMIN.'</i>';
			$_SESSION['uid'] = $_REQUEST['uid'];
			$_SESSION['role'] = 'ADMIN';
		} elseif (in_array($_REQUEST['uid'], explode(',', APP_USERS))) {
			$debugs[] = 'User <b>'.$_REQUEST['uid'].'</b> is in the USER list <i>'.APP_USERS.'</i>';
			$_SESSION['uid'] = $_REQUEST['uid'];
			$_SESSION['role'] = 'USER';

		} else {
			array_push($errors, 'UID:<b>'.$_REQUEST['uid'].'</b> has no role!');
		}
	}
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

	<!-- *****************************************************************************************************************
	 HEADERWRAP
	 ***************************************************************************************************************** -->
	<div id="headerwrap">
	    <div class="container">
			<div class="row">
				<div class="col-lg-8 col-lg-offset-2">
					<h1>URL Link Library.</h1>
					<h3>Collect and Curate all the links that fit to be read.</h3>
					<br />
					<h5>Enter your designated username:</h5>
				</div>
				<div class="col-lg-8 col-lg-offset-2 himg">
					<form method="POST">
						<input type="text" name="uid" />
						<button class="btn btn-info">Login</button>
					</form>
					<br />
					<br />
					<?=implode("<br />", $errors)?>
				</div>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /headerwrap -->

	<?php require_once('templates/parts/footer.html');?>
	<?php require_once('templates/parts/scripts.js'); ?>


    <script>
// Portfolio
(function($) {
	"use strict";
	var $container = $('.portfolio'),
		$items = $container.find('.portfolio-item'),
		portfolioLayout = 'fitRows';

		if( $container.hasClass('portfolio-centered') ) {
			portfolioLayout = 'masonry';
		}

		$container.isotope({
			filter: '*',
			animationEngine: 'best-available',
			layoutMode: portfolioLayout,
			animationOptions: {
			duration: 750,
			easing: 'linear',
			queue: false
		},
		masonry: {
		}
		}, refreshWaypoints());

		function refreshWaypoints() {
			setTimeout(function() {
			}, 1000);
		}

		$('nav.portfolio-filter ul a').on('click', function() {
				var selector = $(this).attr('data-filter');
				$container.isotope({ filter: selector }, refreshWaypoints());
				$('nav.portfolio-filter ul a').removeClass('active');
				$(this).addClass('active');
				return false;
		});

		function getColumnNumber() {
			var winWidth = $(window).width(),
			columnNumber = 1;

			if (winWidth > 1200) {
				columnNumber = 5;
			} else if (winWidth > 950) {
				columnNumber = 4;
			} else if (winWidth > 600) {
				columnNumber = 3;
			} else if (winWidth > 400) {
				columnNumber = 2;
			} else if (winWidth > 250) {
				columnNumber = 1;
			}
				return columnNumber;
			}

			function setColumns() {
				var winWidth = $(window).width(),
				columnNumber = getColumnNumber(),
				itemWidth = Math.floor(winWidth / columnNumber);

				$container.find('.portfolio-item').each(function() {
					$(this).css( {
					width : itemWidth + 'px'
				});
			});
		}

		function setPortfolio() {
			setColumns();
			$container.isotope('reLayout');
		}

		$container.imagesLoaded(function () {
			setPortfolio();
		});

		$(window).on('resize', function () {
		setPortfolio();
	});
})(jQuery);
</script>
  </body>
</html>
