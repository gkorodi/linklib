<?php

$mysqli = new mysqli("127.0.0.1", "root", "root", "links");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error.PHP_EOL;
    exit(255);
}

/* Prepared statement, stage 1: prepare */
if (!($stmt = $mysqli->prepare("INSERT INTO tobecurated (title, link) VALUES (?,?)"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

echo 'Starting'.PHP_EOL;
$files = glob("/Users/Shared/staging/*.webloc");
echo "Processing ".count($files)." files.".PHP_EOL;

foreach($files as $filename) {
    echo '********* PROCESSING '.$filename.' *********'.PHP_EOL;

    $bn = basename($filename, '.webloc');
    $xml = simplexml_load_file($filename);

    $id = 1;
    if (!$stmt->bind_param("ss", $bn, $xml->dict->string)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    if (!$stmt->execute()) {
        if ($stmt->errno === 1062 ) {
            echo "Duplicate link, `".$xml->dict->string."`. Skipping.".PHP_EOL;
            unlink($filename);
        } else {
            echo "LinkError:  ********** `".$xml->dict->string."` in file `".$bn."`".PHP_EOL;
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error.PHP_EOL;
        }
    } else {
        unlink($filename);
    }
}
$stmt->close();

echo 'Finished'.PHP_EOL;