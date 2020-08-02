 <?php
$servername = "127.0.0.1";
$username = "root";
$password = "root";
$dbname = "links";


function getTagList() {
	$listoflinks = Array();
	$sql = "SELECT id, link FROM links WHERE tags IS NULL";
	$rowset = query($sql);
	foreach ($rowset['rows'] AS $row) {
		echo print_r($row, true);
	}
	return $listoflinks;
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// prepare and bind
$stmt = $conn->prepare("UPDATE links SET tags = ? WHERE id = ?");
$stmt->bind_param("sd", $newtags, $id);

// set parameters and execute
$recordcount = 0;
$tagList = getTagList();
foreach ($tagList AS $id => $tags) {
	$newtags = $tags;
	//$stmt->execute();	
	$recordcount++;
}
echo "Updated ${recordcount} records.";

$stmt->close();
$conn->close();
?> 