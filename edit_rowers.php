<?php

include 'common.php';
include 'menu.php';

// Functions

function save_rowers(){
	global $conn;
	// load from $_POST[]
	
	// are we adding new rowers?
	if(array_key_exists('name_last',$_POST)){
		$values_strings = array();
		$values_string="";
		
		foreach($_POST['name_last'] as $key => $name_last ){
			$name_first = $_POST['name_first'][$key];
			$date_birth = $_POST['date_birth'][$key];
			$schoolyear_offset = $_POST['schoolyear_offset'][$key];
			
			$values_strings[] = "('" . $name_last . "','" . $name_first . "','" . $date_birth . "','" . $schoolyear_offset . "')";
		}
		
		// insert here
		$values_string = implode(',',$values_strings);
		$sql = "INSERT INTO rowing.rower (name_last,name_first,date_birth,schoolyear_offset) values" . $values_string;
		$result = $conn->query($sql);
	}
	
	// find out if any delete boxes were ticked
	if(array_key_exists('delete',$_POST)){
		if(sizeof($_POST['delete'])>0){
			$sql = "DELETE FROM rowing.rower WHERE id IN (" . implode(',',array_keys($_POST['delete'])) . ")";
			$result = $conn->query($sql);
		}
	}
	
	// TODO see if any existing entries need editing
	if(array_key_exists('update_name_last',$_POST)){
		$updates = array();
		
		foreach($_POST['update_name_last'] as $key => $update_name_last ){
			$update_name_first = $_POST['update_name_first'][$key];
			$update_date_birth = $_POST['update_date_birth'][$key];
			$update_schoolyear_offset = $_POST['update_schoolyear_offset'][$key];
			
			if($update_name_last!="")$updates[]="name_last='".$update_name_last."'";
			if($update_name_first!="")$updates[]="name_first='".$update_name_first."'";
			if($update_date_birth!="")$updates[]="date_birth='".$update_date_birth."'";
			if($update_schoolyear_offset!="")$updates[]="schoolyear_offset='".$update_schoolyear_offset."'";
			
			if(sizeof($updates)>0){
				$sql = "UPDATE rowing.rower SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
				$result = $conn->query($sql);
			}
		}
	}
}

function show_rowers_page(){
	global $conn;

	?>
	<html>
		<head>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/rower.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show rowers table
	// build table header
	?>
	<h1>Rowers</h1>
	<form method="post">
		<table>
			<tr>
				<th>Edit</th>
				<th>Delete</th>
				<th>ID</th>
				<th>Last Name</th>
				<th>First Name</th>
				<th>DOB</th>
				<th>Age Group</th>
				<th>School Year</th>
				<th>School Year Offset</th>
			</tr>
	<?php
	
	$sql = "SELECT id,
			name_last,
			name_first,
			date_birth,
			YEAR(NOW())-YEAR(date_birth) AS age_group,
			YEAR(NOW())-YEAR(date_birth)-5+schoolyear_offset AS schoolyear,
			schoolyear_offset    
		FROM rowing.rower;";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<tr>"
				. "<td><button class='button-edit-rower' type='button' data-id='" . $row["id"] . "' >edit</button></td>"
				. "<td><label id='delete-rower-" . $row["id"] . "'><input type='checkbox' name='delete[".$row["id"]."]' value='1' />delete</label></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["name_last"] . "</td>"
				. "<td>" . $row["name_first"] . "</td>"
				. "<td>" . $row["date_birth"] . "</td>"
				. "<td>" . $row["age_group"] . "</td>"
				. "<td>" . $row["schoolyear"] . "</td>"
				. "<td>" . $row["schoolyear_offset"] . "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Rowers</td></tr>";
	}
	?>
					<tr>
						<td><button id="button-new-rower" type="button">+</button></td>
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

// TODO load season, config etc into js variables for calcs

// Handle POST action
if(isset($_POST) && array_key_exists('action',$_POST)){
	switch($_POST['action']){
		case 'save':
			save_rowers();
			break;
	}
}

// Show page

show_rowers_page();

$conn->close();

?>

