<?php


function query($sql) {
	// Remote connection example: http://stackoverflow.com/questions/464317/connect-to-a-mysql-server-over-ssh-in-php
	// ssh -fNg -L 3307:127.0.0.1:3306 myuser@remotehost.com
	/*
	ssh -fNg -L 3307:127.0.0.1:3306 myuser@remotehost.com
	mysql -h 127.0.0.1 -P 3307 -u dbuser -p db
	
	$smysql = mysql_connect( "127.0.0.1:3307", "dbuser", "PASS" );
	mysql_select_db( "db", $smysql ); 
	
	*/
	
	$response['sql'] = $sql;

	// Connecting, selecting database
	$DBServer = '127.0.0.1'; // e.g 'localhost' or '192.168.1.100'
	$DBUser   = 'root';
	$DBPass   = 'Kaposvar-16';
	$DBName   = 'links';
	$DBPort = 3307;

	$conn = new mysqli($DBServer.':'.$DBPort, $DBUser, $DBPass, $DBName);

	$rs = $conn->query($sql);
	if($rs === false) {
	  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
	} else {
	  $response['rowcount'] = $rs->num_rows;
	}

	$response['rows'] = Array();
	$rs->data_seek(0);
	while($row = $rs->fetch_row()){
		array_push($response['rows'], $row);
	}
	$rs->free();
	$conn->close();

	return $response;
}
var_dump(query('SELECT count(*) FROM links'));

?>
