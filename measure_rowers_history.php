<?php

include 'common.php';
include 'menu.php';

$title_page='Measurement History';

$rower_id=null;
$rower=null;

// Functions
function save_rowers_measurements(){
	global $conn, $show_debug;
	global $rower_id;
	
	
	// add any new measurements
	if(array_key_exists('new_measurement_date',$_POST)){
		$weight_values_strings = array();
		$mh_values_strings = array();
		$ma_values_strings = array();
		$mha_values_strings = array();
		$values_string="";
		
		foreach($_POST['new_measurement_date'] as $key => $new_measurement_date ){
			// try sanitizing date
			if($new_measurement_date=="")$new_measurement_date=date('Y-m-d');
			else $new_measurement_date = date('Y-m-d', strtotime($new_measurement_date));
			
			// if date not set or invalid, assume today
			if($new_measurement_date==false)$new_measurement_date=date('Y-m-d');
		
			// sanitize
			$new_weight = (is_numeric($_POST['new_weight'][$key]) ? $_POST['new_weight'][$key] : "");
			$new_height = (is_numeric($_POST['new_height'][$key]) ? $_POST['new_height'][$key] : "");
			$new_armspan = (is_numeric($_POST['new_armspan'][$key]) ? $_POST['new_armspan'][$key] : "");
			
			if($new_weight!=""){
				$weight_values_strings[] = "('" . $rower_id . "','" . $new_measurement_date . "','" . $new_weight . "')";
			}
			if($new_height!="" && $new_armspan=="")
				$mh_values_strings[] = "('" . $rower_id . "','" . $new_measurement_date . "','" . $new_height . "')";
			else if($new_height=="" && $new_armspan!="")
				$ma_values_strings[] = "('" . $rower_id . "','" . $new_measurement_date . "','" . $new_armspan . "')";
			else if($new_height!="" && $new_armspan!="")
				$mha_values_strings[] = "('" . $rower_id . "','" . $new_measurement_date . "','" . $new_height . "','" . $new_armspan . "')";
		}
		
		// insert weights
		if(sizeof($weight_values_strings)>0){
			$values_string = implode(',',$weight_values_strings);
			$sql = "INSERT INTO weight (rower_id,date_weighed,weight_kg) values " . $values_string . " ON DUPLICATE KEY UPDATE weight_kg=values(weight_kg);";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
		// insert height only
		if(sizeof($mh_values_strings)>0){
			$values_string = implode(',',$mh_values_strings);
			$sql = "INSERT INTO measurement (rower_id,date_measured,height_cm) values " . $values_string . " ON DUPLICATE KEY UPDATE height_cm=values(height_cm);";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
		// insert armspan only
		if(sizeof($ma_values_strings)>0){
			$values_string = implode(',',$ma_values_strings);
			$sql = "INSERT INTO measurement (rower_id,date_measured,armspan_cm) values " . $values_string . " ON DUPLICATE KEY UPDATE armspan_cm=values(armspan_cm);";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
		// insert height and armspan
		if(sizeof($mha_values_strings)>0){
			$values_string = implode(',',$mha_values_strings);
			$sql = "INSERT INTO measurement (rower_id,date_measured,height_cm,armspan_cm) values " . $values_string
				. " ON DUPLICATE KEY UPDATE height_cm=values(height_cm),armspan_cm=values(armspan_cm);";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// Editing existing entries
	if(array_key_exists('update_measurement_date',$_POST)){

		foreach($_POST['update_measurement_date'] as $id => $update_measurement_date ){
			$weight_updates = array();
			$measurement_updates = array();
		
			// load ids, at least one must exist
			$weight_id = (is_numeric($_POST['wid'][$id]) ? $_POST['wid'][$id] : "");
			$measurement_id = (is_numeric($_POST['mid'][$id]) ? $_POST['mid'][$id] : "");
			
			// load new heights and weights
			$update_weight = (is_numeric($_POST['update_weight'][$id]) ? $_POST['update_weight'][$id] : "");
			$update_height = (is_numeric($_POST['update_height'][$id]) ? $_POST['update_height'][$id] : "");
			$update_armspan = (is_numeric($_POST['update_armspan'][$id]) ? $_POST['update_armspan'][$id] : "");
			
			// try sanitizing date
			$new_measurement_date="";
			if($update_measurement_date!=""){
				$update_measurement_date = date('Y-m-d', strtotime($update_measurement_date));
				if($weight_id!="")$weight_updates[]="date_weighed='".$conn->real_escape_string($update_measurement_date)."'";
				if($measurement_id!="")$measurement_updates[]="date_measured='".$conn->real_escape_string($update_measurement_date)."'";
			}
			else if($_POST['d'][$id]!="")$new_measurement_date = date('Y-m-d', strtotime($_POST['d'][$id]));
			else echo "ERROR date missing!";
			
			if($weight_id!="" && $update_weight!="")$weight_updates[]="weight_kg='".$conn->real_escape_string($update_weight)."'";
			if($measurement_id!=""){
				if($update_height!="")$measurement_updates[]="height_cm='".$conn->real_escape_string($update_height)."'";
				if($update_armspan!="")$measurement_updates[]="armspan_cm='".$conn->real_escape_string($update_armspan)."'";
			}
			
			if($update_weight!="" || $update_measurement_date!=""){
				if($weight_id!="" && sizeof($weight_updates)>0){
					// do update
					$sql = "UPDATE weight SET " . implode(',',$weight_updates) . " WHERE id = " . $weight_id . ";";
					$result = $conn->query($sql);
					if($show_debug && !$result)echo mysqli_error($conn);
				}
				else {
					// do insert
					$sql = "INSERT INTO weight (rower_id,date_weighed,weight_kg) values ('".$rower_id."','" . $new_measurement_date . $update_measurement_date . "','".$update_weight."');";
					$result = $conn->query($sql);
					if($show_debug && !$result)echo mysqli_error($conn);
				}
			}
			
			if($update_height!="" || $update_armspan!="" || $update_measurement_date!=""){
				if($measurement_id!="" && sizeof($measurement_updates)>0){
					// do update
					$sql = "UPDATE measurement SET " . implode(',',$measurement_updates) . " WHERE id = " . $measurement_id . ";";
					$result = $conn->query($sql);
					if($show_debug && !$result)echo mysqli_error($conn);
				}
				else {
					// do insert
					$sql = "INSERT INTO measurement (rower_id,date_measured,height_cm,armspan_cm) values ('"
						. $rower_id."','"
						. $new_measurement_date . $update_measurement_date . "','"
						. $update_height."','"
						. $update_armspan."');";
					$result = $conn->query($sql);
					if($show_debug && !$result)echo mysqli_error($conn);
				}
			}
		}
	}
	

	// delete weights
	if(array_key_exists('delete_weight',$_POST)){
		if(sizeof($_POST['delete_weight'])>0){
			// sanitize is_numeric
			foreach($_POST['delete_weight'] as $key => $value){
				if(!is_numeric($key))unset($_POST['delete_weight'][$key]);
			}
		
			$sql = "DELETE FROM weight WHERE id IN (" . implode(',',array_keys($_POST['delete_weight'])) . ")";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// delete heights by nulling
	if(array_key_exists('delete_height',$_POST)){
		if(sizeof($_POST['delete_height'])>0){
			// sanitize is_numeric
			foreach($_POST['delete_height'] as $key => $value){
				if(!is_numeric($key))unset($_POST['delete_height'][$key]);
			}
		
			$sql = "UPDATE measurement SET height_cm=NULL WHERE id IN (" . implode(',',array_keys($_POST['delete_height'])) . ")";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// delete armspans by nulling
	if(array_key_exists('delete_armspan',$_POST)){
		if(sizeof($_POST['delete_armspan'])>0){
			// sanitize is_numeric
			foreach($_POST['delete_armspan'] as $key => $value){
				if(!is_numeric($key))unset($_POST['delete_armspan'][$key]);
			}
		
			$sql = "UPDATE measurement SET armspan_cm=NULL WHERE id IN (" . implode(',',array_keys($_POST['delete_armspan'])) . ")";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// now delete all measurements where height=null and armspan=null, or date is not set
	$sql = "DELETE FROM measurement WHERE ((height_cm IS NULL AND armspan_cm IS NULL) OR date_measured=NULL);";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
}

// Show rower selector if none exists
// TODO - this probably belongs in config eventually
function show_select_rower_page(){
	global $conn, $show_debug;
	global $title_page;

	// get list of rowers
	$sql="SELECT id,name_last,name_first FROM rower WHERE is_active=1 ORDER BY name_last,name_first;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	?>
	<h1><?php echo $title_page; ?></h1>
	<form method="get">
		Select a Rower: 
		<select name="rower_id">
		<?php
			if ($result->num_rows > 0)
				while($row = $result->fetch_assoc())
					echo "<option value='".$row['id']."'>".$row["name_last"]." ".$row["name_first"]."</option>";
		?>
		</select>
		<button type="submit">Edit</button>
	</form>
	<?php
}

function show_measure_rowers_history_page(){
	global $conn, $show_debug;
	global $title_software, $title_page;
	global $rower_id;

	?>
	<html>
		<head>
			<title><?php echo $title_software." : ".$title_page; ?></title>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/measure_rowers_history.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();
	
	// if $rower_id is null then show a dropdown box which posts to this page with a get
	if($rower_id==null) show_select_rower_page();
	else {

		// Show rowers table
		// build table header
		
		// resolve id to a name
		$rower=get_rower($rower_id);
		if($rower==null) show_select_rower_page();
		else {
			?>
			<h1><?php echo $title_page." for ".$rower['name_first']." ".$rower['name_last']; ?></h1>
			<form method="post">
				<table>
					<tr>
						<th>Edit</th>
						<th>Date</th>
						<th>Weight</th>
						<th>Delete Weight</th>
						<th>Height</th>
						<th>Delete Height</th>
						<th>Armspan</th>
						<th>Delete Armspan</th>
					</tr>
			<?php
			
			// get latest measurements
			
			// TODO this query isn't grouping properly!!!!!!
			
			$sql = "SELECT m.rower_id as id,
					m.date_measured as d,
					m.armspan_cm as ma,
					m.height_cm as mh,
					m.id as mi,
					w.weight_kg as mw,
					w.id as wi
				FROM measurement AS m
					LEFT OUTER JOIN weight AS w
					ON m.date_measured=w.date_weighed
						AND m.rower_id=w.rower_id
				WHERE m.rower_id='".$rower_id."'
				UNION
				SELECT
					w.rower_id as id,
					w.date_weighed as d,
					m.armspan_cm as ma,
					m.height_cm as mh,
					m.id as mi,
					w.weight_kg as mw,
					w.id as wi
				FROM weight as w
					LEFT OUTER JOIN measurement AS m
					ON w.date_weighed=m.date_measured
						AND w.rower_id=m.rower_id
				WHERE w.rower_id='".$rower_id."'
				ORDER BY d ASC;";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
			
			if ($result->num_rows > 0) {
				$index=0;
				// output data of each row
				while($row = $result->fetch_assoc()) {
					echo "<tr>"
						. "<td><button class='button-edit-measurement' type='button' data-id='" . $index . "' data-wid='" . $row["wi"] . "' data-mid='" . $row["mi"] . "' data-d='" . $row["d"] . "' >edit</button></td>"
						. "<td>" . $row["d"] . "</td>"
						. "<td>" . $row["mw"] . "</td>"
						. ($row["mw"]!="" ?
							"<td><label id='delete-weight-" . $row["wi"] . "'><input type='checkbox' name='delete_weight[".$row["wi"]."]' value='1' />delete</label></td>"
							: "<td></td>")
						. "<td>" . $row["mh"] . "</td>"
						. ($row["mh"]!="" ?
							"<td><label id='delete-height-" . $row["mi"] . "'><input type='checkbox' name='delete_height[".$row["mi"]."]' value='1' />delete</label></td>"
							: "<td></td>")
						. "<td>" . $row["ma"] . "</td>"
						. ($row["ma"]!="" ?
							"<td><label id='delete-armspan-" . $row["mi"] . "'><input type='checkbox' name='delete_armspan[".$row["mi"]."]' value='1' />delete</label></td>"
							: "<td></td>")
						. "</tr>";
					$index++;
				}
			} else {
				echo "<tr><td>No Measurements to Display</td></tr>";
			}
			// finish table
			?>
						<tr>
							<td><button id="button-new-measurement" type="button">+</button></td>
						</tr>
					</table>
					<button type="submit" name="action" value="save">Save Changes</button>
				</form>
			<?php
		}
	}
	
	// end page
	?>
			</body>
		</html>
	<?php
}

// connect to the database
connect_db();

// Determine which rower from the get request
if(isset($_GET) && array_key_exists('rower_id',$_GET) && is_numeric($_GET['rower_id']))$rower_id=$_GET['rower_id'];
// otherwise leave as null

// Handle POST action
if($rower_id!=null && isset($_POST) && array_key_exists('action',$_POST)){
	switch($_POST['action']){
		case 'save':
			save_rowers_measurements();
			break;
	}
}

// Show page
show_measure_rowers_history_page();

$conn->close();

?>

