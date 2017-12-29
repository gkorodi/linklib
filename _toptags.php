<?php

$sql = 'select tabs, count(*) from links where tabs IS NOT NULL group by tabs order by count(*) desc LIMIT 6';

?>