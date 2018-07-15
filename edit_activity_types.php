<?php

include 'common.php';
include 'menu.php';

$title_page='Edit Activity Types';

$sports=null;

// Functions

function save_activitytypes(){
	global $conn, $show_debug;
	
	// are we adding new activity types?
	if(array_key_exists('description',$_POST)){
		$values_strings = array();
		$values_string="";
		
		// load display index
		$sql = "SELECT max(display_index) AS display_index FROM activitytype;";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
		$display_index = $result->fetch_assoc()['display_index'];
		if($display_index==null)$display_index=0;
		else $display_index++;
		
		foreach($_POST['description'] as $key => $description ){
			$sporttype_id = is_numeric($_POST['sporttype_id'][$key]) ? "'".$_POST['sporttype_id'][$key]."'" : null;
			if($sporttype_id==-1)$sporttype_id=null;
			$uses_boats = (array_key_exists('uses_boats',$_POST) && array_key_exists($key,$_POST['uses_boats']) && $_POST['uses_boats'][$key]) ? 'true' : 'false';
			$uses_oars = (array_key_exists('uses_oars',$_POST) && array_key_exists($key,$_POST['uses_oars']) && $_POST['uses_oars'][$key]) ? 'true' : 'false';
			$uses_ergs = (array_key_exists('uses_ergs',$_POST) && array_key_exists($key,$_POST['uses_ergs']) && $_POST['uses_ergs'][$key]) ? 'true' : 'false';
			$values_strings[] = "('" . $conn->real_escape_string($description) . "',true,".$sporttype_id.",'".$display_index."',".$uses_boats.",".$uses_oars.",".$uses_ergs.")";
			$display_index++;
		}
		
		// insert here
		$values_string = implode(',',$values_strings);
		$sql = "INSERT INTO activitytype (description,is_active,sporttype_id,display_index,uses_boats,uses_oars,uses_ergs) values " . $values_string;
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
	
	// see if any existing entries need editing
	if(array_key_exists('update_description',$_POST)){
		$updates = array();
		
		foreach($_POST['update_description'] as $key => $update_description ){
			if($update_description!="")$updates[]="description='".$conn->real_escape_string($update_description)."'";
			$update_sporttype_id = is_numeric($_POST['update_sporttype_id'][$key]) ? $_POST['update_sporttype_id'][$key] : null;
			if($update_sporttype_id!=null && $update_sporttype_id>=0){
				$updates[]="sporttype_id='".$update_sporttype_id."'";
			}
			
			if(sizeof($updates)>0){
				$sql = "UPDATE activitytype SET " . implode(',',$updates) . " WHERE id = " . $key . ";";
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
				$sql = "UPDATE activitytype SET " . implode(',',$updates) . " WHERE id = " . $key . ";";
				$result = $conn->query($sql);
				if($show_debug && !$result)echo mysqli_error($conn);
			}
		}
	}
	
	// process booleans
	$booleans = ['activitytype'=>'is_active','boats'=>'uses_boats','oars'=>'uses_oars','ergs'=>'uses_ergs'];
	foreach($booleans as $bool =>  $storage){
		// do any entries need activating
		if(array_key_exists('enable_'.$bool,$_POST)){
			if(sizeof($_POST['enable_'.$bool])>0){
				// sanitize by deleting non numeric keys
				foreach($_POST['enable_'.$bool] as $key => $value){
					if(!is_numeric($key))unset($_POST['enable_'.$bool][$key]);
				}
				$sql = "UPDATE activitytype SET ".$storage."=1 WHERE id IN (" . implode(',',array_keys($_POST['enable_'.$bool])) . ");";
				$result = $conn->query($sql);
				if($show_debug && !$result)echo mysqli_error($conn);
			}
		}
		
		// do any entries need deactivating
		if(array_key_exists('disable_'.$bool,$_POST)){
			if(sizeof($_POST['disable_'.$bool])>0){
				// sanitize by deleting non numeric keys
				foreach($_POST['disable_'.$bool] as $key => $value){
					if(!is_numeric($key))unset($_POST['disable_'.$bool][$key]);
				}
				$sql = "UPDATE activitytype SET ".$storage."=0 WHERE id IN (" . implode(',',array_keys($_POST['disable_'.$bool])) . ");";
				$result = $conn->query($sql);
				if($show_debug && !$result)echo mysqli_error($conn);
			}
		}
	}
	
	// find out if any delete boxes were ticked
	if(array_key_exists('delete',$_POST)){
		if(sizeof($_POST['delete'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['delete'] as $key => $value){
				if(!is_numeric($key))unset($_POST['delete'][$key]);
			}
			$sql = "DELETE FROM activitytype WHERE id IN (" . implode(',',array_keys($_POST['delete'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
}

function build_sports_dropdown(){
	global $sports;
	
	$sports_dropdown="<option value='-1'></option>";
	
	foreach($sports as $sport_id => $sport){
		$sports_dropdown.="<option value='".$sport_id."'>"
			.$sport["description"]
			."</option>";
	}
	
	return $sports_dropdown;
}

function show_activitytypes_page(){
	global $conn, $show_debug;
	global $title_software, $title_page;
	global $sports;

	?>
	<html>
		<head>
			<title><?php echo $title_software." : ".$title_page; ?></title>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/edit_activity_types.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();
	
	// load sports from common
	load_sports();

	// Show activity types table
	// build table header
	?>
	<h1><?php echo $title_page; ?></h1>
	<form method="post">
		<table>
			<tr>
				<th>Edit</th>
				<th>Delete</th>
				<th>ID</th>
				<th>Activity Type</th>
				<th>Sport Type</th>
				<th>Boats</th>
				<th>Oars</th>
				<th>Ergs</th>
				<th>Active</th>
				<th>Order</th>
			</tr>
	<?php
	
	$sql = "SELECT id,
			description,
			display_index,
			sporttype_id,
			uses_boats,
			uses_oars,
			uses_ergs,
			is_active
		FROM activitytype
		ORDER BY display_index;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<tr class='tr-display' data-id='" . $row["id"] . "'>"
				. "<td><button class='button-edit-activitytype' type='button' data-id='" . $row["id"] . "' >edit</button></td>"
				. "<td><label id='delete-activitytype-" . $row["id"] . "'><input type='checkbox' name='delete[".$row["id"]."]' value='1' />delete</label></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["description"] . "</td>"
				. "<td>"
					. ( array_key_exists($row["sporttype_id"],$sports) ? $sports[$row["sporttype_id"]]["description"] : "unselected" )
					. "</td>"
				. "<td>" 
					. ($row["uses_boats"]==1 ?
						"Yes <label><input type='checkbox' name='disable_boats[".$row["id"]."]' value='1' />disable</label>"
						: "No <label><input type='checkbox' name='enable_boats[".$row["id"]."]' value='1' />enable</label>" )
					. "</td>"
				. "<td>" 
					. ($row["uses_oars"]==1 ?
						"Yes <label><input type='checkbox' name='disable_oars[".$row["id"]."]' value='1' />disable</label>"
						: "No <label><input type='checkbox' name='enable_oars[".$row["id"]."]' value='1' />enable</label>" )
					. "</td>"
				. "<td>" 
					. ($row["uses_ergs"]==1 ?
						"Yes <label><input type='checkbox' name='disable_ergs[".$row["id"]."]' value='1' />disable</label>"
						: "No <label><input type='checkbox' name='enable_ergs[".$row["id"]."]' value='1' />enable</label>" )
					. "</td>"
				. "<td>" 
					. ($row["is_active"]==1 ?
						"Active <label><input type='checkbox' name='disable_activitytype[".$row["id"]."]' value='1' />disable</label>"
						: "Inactive <label><input type='checkbox' name='enable_activitytype[".$row["id"]."]' value='1' />enable</label>" )
					. "</td>"
				. "<td>"
					. "<input class='input-display-index' type='hidden' name='display_index[".$row["id"]."]' value='" . $row["display_index"] . "' disabled />"
					. "<button class='button-move-up' type='button' data-id='" . $row["id"] . "' >up</button>"
					. "<button class='button-move-down' type='button' data-id='" . $row["id"] . "' >down</button>"
				. "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Activity Types</td></tr>";
	}
	?>
					<tr>
						<td><button id="button-new-activitytype" type="button">+</button></td>
					</tr>
				</table>
				<button type="submit" name="action" value="save">Save</button>
			</form>
			<script>
				var sports_dropdown_code = "<?php echo build_sports_dropdown(); ?>";
			</script>
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
			save_activitytypes();
			break;
	}
}

// Show page

show_activitytypes_page();

$conn->close();

?>

