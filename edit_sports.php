<?php

include 'common.php';
include 'menu.php';

$title_page='Edit Sports';

// Functions

function save_sports(){
	global $conn, $show_debug;
	
	// are we adding new sports?
	if(array_key_exists('description',$_POST)){
		$values_strings = array();
		$values_string="";
		
		// load display index
		$sql = "SELECT max(display_index) AS display_index FROM sporttype;";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
		$display_index = $result->fetch_assoc()['display_index'];
		if($display_index==null)$display_index=0;
		else $display_index++;
		
		foreach($_POST['description'] as $key => $description ){
			$values_strings[] = "('" . $conn->real_escape_string($description) . "',true,'".$display_index."')";
			$display_index++;
		}
		
		// insert here
		$values_string = implode(',',$values_strings);
		$sql = "INSERT INTO sporttype (description,is_active,display_index) values " . $values_string;
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
	
	// see if any existing entries need editing
	if(array_key_exists('update_description',$_POST)){
		$updates = array();
		
		foreach($_POST['update_description'] as $key => $update_description ){
			if($update_description!="")$updates[]="description='".$conn->real_escape_string($update_description)."'";
			
			if(sizeof($updates)>0){
				$sql = "UPDATE sporttype SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
				$result = $conn->query($sql);
				if($show_debug && !$result)echo mysqli_error($conn);
			}
		}
	}
	
	// see if any entries need reordering
	if(array_key_exists('display_index',$_POST)){
		$updates = array();
		
		foreach($_POST['display_index'] as $key => $display_index ){
			if($display_index!="")$updates[]="display_index='".$conn->real_escape_string($display_index)."'";
			
			if(sizeof($updates)>0){
				$sql = "UPDATE sporttype SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
				$result = $conn->query($sql);
				if($show_debug && !$result)echo mysqli_error($conn);
			}
		}
	}
	
	// do any entries need activating
	if(array_key_exists('enable_sport',$_POST)){
		if(sizeof($_POST['enable_sport'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['enable_sport'] as $key => $value){
				if(!is_numeric($key))unset($_POST['enable_sport'][$key]);
			}
			$sql = "UPDATE sporttype SET is_active=1 WHERE id IN (" . implode(',',array_keys($_POST['enable_sport'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// do any entries need deactivating
	if(array_key_exists('disable_sport',$_POST)){
		if(sizeof($_POST['disable_sport'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['disable_sport'] as $key => $value){
				if(!is_numeric($key))unset($_POST['disable_sport'][$key]);
			}
			$sql = "UPDATE sporttype SET is_active=0 WHERE id IN (" . implode(',',array_keys($_POST['disable_sport'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// find out if any delete boxes were ticked
	if(array_key_exists('delete',$_POST)){
		if(sizeof($_POST['delete'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['delete'] as $key => $value){
				if(!is_numeric($key))unset($_POST['delete'][$key]);
			}
			$sql = "DELETE FROM sporttype WHERE id IN (" . implode(',',array_keys($_POST['delete'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
}

function show_sports_page(){
	global $conn, $show_debug;
	global $title_software, $title_page;

	?>
	<html>
		<head>
			<title><?php echo $title_software." : ".$title_page; ?></title>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/edit_sports.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show sports table
	// build table header
	?>
	<h1><?php echo $title_page; ?></h1>
	<form method="post">
		<table>
			<tr>
				<th>Edit</th>
				<th>Delete</th>
				<th>ID</th>
				<th>Sport Name</th>
				<th>Active</th>
				<th>Order</th>
			</tr>
	<?php
	
	$sql = "SELECT id,
			description,
			display_index,
			is_active
		FROM sporttype
		ORDER BY display_index;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<tr class='tr-display' data-id='" . $row["id"] . "'>"
				. "<td><button class='button-edit-sport' type='button' data-id='" . $row["id"] . "' >edit</button></td>"
				. "<td><label id='delete-sport-" . $row["id"] . "'><input type='checkbox' name='delete[".$row["id"]."]' value='1' />delete</label></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["description"] . "</td>"
				. "<td>" 
					. ($row["is_active"]==1 ?
						"Active <label><input type='checkbox' name='disable_sport[".$row["id"]."]' value='1' />disable</label>"
						: "Inactive <label><input type='checkbox' name='enable_sport[".$row["id"]."]' value='1' />enable</label>" )
					. "</td>"
				. "<td>"
					. "<input class='input-display-index' type='hidden' name='display_index[".$row["id"]."]' value='" . $row["display_index"] . "' disabled />"
					. "<button class='button-move-up' type='button' data-id='" . $row["id"] . "' >up</button>"
					. "<button class='button-move-down' type='button' data-id='" . $row["id"] . "' >down</button>"
				. "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Sports</td></tr>";
	}
	?>
					<tr>
						<td><button id="button-new-sport" type="button">+</button></td>
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
			save_sports();
			break;
	}
}

// Show page

show_sports_page();

$conn->close();

?>

