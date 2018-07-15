<?php

// note skeleton code copied from edit_squads
// make sure any edits/fixes are copied across

include 'common.php';
include 'menu.php';

$title_page='Edit Common Oar Rigs';

// Functions

function save_oar_rigs(){
	global $conn, $show_debug;
	
	// are we adding new oar rigs?
	if(array_key_exists('description',$_POST)){
		$values_strings = array();
		$values_string="";
		
		// load display index
		$sql = "SELECT max(display_index) AS display_index FROM oarrig;";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
		$display_index = $result->fetch_assoc()['display_index'];
		if($display_index==null)$display_index=0;
		else $display_index++;
		
		foreach($_POST['description'] as $key => $description ){
			$description = $conn->real_escape_string($description);
			$overall_cm = (is_numeric($_POST['overall_cm'][$key]) ? $_POST['overall_cm'][$key] : 'null');
			$inboard_cm = is_numeric($_POST['inboard_cm'][$key]) ? $_POST['inboard_cm'][$key] : 'null';
			
			$values_strings[] = "('" . $description
				. "'," . $overall_cm
				. "," . $inboard_cm
				. ",true,".$display_index.")";
			$display_index++;
		}
		
		// insert here
		$values_string = implode(',',$values_strings);
		$sql = "INSERT INTO oarrig (description,
			overall_cm,
			inboard_cm,
			is_active,
			display_index) values " . $values_string;
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
	
	// see if any existing entries need editing
	if(array_key_exists('update_description',$_POST)){
		$updates = array();
		
		foreach($_POST['update_description'] as $key => $update_description ){
			$update_description = $conn->real_escape_string($update_description);
			$update_overall_cm = (is_numeric($_POST['update_overall_cm'][$key]) ? $_POST['update_overall_cm'][$key] : -1);
			$update_inboard_cm = is_numeric($_POST['update_inboard_cm'][$key]) ? $_POST['update_inboard_cm'][$key] : -1;
		
			if($update_description!="")$updates[]="description='".$update_description."'";
			if($update_overall_cm!=-1)$updates[]="overall_cm='".$update_overall_cm."'";
			if($update_inboard_cm!=-1)$updates[]="inboard_cm='".$update_inboard_cm."'";
			
			if(sizeof($updates)>0){
				$sql = "UPDATE oarrig SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
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
				$sql = "UPDATE oarrig SET " . implode(',',$updates) . "WHERE id = " . $key . ";";
				$result = $conn->query($sql);
				if($show_debug && !$result)echo mysqli_error($conn);
			}
		}
	}
	
	// do any entries need activating
	if(array_key_exists('enable_oarrigs',$_POST)){
		if(sizeof($_POST['enable_oarrigs'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['enable_oarrigs'] as $key => $value){
				if(!is_numeric($key))unset($_POST['enable_oarrigs'][$key]);
			}
			$sql = "UPDATE oarrig SET is_active=1 WHERE id IN (" . implode(',',array_keys($_POST['enable_oarrigs'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// do any entries need deactivating
	if(array_key_exists('disable_oarrigs',$_POST)){
		if(sizeof($_POST['disable_oarrigs'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['disable_oarrigs'] as $key => $value){
				if(!is_numeric($key))unset($_POST['disable_oarrigs'][$key]);
			}
			$sql = "UPDATE oarrig SET is_active=0 WHERE id IN (" . implode(',',array_keys($_POST['disable_oarrigs'])) . ");";
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
			$sql = "DELETE FROM oarrig WHERE id IN (" . implode(',',array_keys($_POST['delete'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
}

function show_oar_rigs_page(){
	global $conn, $show_debug;
	global $title_software,$title_page;

	?>
	<html>
		<head>
			<title><?php echo $title_software." : ".$title_page; ?></title>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/edit_oar_rigs.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show oar rigs table
	// build table header
	?>
	<h1><?php echo $title_page; ?></h1>
	<p>Editing a shared oar rig will edit it for all previous sessions it has been used for!</p>
	<form method="post">
		<table>
			<tr>
				<th>Edit</th>
				<th>Delete</th>
				<th>ID</th>
				<th>Description</th>
				<th>Overall</th>
				<th>Inboard</th>
				<th>Outboard</th>
				<th>Gear Ratio</th>
				<th>Active</th>
				<th>Order</th>
			</tr>
	<?php
	
	$sql = "SELECT id,
			description,
			overall_cm,
			inboard_cm,
			display_index,
			is_active
		FROM oarrig
		ORDER BY display_index;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$outboard_cm='';
			$gear_ratio='';
			
			if(is_numeric($row["overall_cm"]) && is_numeric($row["inboard_cm"])){
				$outboard_cm = $row["overall_cm"] - $row["inboard_cm"];
				if($row["inboard_cm"]>0)$gear_ratio=$outboard_cm/$row["inboard_cm"];
			}
		
			echo "<tr class='tr-display' data-id='" . $row["id"] . "'>"
				. "<td><button class='button-edit-oarrigs' type='button' data-id='" . $row["id"] . "' >edit</button></td>"
				. "<td><label id='delete-oarrigs-" . $row["id"] . "'><input type='checkbox' name='delete[".$row["id"]."]' value='1' />delete</label></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["description"] . "</td>"
				. "<td>" . $row["overall_cm"] . "</td>"
				. "<td>" . $row["inboard_cm"] . "</td>"
				. "<td>" . $outboard_cm . "</td>"
				. "<td>" . round($gear_ratio,3) . "</td>"
				. "<td>" 
					. ($row["is_active"]==1 ?
						"Active <label><input type='checkbox' name='disable_oarrigs[".$row["id"]."]' value='1' />disable</label>"
						: "Inactive <label><input type='checkbox' name='enable_oarrigs[".$row["id"]."]' value='1' />enable</label>" )
					. "</td>"
				. "<td>"
					. "<input class='input-display-index' type='hidden' name='display_index[".$row["id"]."]' value='" . $row["display_index"] . "' disabled />"
					. "<button class='button-move-up' type='button' data-id='" . $row["id"] . "' >up</button>"
					. "<button class='button-move-down' type='button' data-id='" . $row["id"] . "' >down</button>"
				. "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Oar Rigs</td></tr>";
	}
	?>
					<tr>
						<td><button id="button-new-oarrigs" type="button">+</button></td>
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
			save_oar_rigs();
			break;
	}
}

// Show page

show_oar_rigs_page();

$conn->close();

?>

