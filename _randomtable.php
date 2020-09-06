        <table class="table">
				<?php
				foreach ($resultset['rows'] AS $row) {
					//$row = $resultset['rows'][$itemidx];
					//$link = new Link($row[0]);
					$id = 0;
					$link = 1;
					$title = 2;
					$status = 3;
					$tags = 4;
					$created_at = 5;
					$updated_at = 6;

					//
          if (isset($_SESSION['role']) && $_SESSION['role'] == 'USER') {
            ?>
            <tr id="row<?=$row[$id]?>">
              <td>
								
          			<b><a href="<?=$row[$link]?>" target="_newWindow"><?=urldecode($row[$title])?></a></b><br />
          			<small><?php echo justHostName($row[$link]);?></small>
          		</td>

              <td>
                <?php foreach(explode(',', $row[$tags]) AS $tag) {
                    ?><span class="badge"><?=$tag?></span> <?php
                }?>
          	  </td>

              <td>
                <?php echo date('Y-m-d', strtotime($row[$updated_at]));?>
              </td>
          	</tr>
            <?php
          } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN') {
            ?>
            <tr id="row<?=$row[$id]?>">
              <td>
                <button class="btn btn-danger" onClick="deleteLink(<?=$row[$id]?>);"><span class="glyphicon glyphicon-remove"> </span></button>
          	  </td>
          		<td>
          			<a href="<?=$row[$link]?>" target="_newWindow"><?=urldecode($row[$title])?></a><br />
          			<small><?php echo justHostName($row[$link]);?></small>
          		</td>
          		<td>
          		    <input type="text" id="tags<?=$row[$id]?>"
                    onChange="repairLink(<?=$row[$id]?>, $(this).val());" value="<?=$row[$tags]?>" />
          	  </td>
              <td>
          		  <?php echo date('Y-m-d', strtotime($row[$updated_at]));?>
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
				</table>	