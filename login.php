<?php
require_once('_includes.php');

$errors = Array();
if (isset($_SESSION['uid'])) {
	array_push($errors, 'Already logged in, as '.$_SESSION['uid']);
} else {
	if (!isset($_REQUEST['uid'])) {
		array_push($errors, 'No UID specified. Need it to verify!');
	} else {
		if ($_REQUEST['uid'] === APP_SUPERUSER) {
			$_SESSION['uid'] = $_REQUEST['uid'];
			

			header('Location: index.php');
		} else {
			array_push($errors, 'UID:<b>'.$_REQUEST['uid'].'</b> is not a superuser! <b>'.APP_SUPERUSER.'</b> is!');
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
					<h3>Collect and Curate all the links that fit to be read.</h3>
					<h1>URL Link Library.</h1>
					<h5>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</h5>
					<h5>More Lorem Ipsum added here too.</h5>
				</div>
				<div class="col-lg-8 col-lg-offset-2 himg">
					<form method="POST">
						<input type="text" name="uid" />
						<button class="btn btn-info">Login</button>
					</form>
				</div>
			</div><!-- /row -->
	    </div> <!-- /container -->
	</div><!-- /headerwrap -->

	<!-- *****************************************************************************************************************
	 SERVICE LOGOS
	 ***************************************************************************************************************** -->
	 <div id="service">
	 	<div class="container">
			<?php
			if (count($errors)>0) {
				echo implode('<br />', $errors);
			}
			?>
 			<div class="row centered">
 				<div class="col-md-4">
 					<i class="fa fa-heart-o"></i>
 					<h4>Handsomely Crafted</h4>
 					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
 					<p><br/><a href="#" class="btn btn-theme">More Info</a></p>
 				</div>
 				<div class="col-md-4">
 					<i class="fa fa-flask"></i>
 					<h4>Retina Ready</h4>
 					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
 					<p><br/><a href="#" class="btn btn-theme">More Info</a></p>
 				</div>
 				<div class="col-md-4">
 					<i class="fa fa-trophy"></i>
 					<h4>Quality Theme</h4>
 					<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
 					<p><br/><a href="#" class="btn btn-theme">More Info</a></p>
 				</div>
	 		</div>
	 	</div><! --/container -->
	 </div><! --/service -->

	<!-- *****************************************************************************************************************
	 PORTFOLIO SECTION
	 ***************************************************************************************************************** -->
	 <div id="portfoliowrap">
        <h3>LATEST WORKS</h3>

        <div class="portfolio-centered">
            <div class="recentitems portfolio">
				<div class="portfolio-item graphic-design">
					<div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_09.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Graphic Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_09.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

                <div class="portfolio-item web-design">
                    <div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_02.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Web Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_02.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

                <div class="portfolio-item graphic-design">
                    <div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_03.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Graphic Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_03.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

                <div class="portfolio-item graphic-design">
                    <div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_04.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Graphic Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_04.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

                <div class="portfolio-item books">
                    <div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_05.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Book Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_05.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

                <div class="portfolio-item graphic-design">
                    <div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_06.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Graphic Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_06.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

                <div class="portfolio-item books">
                    <div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_07.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Book Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_07.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

                <div class="portfolio-item graphic-design">
                    <div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_08.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Graphic Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_08.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

                <div class="portfolio-item web-design">
                    <div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_01.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Web Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_01.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

			<div class="portfolio-item books">
                <div class="he-wrap tpl6">
					<img src="assets/img/portfolio/portfolio_10.jpg" alt="">
						<div class="he-view">
							<div class="bg a0" data-animate="fadeIn">
                                <h3 class="a1" data-animate="fadeInDown">A Book Design Item</h3>
                                <a data-rel="prettyPhoto" href="assets/img/portfolio/portfolio_10.jpg" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-search"></i></a>
                                <a href="single-project.html" class="dmbutton a2" data-animate="fadeInUp"><i class="fa fa-link"></i></a>
                        	</div><!-- he bg -->
						</div><!-- he view -->
					</div><!-- he wrap -->
				</div><!-- end col-12 -->

            </div><!-- portfolio -->
        </div><!-- portfolio container -->
	 </div><!--/Portfoliowrap -->


	<!-- *****************************************************************************************************************
	 MIDDLE CONTENT
	 ***************************************************************************************************************** -->

	 <div class="container mtb">
	 	<div class="row">
	 		<div class="col-lg-4 col-lg-offset-1">
		 		<h4>More About Our Agency.</h4>
		 		<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </p>
 				<p><br/><a href="about.html" class="btn btn-theme">More Info</a></p>
	 		</div>

	 		<div class="col-lg-3">
	 			<h4>Frequently Asked</h4>
	 			<div class="hline"></div>
	 			<p><a href="#">How cool is this theme?</a></p>
	 			<p><a href="#">Need a nice good-looking site?</a></p>
	 			<p><a href="#">Is this theme retina ready?</a></p>
	 			<p><a href="#">Which version of Font Awesome uses?</a></p>
	 			<p><a href="#">Free support is integrated?</a></p>
	 		</div>

	 		<div class="col-lg-3">
	 			<h4>Latest Posts</h4>
	 			<div class="hline"></div>
	 			<p><a href="single-post.html">Our new site is live now.</a></p>
	 			<p><a href="single-post.html">Retina ready is not an option.</a></p>
	 			<p><a href="single-post.html">Bootstrap 3 framework is the best.</a></p>
	 			<p><a href="single-post.html">You need this theme, buy it now.</a></p>
	 			<p><a href="single-post.html">This theme is what you need.</a></p>
	 		</div>

	 	</div><! --/row -->
	 </div><! --/container -->

	<!-- *****************************************************************************************************************
	 TESTIMONIALS
	 ***************************************************************************************************************** -->
	 <div id="twrap">
	 	<div class="container centered">
	 		<div class="row">
	 			<div class="col-lg-8 col-lg-offset-2">
	 			<i class="fa fa-comment-o"></i>
	 			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
	 			<h4><br/>Marcel Newman</h4>
	 			<p>WEB DESIGNER - BLACKTIE.CO</p>
	 			</div>
	 		</div><! --/row -->
	 	</div><! --/container -->
	 </div><! --/twrap -->

	<!-- *****************************************************************************************************************
	 OUR CLIENTS
	 ***************************************************************************************************************** -->
	 <div id="cwrap">
		 <div class="container">
		 	<div class="row centered">
			 	<h3>OUR CLIENTS</h3>
			 	<div class="col-lg-3 col-md-3 col-sm-3">
			 		<img src="assets/img/clients/client01.png" class="img-responsive">
			 	</div>
			 	<div class="col-lg-3 col-md-3 col-sm-3">
			 		<img src="assets/img/clients/client02.png" class="img-responsive">
			 	</div>
			 	<div class="col-lg-3 col-md-3 col-sm-3">
			 		<img src="assets/img/clients/client03.png" class="img-responsive">
			 	</div>
			 	<div class="col-lg-3 col-md-3 col-sm-3">
			 		<img src="assets/img/clients/client04.png" class="img-responsive">
			 	</div>
		 	</div><! --/row -->
		 </div><! --/container -->
	 </div><! --/cwrap -->

	<!-- *****************************************************************************************************************
	 FOOTER
	 ***************************************************************************************************************** -->
	 <div id="footerwrap">
	 	<div class="container">
		 	<div class="row">
		 		<div class="col-lg-4">
		 			<h4>About</h4>
		 			<div class="hline-w"></div>
		 			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
		 		</div>
		 		<div class="col-lg-4">
		 			<h4>Social Links</h4>
		 			<div class="hline-w"></div>
		 			<p>
		 				<a href="#"><i class="fa fa-dribbble"></i></a>
		 				<a href="#"><i class="fa fa-facebook"></i></a>
		 				<a href="#"><i class="fa fa-twitter"></i></a>
		 				<a href="#"><i class="fa fa-instagram"></i></a>
		 				<a href="#"><i class="fa fa-tumblr"></i></a>
		 			</p>
		 		</div>
		 		<div class="col-lg-4">
		 			<h4>Our Bunker</h4>
		 			<div class="hline-w"></div>
		 			<p>
		 				Some Ave, 987,<br/>
		 				23890, New York,<br/>
		 				United States.<br/>
		 			</p>
		 		</div>

		 	</div><! --/row -->
	 	</div><! --/container -->
	 </div><! --/footerwrap -->


	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php require_once('_scripts.php'); ?>
	

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
