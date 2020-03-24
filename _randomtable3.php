<form id="frmCurate" method="POST" action="#">
<table class="table">
	<?php
	foreach($resultset['rows'] as $row) {
		?>
		<tr>
			<td>
				<input type="checkbox" name="later" value="<?=$row[ROW_ID]?>" />
			</td>
			<td style="background-color: red">
				<input type="checkbox" name="delete" value="<?=$row[ROW_ID]?>"/>
			</td>
			<td>
				<a href="<?=$row[ROW_LINK]?>" target="_newWin"><?=$row[ROW_TITLE]?></a><br />
				<small><?=justHostName($row[ROW_LINK])?></small>
			</td>
			<td>
				<?=date('Y-m-d', strtotime($row[ROW_CREATED_AT]))?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
<button class="btn btn-info">Update</button>
</form>

<script>
	$('#frmCurate').on('submit', function(data) {
		alert("Submitted.....");
	});
</script>