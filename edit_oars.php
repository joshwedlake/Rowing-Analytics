<?php

// note skeleton code copied from edit_squads
// make sure any edits/fixes are copied across

include 'common.php';
include 'menu.php';

$title_page='Edit Oars';

// Functions

function save_oars(){
	global $conn, $show_debug;
	
	// are we adding new oars?
	if(array_key_exists('description_name',$_POST)){
		$values_strings = array();
		$values_string="";
		
		// load display index
		$sql = "SELECT max(display_index) AS display_index FROM oar;";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
		$display_index = $result->fetch_assoc()['display_index'];
		if($display_index==null)$display_index=0;
		else $display_index++;
		
		foreach($_POST['description_name'] as $key => $description_name ){
			$description_name = $conn->real_escape_string($description_name);
			$description_manufacturer = $conn->real_escape_string($_POST['description_manufacturer'][$key]);
			$description_style = $conn->real_escape_string($_POST['description_style'][$key]);
			$description_comment = $conn->real_escape_string($_POST['description_comment'][$key]);
			$seats_count = (is_numeric($_POST['seats_count'][$key]) ? $_POST['seats_count'][$key] : 'null');
			$year_manufacture = is_numeric($_POST['year_manufacture'][$key]) ? $_POST['year_manufacture'][$key] : 'null';
			$is_sweep = is_numeric($_POST['is_sweep'][$key]) ? $_POST['is_sweep'][$key] : 'null';
			$is_hatchet = is_numeric($_POST['is_hatchet'][$key]) ? $_POST['is_hatchet'][$key] : 'null';
			$is_fat = is_numeric($_POST['is_fat'][$key]) ? $_POST['is_fat'][$key] : 'null';
			$is_smoothie = is_numeric($_POST['is_smoothie'][$key]) ? $_POST['is_smoothie'][$key] : 'null';
			$is_vortex = is_numeric($_POST['is_vortex'][$key]) ? $_POST['is_vortex'][$key] : 'null';
			
			$values_strings[] = "('" . $description_name
				. "','" . $description_manufacturer
				. "','" . $description_style
				. "','" . $description_comment
				. "'," . $seats_count
				. "," . $year_manufacture
				. "," . $is_sweep
				. "," . $is_hatchet
				. "," . $is_fat
				. "," . $is_smoothie
				. "," . $is_vortex
				. ",true,".$display_index.")";
			$display_index++;
		}
		
		// insert here
		$values_string = implode(',',$values_strings);
		$sql = "INSERT INTO oar (description_name,
			description_manufacturer,
			description_style,
			description_comment,
			seats_count,
			year_manufacture,
			is_sweep,
			is_hatchet,
			is_fat,
			is_smoothie,
			is_vortex,
			is_active,
			display_index) values " . $values_string;
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
	
	// see if any existing entries need editing
	if(array_key_exists('update_description_name',$_POST)){
		$updates = array();
		
		foreach($_POST['update_description_name'] as $key => $update_description_name ){
			$update_description_name = $conn->real_escape_string($update_description_name);
			$update_description_manufacturer = $conn->real_escape_string($_POST['update_description_manufacturer'][$key]);
			$update_description_style = $conn->real_escape_string($_POST['update_description_style'][$key]);
			$update_description_comment = $conn->real_escape_string($_POST['update_description_comment'][$key]);
			$update_seats_count = (is_numeric($_POST['update_seats_count'][$key]) ? $_POST['update_seats_count'][$key] : -1);
			$update_year_manufacture = is_numeric($_POST['update_year_manufacture'][$key]) ? $_POST['update_year_manufacture'][$key] : -1;
			$update_is_sweep = is_numeric($_POST['update_is_sweep'][$key]) ? $_POST['update_is_sweep'][$key] : -1;
			$update_is_hatchet = is_numeric($_POST['update_is_hatchet'][$key]) ? $_POST['update_is_hatchet'][$key] : -1;
			$update_is_fat = is_numeric($_POST['update_is_fat'][$key]) ? $_POST['update_is_fat'][$key] : -1;
			$update_is_smoothie = is_numeric($_POST['update_is_smoothie'][$key]) ? $_POST['update_is_smoothie'][$key] : -1;
			$update_is_vortex = is_numeric($_POST['update_is_vortex'][$key]) ? $_POST['update_is_vortex'][$key] : -1;
		
			if($update_description_name!="")$updates[]="description_name='".$update_description_name."'";
			if($update_description_manufacturer!="")$updates[]="description_manufacturer='".$conn->real_escape_string($update_description_manufacturer)."'";
			if($update_description_style!="")$updates[]="description_style='".$conn->real_escape_string($update_description_style)."'";
			if($update_description_comment!="")$updates[]="description_comment='".$conn->real_escape_string($update_description_comment)."'";
			if($update_seats_count!=-1)$updates[]="seats_count='".$update_seats_count."'";
			if($update_year_manufacture!=-1)$updates[]="year_manufacture='".$update_year_manufacture."'";
			if($update_is_sweep!=-1)$updates[]="is_sweep='".$update_is_sweep."'";
			if($update_is_hatchet!=-1)$updates[]="is_hatchet='".$update_is_hatchet."'";
			if($update_is_fat!=-1)$updates[]="is_fat='".$update_is_fat."'";
			if($update_is_smoothie!=-1)$updates[]="is_smoothie='".$update_is_smoothie."'";
			if($update_is_vortex!=-1)$updates[]="is_vortex='".$update_is_vortex."'";
			
			if(sizeof($updates)>0){
				$sql = "UPDATE oar SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
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
				$sql = "UPDATE oar SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
				$result = $conn->query($sql);
				if($show_debug && !$result)echo mysqli_error($conn);
			}
		}
	}
	
	// do any entries need activating
	if(array_key_exists('enable_oars',$_POST)){
		if(sizeof($_POST['enable_oars'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['enable_oars'] as $key => $value){
				if(!is_numeric($key))unset($_POST['enable_oars'][$key]);
			}
			$sql = "UPDATE oar SET is_active=1 WHERE id IN (" . implode(',',array_keys($_POST['enable_oars'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// do any entries need deactivating
	if(array_key_exists('disable_oars',$_POST)){
		if(sizeof($_POST['disable_oars'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['disable_oars'] as $key => $value){
				if(!is_numeric($key))unset($_POST['disable_oars'][$key]);
			}
			$sql = "UPDATE oar SET is_active=0 WHERE id IN (" . implode(',',array_keys($_POST['disable_oars'])) . ");";
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
			$sql = "DELETE FROM oar WHERE id IN (" . implode(',',array_keys($_POST['delete'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
}

function show_oars_page(){
	global $conn, $show_debug;
	global $title_software,$title_page;

	?>
	<html>
		<head>
			<title><?php echo $title_software." : ".$title_page; ?></title>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/edit_oars.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show oars table
	// build table header
	?>
	<h1><?php echo $title_page; ?></h1>
	<form method="post">
		<table>
			<tr>
				<th>Edit</th>
				<th>Delete</th>
				<th>ID</th>
				<th>Set Name</th>
				<th>Manufacturer</th>
				<th>Style</th>
				<th>Count</th>
				<th>Year</th>
				<th>Discipline</th>
				<th>Spoon</th>
				<th>Size</th>
				<th>Face</th>
				<th>Tip</th>
				<th>Comment</th>
				<th>Active</th>
				<th>Order</th>
			</tr>
	<?php
	
	$sql = "SELECT id,
			description_name,
			description_comment,
			description_manufacturer,
			description_style,
			seats_count,
			year_manufacture,
			is_sweep,
			is_hatchet,
			is_fat,
			is_smoothie,
			is_vortex,
			display_index,
			is_active
		FROM oar
		ORDER BY display_index;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<tr class='tr-display' data-id='" . $row["id"] . "'>"
				. "<td><button class='button-edit-oars' type='button' data-id='" . $row["id"] . "' >edit</button></td>"
				. "<td><label id='delete-oars-" . $row["id"] . "'><input type='checkbox' name='delete[".$row["id"]."]' value='1' />delete</label></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["description_name"] . "</td>"
				. "<td>" . $row["description_manufacturer"] . "</td>"
				. "<td>" . $row["description_style"] . "</td>"
				. "<td>" . $row["seats_count"] . "</td>"
				. "<td>" . $row["year_manufacture"] . "</td>"
				. "<td>" . ( $row["is_sweep"]==1 ?
						"Sweep"
						: "Scull" ) . "</td>"
				. "<td>" . ($row["is_hatchet"]==1 ?
						"Hatchet"
						: "Macon" ) . "</td>"
				. "<td>" . ($row["is_fat"]==1 ?
						"Fat"
						: "Standard" ) . "</td>"
				. "<td>" . ($row["is_smoothie"]==1 ?
						"Smoothie"
						: "Ridge" ) . "</td>"
				. "<td>" . ($row["is_vortex"]==1 ?
						"Vortex"
						: "Standard" ) . "</td>"
				. "<td>" . $row["description_comment"] . "</td>"
				. "<td>" 
					. ($row["is_active"]==1 ?
						"Active <label><input type='checkbox' name='disable_oars[".$row["id"]."]' value='1' />disable</label>"
						: "Inactive <label><input type='checkbox' name='enable_oars[".$row["id"]."]' value='1' />enable</label>" )
					. "</td>"
				. "<td>"
					. "<input class='input-display-index' type='hidden' name='display_index[".$row["id"]."]' value='" . $row["display_index"] . "' disabled />"
					. "<button class='button-move-up' type='button' data-id='" . $row["id"] . "' >up</button>"
					. "<button class='button-move-down' type='button' data-id='" . $row["id"] . "' >down</button>"
				. "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Oars</td></tr>";
	}
	?>
					<tr>
						<td><button id="button-new-oars" type="button">+</button></td>
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
			save_oars();
			break;
	}
}

// Show page

show_oars_page();

$conn->close();

?>

