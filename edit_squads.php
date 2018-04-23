<?php

include 'common.php';
include 'menu.php';

// Functions

function save_squads(){
	global $conn;
	
	// are we adding new squads?
	if(array_key_exists('description',$_POST)){
		$values_strings = array();
		$values_string="";
		
		foreach($_POST['description'] as $key => $description ){
			$values_strings[] = "('" . $conn->real_escape_string($description) . "')";
		}
		
		// insert here
		$values_string = implode(',',$values_strings);
		$sql = "INSERT INTO squad (description) values " . $values_string;
		$result = $conn->query($sql);
	}
	
	// find out if any delete boxes were ticked
	if(array_key_exists('delete',$_POST)){
		if(sizeof($_POST['delete'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['delete'] as $key => $value){
				if(!is_numeric($key))unset($_POST['delete'][$key]);
			}
			$sql = "DELETE FROM squad WHERE id IN (" . implode(',',array_keys($_POST['delete'])) . ")";
			$result = $conn->query($sql);
		}
	}
	
	// TODO see if any existing entries need editing
	if(array_key_exists('update_description',$_POST)){
		$updates = array();
		
		foreach($_POST['update_description'] as $key => $update_description ){
			if($update_description!="")$updates[]="description='".$conn->real_escape_string($update_description)."'";
			
			if(sizeof($updates)>0){
				$sql = "UPDATE squad SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
				$result = $conn->query($sql);
			}
		}
	}
}

function show_squads_page(){
	global $conn;

	?>
	<html>
		<head>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/edit_squads.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show squads table
	// build table header
	?>
	<h1>Squads</h1>
	<form method="post">
		<table>
			<tr>
				<th>Edit</th>
				<th>Delete</th>
				<th>ID</th>
				<th>Squad Name</th>
			</tr>
	<?php
	
	$sql = "SELECT id,
			description
		FROM squad;";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<tr>"
				. "<td><button class='button-edit-squad' type='button' data-id='" . $row["id"] . "' >edit</button></td>"
				. "<td><label id='delete-squad-" . $row["id"] . "'><input type='checkbox' name='delete[".$row["id"]."]' value='1' />delete</label></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["description"] . "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Squads</td></tr>";
	}
	?>
					<tr>
						<td><button id="button-new-squad" type="button">+</button></td>
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
			save_squads();
			break;
	}
}

// Show page

show_squads_page();

$conn->close();

?>

