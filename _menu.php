    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo APP_ROOT;?>">linkLIB</a>
        </div>
        <div class="navbar-collapse collapse navbar-right">
          <ul class="nav navbar-nav">
            <li class="active"><a href="index.php">HOME</a></li>
            <li><a href="stats.php">STATS</a></li>
            <li><a href="load.php">LOAD</a></li>
	    <li><a href="account.php">ACCOUNT</a></li>
            <li><a href="account.php"><?php echo (isset($_SESSION['uid'])?$_SESSION['uid'].'^':'invalid user');?></a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">OPTIONS <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="random.php">RANDOM100</a></li>
                <li><a href="single-post.html">SINGLE POST</a></li>
                <li><a href="portfolio.php">PORTFOLIO</a></li>
                <li><a href="article.php">ARTICLE</a></li>
		<li><a href="logout.php">LOGOUT</a></li>
              </ul>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
