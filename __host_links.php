<div class="container mtb">
	<div class="row">
		<div class="col-lg-12">
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
					?>
						<tr id="row<?php echo $row[0];?>">
						<td>
							<button class="btn btn-sm btn-danger pull-right" onClick="deleteLink(<?php echo $row[0];?>);">
							<span class="glyphicon glyphicon-remove"> </span>
							</button>
						</td>
						<td>
							<b><a href="<?=$row[1]?>" target="_newWindow"><?=urldecode($row[2])?></a></b><br />
							<small><?php
							if (strpos(strtolower($row[5]), 'repair')>-1 || isset($_REQUEST['fullurl'])) {
							echo $row[1];
							}
							?>

							<?php
							foreach(explode(',', $row[5]) AS $tag) {
								echo '<span class="badge">'.$tag.'</span>';
							}
							?>

							</small>
						</td>
						<td>
							<small><?php echo date('Y-m-d', strtotime($row[7]));?></small>
						</td>

						<td>
							<a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
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
