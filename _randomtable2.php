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
                <button class="btn btn-danger" onClick="deleteLink(<?=$row[$id]?>);">
									<span class="glyphicon glyphicon-remove"> </span>
								</button>
          	  </td>
          		<td>
          			<b><a href="<?=$row[$link]?>" target="_newWindow"><?=urldecode($row[$title])?></a></b><br />
          			<small><?php echo justHostName($row[$link]);?></small><br />
	                <button class="btn btn-primary" onClick="tagLink(<?=$row[ROW_ID]?>, 'later1') ;">
	                  <span class="glyphicon glyphicon-bookmark"> </span>
	                </button>
					<span style="width: 20px;display: inline-block"> </span>
					
	                <button class="btn btn-secondary" onClick="tagLink(<?=$row[ROW_ID]?>, 'later2') ;">
	                  <span class="glyphicon glyphicon-bookmark"> </span>
	                </button>
					<span style="width: 20px;display: inline-block"> </span>
	                
					<button class="btn btn-info" onClick="tagLink(<?=$row[ROW_ID]?>, 'later3') ;">
	                  <span class="glyphicon glyphicon-bookmark"> </span>
	                </button>
					<span style="width: 20px;display: inline-block"> </span>
	                
					<button class="btn btn-success" onClick="tagLink(<?=$row[ROW_ID]?>, 'later4') ;">
	                  <span class="glyphicon glyphicon-bookmark"> </span>
	                </button>
          		</td>
              <!-- 
							
              <td>
          		  <small><?=date('Y-m-d', strtotime($row[$created_at]))?></small>
          	  </td>
              <td>
          		  <small><?=date('Y-m-d', strtotime($row[$updated_at]))?></small>
          	  </td>
								
	              <td>
	                <a class="btn-lg btn-success" href="linkedit.php?id=<?=$row[ROW_ID]?>" target="_NewWindow">
	                  <span class="glyphicon glyphicon-link"> </span>
	                </a>
	              </td>
								
							-->
								
								
          	</tr>
            <?php
          } else {

          }
				}
				?>
				</table>	