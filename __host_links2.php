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
        <div class="col-12">
            <table class="table">
                <?php
                foreach($resultset['rows'] AS $row) {
                    ?>
                    <tr id="row<?=$row[ROW_ID]?>">
                        <td><button class="btn-lg btn-danger" onClick="deleteLink(<?=$row[ROW_ID]?>);">
                                <span class="glyphicon glyphicon-remove"> </span>
                            </button></td>
                        <td>
                            <b><a href="<?=$row[ROW_LINK]?>" target="_newWindow"><?=urldecode(empty($row[ROW_TITLE])?'No Title :(':$row[ROW_TITLE])?></a></b><br />
                            <small>
                                <?=json_decode($row[ROW_DESCRIPTION])->description?><br />
                                <?=$row[ROW_LINK]?><br />
                                <?php
                                if (isset($row[ROW_TAGS]) && !empty($row[ROW_TAGS])) {
                                    foreach(explode(',', $row[ROW_TAGS]) AS $tag) {
                                        echo '<span class="badge">'.$tag.'</span>';
                                    }
                                } else {
                                    echo 'NOTAGS';
                                }
                                ?>
                                Created: <b><?=date('Y-m-d', strtotime($row[ROW_CREATED_AT]))?></b> Updated: <b><?=date('Y-m-d', strtotime($row[ROW_UPDATED_AT]))?></b>
                            </small>

                        </td>
                        <td>
                            <a class="btn-lg btn-info" href="linkedit.php?id=<?=$row[ROW_ID]?>" target="_winEditLink">
                                <span class="glyphicon glyphicon-ok"> </span>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
</div><! --/container -->
