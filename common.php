<?php

$title = 'Rowing Database';

$conn = null;
$rowserver = "127.0.0.1";
$rowuser = "rowuser";
$rowpass = "password";
$rowdb = "rowing";

function connect_db() {
	global $conn, $rowserver, $rowuser, $rowpass, $rowdb;
	// Create connection
	$conn = new mysqli($rowserver, $rowuser, $rowpass, $rowdb);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
}


?>