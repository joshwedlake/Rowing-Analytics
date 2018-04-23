<?php

include 'common.php';
include 'menu.php';

$rower_id=null;
$rower=null;

// Functions
function save_rowers_measurements(){
	global $conn, $show_debug;

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

	// get list of rowers
	$sql="SELECT id,name_last,name_first FROM rower ORDER BY name_last,name_first;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	?>
	<h1>Measurement History Editor</h1>
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
	global $rower_id;

	?>
	<html>
		<head>
			<script src="script/jquery-3.3.1.min.js"></script>
			<!--<script src="script/measure_rowers.js"></script>-->
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
			<h1>Measurement History for <?php echo $rower['name_first']." ".$rower['name_last']; ?></h1>
			<form method="post">
				<table>
					<tr>
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
				// output data of each row
				while($row = $result->fetch_assoc()) {
					echo "<tr>"
						. "<td>" . $row["d"] . "</td>"
						. "<td>" . $row["mw"] . "</td>"
						. ($row["mw"]!="" ?
							"<td><label><input type='checkbox' name='delete_weight[".$row["wi"]."]' value='1' />delete</label></td>"
							: "<td></td>")
						. "<td>" . $row["mh"] . "</td>"
						. ($row["mh"]!="" ?
							"<td><label><input type='checkbox' name='delete_height[".$row["mi"]."]' value='1' />delete</label></td>"
							: "<td></td>")
						. "<td>" . $row["ma"] . "</td>"
						. ($row["ma"]!="" ?
							"<td><label><input type='checkbox' name='delete_armspan[".$row["mi"]."]' value='1' />delete</label></td>"
							: "<td></td>")
						. "</tr>";
				}
			} else {
				echo "<tr><td>No Measurements to Display</td></tr>";
			}
			// finish table
			?>
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

