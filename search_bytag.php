<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once('_includes.php');
require_once(__DIR__.'/vendor/autoload.php');
$loader = new FilesystemLoader(__DIR__.'/templates');
$twig = new Environment($loader, array('debug' => true));

$links = Array();
if (isset($_REQUEST['tag'])) {
    if ($_REQUEST['tag'] == 'empty') {
        $sql = "SELECT * FROM links WHERE tags IS NULL ".
            (isset($_REQUEST['notstatus'])?' AND status != '.$_REQUEST['notstatus']:'').
            (isset($_REQUEST['status'])?' AND status = '.$_REQUEST['status']:'').
            ' ORDER BY updated_at '.($_REQUEST['olderfirst']?'ASC':'DESC')
            .' LIMIT 50';
    } else {
        $criteria = [];
        foreach(explode(',', $_REQUEST['tag']) AS $t) {
            $criteria[] = " UPPER(tags) LIKE '%".strtoupper($t)."%' ";
        }
        $sql = "SELECT * FROM links WHERE ".
            implode(' AND ', $criteria)." ".
            (isset($_REQUEST['notstatus'])?' AND status != '.$_REQUEST['notstatus']:'').
            (isset($_REQUEST['status'])?' AND status = '.$_REQUEST['status']:'').
            ' ORDER BY updated_at '.(isset($_REQUEST['olderfirst'])?'ASC':'DESC')
            .' LIMIT 50'	;
    }
    $links = queryX($sql);
}

$relatedTags = Array();
foreach($links AS $linkIdx => $linkObject) {
    $links[$linkIdx]['hostname'] = justHostName($linkObject['link']);
    foreach(explode(',', $links[$linkIdx]['tags']) AS $tag) {
        if ( empty($tag) || $tag === $_REQUEST['tag']) { continue; }
        if (isset($relatedTags[$tag])) {
            $count = $relatedTags[$tag]['count']+1;
            $relatedTags[$tag] = ['tag'=>$tag, 'count'=>$count];
        } else {
            $relatedTags[$tag] = ['tag'=>$tag, 'count'=>1];
        }
    }
}

$data = [
    'links' => $links,
    'searchTag' => $_REQUEST['tag'],
    'relatedTags' => $relatedTags
];

if (isset($_REQUEST['format']) && $_REQUEST['format'] === 'json') {
    header('Content-type: application/json');
    echo json_encode($data);
    exit;
}

renderView('search_bytag.html', $data);