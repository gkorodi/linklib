 <?php
function getURLFromShortcut($fileContent) {
    $urlValue = 'something';
    foreach(explode("\n", $fileContent) AS $line) {
        //echo substr($line, 0, 4).PHP_EOL;
        if (substr($line, 0, 4) === 'URL=') {
            $urlValue = substr($line,4).PHP_EOL;
        }
    }
    return $urlValue;
}

function serviceAddLink($linkRecord) {
    $urlToPost = 'https://gaborkorodi.com/linklib/api/link.php';

    $ch = curl_init( $urlToPost );
    # Setup request to send json via POST.
    $payload = json_encode($linkRecord);
    echo $payload.PHP_EOL;

    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    # Return response instead of printing.
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    # Send request.
    $result = curl_exec($ch);
    echo json_encode(curl_getinfo($ch), JSON_PRETTY_PRINT);

    curl_close($ch);

    echo $result.PHP_EOL;

}

foreach(glob('C:\Users\gkoro\Desktop\*Emotions.URL') AS $filename) {
    $link['URL'] = getURLFromShortcut(file_get_contents($filename));
    $link['title'] = basename($filename, '.URL');
    serviceAddLink($link);

}