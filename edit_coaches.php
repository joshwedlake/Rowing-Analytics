<?php

include 'common.php';
include 'menu.php';

$title_page='Edit Coaches';

// Functions

function save_coaches(){
	global $conn, $show_debug;
	
	// are we adding new coaches?
	if(array_key_exists('name_last',$_POST)){
		$values_strings = array();
		$values_string="";
		
		foreach($_POST['name_last'] as $key => $name_last ){
			$name_first = $_POST['name_first'][$key];
			
			// sanitize
			$name_last=$conn->real_escape_string($name_last);
			$name_first=$conn->real_escape_string($name_first);
			
			$values_strings[] = "('" . $name_last . "','" . $name_first . "')";
		}
		
		// insert here
		$values_string = implode(',',$values_strings);
		$sql = "INSERT INTO coach (name_last,name_first) values " . $values_string;
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
	
	// find out if any delete boxes were ticked
	if(array_key_exists('delete',$_POST)){
		if(sizeof($_POST['delete'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['delete'] as $key => $value){
				if(!is_numeric($key))unset($_POST['delete'][$key]);
			}
			
			$sql = "DELETE FROM coach WHERE id IN (" . implode(',',array_keys($_POST['delete'])) . ")";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// see if any existing entries need editing
	if(array_key_exists('update_name_last',$_POST)){
		$updates = array();
		
		foreach($_POST['update_name_last'] as $key => $update_name_last ){
			$update_name_first = $_POST['update_name_first'][$key];
			
			// sanitize
			$update_name_last=$conn->real_escape_string($update_name_last);
			$update_name_first=$conn->real_escape_string($update_name_first);
			
			if($update_name_last!="")$updates[]="name_last='".$update_name_last."'";
			if($update_name_first!="")$updates[]="name_first='".$update_name_first."'";
			
			if(sizeof($updates)>0){
				$sql = "UPDATE coach SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
				$result = $conn->query($sql);
				if($show_debug && !$result)echo mysqli_error($conn);
			}
		}
	}
}

function show_coaches_page(){
	global $conn, $show_debug;
	global $title_software, $title_page;

	?>
	<html>
		<head>
			<title><?php echo $title_software." : ".$title_page; ?></title>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/edit_coaches.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show coaches table
	// build table header
	?>
	<h1><?php echo $title_page; ?></h1>
	<form method="post">
		<table>
			<tr>
				<th>Edit</th>
				<th>Delete</th>
				<th>ID</th>
				<th>Last Name</th>
				<th>First Name</th>
			</tr>
	<?php
	
	$sql = "SELECT id,
			name_last,
			name_first
		FROM coach ORDER BY name_last,name_first;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<tr>"
				. "<td><button class='button-edit-coach' type='button' data-id='" . $row["id"] . "' >edit</button></td>"
				. "<td><label id='delete-coach-" . $row["id"] . "'><input type='checkbox' name='delete[".$row["id"]."]' value='1' />delete</label></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["name_last"] . "</td>"
				. "<td>" . $row["name_first"] . "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Coaches</td></tr>";
	}
	?>
					<tr>
						<td><button id="button-new-coach" type="button">+</button></td>
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
			save_coaches();
			break;
	}
}

// Show page

show_coaches_page();

$conn->close();

?>

