<?php

include 'common.php';
include 'menu.php';

// Season
// description
// date_begins
// date_agegroup

// Functions

function save_seasons(){
	global $conn;
	
	// are we adding new seasons?
	if(array_key_exists('description',$_POST)){
		$values_strings = array();
		$values_string="";
		
		foreach($_POST['description'] as $key => $description ){
			$date_begins = $_POST['date_begins'][$key];
			$date_agegroup = $_POST['date_agegroup'][$key];
			$values_strings[] = "('" . $description . "','" . $date_begins  . "','" . $date_agegroup ."')";
		}
		
		// insert here
		$values_string = implode(',',$values_strings);
		$sql = "INSERT INTO season (description,date_begins,date_agegroup) values " . $values_string;
		$result = $conn->query($sql);
	}
	
	// find out if any delete boxes were ticked
	if(array_key_exists('delete',$_POST)){
		if(sizeof($_POST['delete'])>0){
			$sql = "DELETE FROM season WHERE id IN (" . implode(',',array_keys($_POST['delete'])) . ")";
			$result = $conn->query($sql);
		}
	}
	
	// TODO see if any existing entries need editing
	if(array_key_exists('update_description',$_POST)){
		$updates = array();
		
		foreach($_POST['update_description'] as $key => $update_description ){
			$update_date_begins = $_POST['update_date_begins'][$key];
			$update_date_agegroup = $_POST['update_date_agegroup'][$key];
			
			if($update_description!="")$updates[]="description='".$update_description."'";
			if($update_date_begins!="")$updates[]="date_begins='".$update_date_begins."'";
			if($update_date_agegroup!="")$updates[]="date_agegroup='".$update_date_agegroup."'";
			
			if(sizeof($updates)>0){
				$sql = "UPDATE season SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
				$result = $conn->query($sql);
			}
		}
	}
}

function show_seasons_page(){
	global $conn;

	?>
	<html>
		<head>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/season.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show seasons table
	// build table header
	?>
	<h1>Seasons</h1>
	<form method="post">
		<table>
			<tr>
				<th>Edit</th>
				<th>Delete</th>
				<th>ID</th>
				<th>Description</th>
				<th>Date Begins</th>
				<th>Date Age Group Determined</th>
			</tr>
	<?php
	
	$sql = "SELECT id,
			description,
			date_begins,
			date_agegroup
		FROM season;";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<tr>"
				. "<td><button class='button-edit-season' type='button' data-id='" . $row["id"] . "' >edit</button></td>"
				. "<td><label id='delete-season-" . $row["id"] . "'><input type='checkbox' name='delete[".$row["id"]."]' value='1' />delete</label></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["description"] . "</td>"
				. "<td>" . $row["date_begins"] . "</td>"
				. "<td>" . $row["date_agegroup"] . "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Seasons</td></tr>";
	}
	?>
					<tr>
						<td><button id="button-new-season" type="button">+</button></td>
					</tr>
				</table>
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
			save_seasons();
			break;
	}
}

// Show page

show_seasons_page();

$conn->close();

?>

