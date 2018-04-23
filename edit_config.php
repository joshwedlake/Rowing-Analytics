<?php

include 'common.php';
include 'menu.php';

// Config

// load_config lives in common.php

// Global variables
$seasons=null;

// Functions

function save_config(){
	global $conn,$show_debug;
	
	// should only be one entry to update
	
	// are we updating the current season id?
	if(array_key_exists('current_season_id',$_POST) && is_numeric($_POST['current_season_id'])){
		$sql = "UPDATE config SET current_season_id=".$_POST['current_season_id']." WHERE id = 0;";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
}

function load_seasons(){
	global $conn, $show_debug, $seasons;
	
	$seasons=array();
	
	$sql = "SELECT id,
			description,
			date_begins,
			date_agegroup
		FROM season;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			if(is_numeric($row["id"])){
				$seasons[$row["id"]]=array(
					"description" => $row["description"],
					"date_begins" => $row["date_begins"],
					"date_agegroup" => $row["date_agegroup"]
				);
			}
		}
	}
}

function show_config_page(){
	global $conn,$show_debug,$seasons,$config_current_season_id;

	?>
	<html>
		<head>
			<script src="script/jquery-3.3.1.min.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show seasons table
	// build table header
	?>
	<h1>Configure</h1>
	<form method="post">
	<?php
	
	// PHP code to build config form here
	
	// load config
	load_config();
	
	// get seasons list to $seasons
	load_seasons();
	
	?>
				<div>
					Default Season: 
					<select name="current_season_id">
					<?php
						foreach($seasons as $season_id => $season){
							echo "<option value='".$season_id."'"
								. ( $config_current_season_id==$season_id ? " selected='selected' " : "")
								.">"
								.$season["description"]
								.", beginning "
								.$season["date_begins"]
								.", age on "
								.$season["date_agegroup"]
								."</option>";
						
						}
					?>
					</select>
				</div>
				<button type="submit" name="action" value="save">Save</button>
			</form>
		</body>
	</html>
	<?php
}

// connect to the database
connect_db();

// Handle POST action
if(isset($_POST) && array_key_exists('action',$_POST)){
	switch($_POST['action']){
		case 'save':
			save_config();
			break;
	}
}

// Show page

show_config_page();

$conn->close();

?>

