<style>
	TR.link-301 {
		background-color: lightGray;
	}
	TR.link--2 {
		background-color: Purple; color: white;
	}
	TR.link-2 {
		background-color: Pink; color: white;
	}
</style>

<div class="container mtb">
	<div class="row">
		<div class="col-sm-12">
			<?php
			if (!isset($_REQUEST['host'])) {
			?>
			<h3>Really?</h3>
			<p>Give me a host</p>
			<form method="GET">
			<input type="text" name="host" />
			<button class="btn btn-success">Search</button>
			</form>
			<?php
			} else {
				$id = 0;
				$link = 1;
				$title = 2;
				$status = 3;
				$tags = 4;
				$created_at = 5;
				$updated_at = 6;
				
				?>
				<table class="table">
					<thead>
						<th></th>
						<th></th>
						<th>UpdatedAt</th>
						<th></th>
					</thead>
				<tbody>
					<?php
					foreach($resultset['rows'] AS $row) {
						//$link = new Link($row);
					?>
						<tr class="link-<?=$row[$status]?>" id="row<?=$row[$id]?>">
						<td>
							<button class="btn btn-sm btn-danger pull-right" onClick="deleteLink(<?=$row[$id]?>);">
							<span class="glyphicon glyphicon-remove"> </span>
							</button><small><?=($row[$status]==200?'':$row[$status])?></small>
						</td>
						<td>
							<b><a href="<?=$row[$link]?>" target="_newWindow"><?=urldecode(empty($row[$title])?'No Title :(':$row[$title])?></a></b><br />
							<small><?=$row[$link]?><br />
							<?php
							foreach(explode(',', $row[$tags]) AS $tag) {
								echo '<span class="badge">'.$tag.'</span>';
							}
							?>
							</small>
						</td>
						<td>
							<small><?=date('Y-m-d', strtotime($row[$created_at]))?></small>
						</td>
						<td>
							<a class="btn btn-sm btn-info" href="linkedit.php?id=<?=$row[$id]?>" target="_winEditLink">
							<span class="glyphicon glyphicon-ok"> </span>
							</a>
						</td>
						</tr>
						<?php
						}
					}
					?>
				</tbody>
				</table>
		</div>
	</div>
</div><! --/container -->
