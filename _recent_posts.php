<h4>Recent Posts</h4>
<div class="hline"></div>
<ul class="popular-posts">
<?php
	$posts = query('SELECT * FROM links ORDER BY created_at DESC LIMIT 5');
	foreach ($posts['rows'] AS $post) {
		?><li>
		    <a href="<?php echo $post[1];?>"><?php echo $post[2];?></a>
		    <em>Posted on <?php echo $post[4];?></em>
		</li><?php
	}
?>
</ul>
