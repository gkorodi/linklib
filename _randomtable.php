        <table class="table">
				<?php
				foreach ($idlist AS $itemidx) {
					$row = $resultset['rows'][$itemidx];

          if (isset($_SESSION['role']) && $_SESSION['role'] == 'USER') {
            ?>
            <tr id="row<?php echo $row[0];?>">
              <td>
          			<b><a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a></b><br />
          			<small><?php echo justHostName($row[1]);?></small>
          		</td>

              <td>
                <?php foreach(explode(',', $row[5]) AS $tag) {
                    ?><span class="badge"><?php echo $tag;?></span> <?php
                }?>
          	  </td>

              <td>
                <?php echo date('Y-m-d', strtotime($row[4]));?>
              </td>

              <!--<td>
                <span class="glyphicon glyphicon-ok"> </span>
          	  </td>
              <td>
                <span class="glyphicon glyphicon-remove"> </span>
          	  </td>-->
          	</tr>
            <?php
          } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN') {
            ?>
            <tr id="row<?php echo $row[0];?>">
          		<td>
          			<a href="<?php echo $row[1];?>" target="_newWindow"><?php echo urldecode($row[2]);?></a><br />
          			<small><?php echo justHostName($row[1]);?></small>
          		</td>
          		<td>
          		    <input type="text" id="tags<?php echo $row[0];?>"
                    onChange="repairLink(<?php echo $row[0];?>, $(this).val());" value="<?php echo $row[5];?>" />
          	  </td>

              <td>
          		  <?php echo date('Y-m-d', strtotime($row[4]));?>
          	  </td>

               <td>
                 <a class="btn btn-sm btn-info" href="linkedit.php?id=<?php echo $row[0];?>" target="_winEditLink">
                   <span class="glyphicon glyphicon-ok"> </span>
                 </a>
               </td>
              <td>
                <button class="btn btn-danger" onClick="deleteLink(<?=$row[0]?>);"><span class="glyphicon glyphicon-remove"> </span></button>
          	  </td>

          	</tr>
            <?php
          } else {

          }
				}
				?>
				</table>	