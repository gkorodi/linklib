<h4>Recent Posts</h4>
<div class="hline"></div>
<ul class="popular-posts">
<?php
	$posts = query('SELECT * FROM links ORDER BY last_updated DESC LIMIT 5');
	foreach ($posts['rows'] AS $post) {
		?><li>
		    <a href="<?=$post[1]?>"><?=$post[2]?></a>	
		    <em>Posted on <?=$post[4]?></em>
		</li><?php
	}
?>
</ul>

