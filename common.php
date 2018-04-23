<?php

$title = 'Rowing Database';

$conn = null;
$rowserver = "127.0.0.1";
$rowuser = "rowuser";
$rowpass = "password";
$rowdb = "rowing";
$show_debug = true;

$config_current_season_id=null;

function connect_db() {
	global $conn, $rowserver, $rowuser, $rowpass, $rowdb;
	// Create connection
	$conn = new mysqli($rowserver, $rowuser, $rowpass, $rowdb);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
}

// this will run if config is missing
function reset_config(){
	global $conn, $show_debug;
	global $config_current_season_id;

	// delete all configurations
	$sql = "TRUNCATE TABLE config;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	// create a config
	
	// are there any seasons?
	$sql = "SELECT id FROM season;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$config_current_season_id=$row['id'];
	}
	else {
		$sql = "INSERT INTO season
				(description,date_begins,date_agegroup)
			VALUES ('default',
				date(curdate()-(month(now())*100)-(dayofmonth(now()))+0901),
				date(curdate()-(month(now())*100)-(dayofmonth(now()))+00010101));";
			
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
		
		// get the id back
		$sql = "SELECT id FROM season;";
		$result = $conn->query($sql);
		$config_current_season_id=$result->fetch_assoc()['id'];
	}
	
	// insert config
	$sql = "INSERT INTO config (id,current_season_id) VALUES (0,'".$config_current_season_id."');";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
}

// load_config should be called after a connection $conn has been created
function load_config() {
	global $conn, $show_debug;
	global $config_current_season_id;

	// try to load the default config, and if it doesn't exist, create it
	$sql = "SELECT * FROM config WHERE id=0;";
	// run query
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);

	// should be only one config available
	if ($result->num_rows == 1) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$config_current_season_id=$row['current_season_id'];
		}
	}
	// otherwise there is an issue
	else reset_config();
}

// get rower as array
function get_rower($rower_id){
	global $conn, $show_debug;
	
	if(is_numeric($rower_id)){
		// select the rower
		$sql = "SELECT * FROM rower WHERE id='".$rower_id."';";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);

		// should be only one rower available
		if ($result->num_rows == 1) return $result->fetch_assoc();
		else return null;
	}
	else return null;
}
?>