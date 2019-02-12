<?php
require_once('_includes.php');
foreach (glob(FEED_DIR.'/*.json') as $inputFileName) {
    echo "Processing file ${inputFileName}".PHP_EOL;
    try {
        $raw = file_get_contents($inputFileName);
        $newLink = new Link();

        $obj = json_decode($raw);
        $newLink->link = $obj->link.'';
        $newLink->title = $obj->title;
        $newLink->status = getLinkStatus($newLink->link);
        $newLink->last_updated = $obj->last_updated.'';
        $newLink->tags = $obj->tags;

        if ($newLink->addLink()) {
            if (!unlink($inputFileName)) {
                echo "ERROR: could not delete file ${inputFileName}".PHP_EOL;
            }
        } else {
            echo "Could not import file for curation!!!!".PHP_EOL;
        }
    } catch (Exception $e) {
        echo "Could not import file!!!! ".print_r($e, true).PHP_EOL;
    }
}
?>
