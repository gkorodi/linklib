<?php
require_once('_includes.php');

if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $linkIds = queryX("SELECT id FROM links WHERE id = ".$_REQUEST['id']);

    foreach($linkIds AS $linkid) {
        $link = new Link($linkid['id']);

        $info = $link->getURLInfo();
        if ($info['http_code'] === 200) {
            var_dump($info);
        }

        $newurl = str_replace('?ref=webdesignernews.com','', $link->link);

        $newLinks = queryX("SELECT * FROM links WHERE link = '".$newurl."'");
        if (count($newLinks)===0) {
            $link->setURL($newurl);
        }
    }

}






