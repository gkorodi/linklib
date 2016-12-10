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
            <li><a href="about.php">ABOUT</a></li>
            <li><a href="contact.php">CONTACT</a></li>
            <?php
            if (isset($_SESSION['uid'])) {
              ?>
              <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo strtoupper($_SESSION['uid']);?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="random.php">RANDOM100</a></li>
                <li><a href="single-post.html">SINGLE POST</a></li>
                <li><a href="portfolio.php">PORTFOLIO</a></li>
		
                <li><a href="linkedit.php">RANDOMLINK</a></li>
                <li><a href="list_hosts.php">LIST HOSTS</a></li>
                <li><a href="list_tags.php">LIST TAGS</a></li>
                <li>---</li>
		<li><a href="settings.php">SETTINGS</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
              </ul>
              <?php
            } else {
              ?>
              <li>
                <li><a href="login.php">LOGIN</a></li>
              <?php
            }
            ?>
            </li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
