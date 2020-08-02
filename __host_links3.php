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

				foreach($resultset['rows'] AS $row) {
				?>
					<div class="row" id="row<?=$row[ROW_ID]?>">
						<hr />
						<div class="col-sm-1">
							<br />
							<button class="btn btn-danger" onClick="deleteLink(<?=$row[ROW_ID]?>);">
								<span class="glyphicon glyphicon-remove"> </span>
							</button>
						</div>

						<div class="col-sm-10">
							<h4><a href="<?=$row[ROW_LINK]?>" target="_newWindow"><?=urldecode(empty($row[ROW_TITLE])?'No Title :(':$row[ROW_TITLE])?></a></h4>
							
							<?=getRowDescription($row)?><br />
							
							<small><?=$row[ROW_LINK]?><br />
							Status: <b><?=$row[ROW_STATUS]?></b>
							Created: <b><?=date('Y-m-d', strtotime($row[ROW_CREATED_AT]))?></b>
							Updated: <b><?=date('Y-m-d', strtotime($row[ROW_UPDATED_AT]))?></b> 
							Tag: <b><?=empty($row[ROW_TAGS])?'n/a':$row[ROW_TAGS]?></b>
							</small>
						</div>
						
						<div class="col-sm-1">
							<a class="btn btn-info" href="linkedit.php?id=<?=$row[ROW_ID]?>" target="_winEditLink">
								<span class="glyphicon glyphicon-ok"> </span>
							</a>
						</div>
					</div>
					<?php
					}
				}
			?>
		</div>
	</div>
</div><! --/container -->
