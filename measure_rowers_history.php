<?php

include 'common.php';
include 'menu.php';

$rower_id=null;

// Functions

function save_rowers_measurements(){
	global $conn, $show_debug;

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
	if($rower_id==null){
	
	
	}
	else {

		// Show rowers table
		// build table header
		
		// TODO !!!! resolve rower_id to a name!!
		?>
		<h1>Measurement History for <?php echo $rower_id; ?></h1>
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
		
		$sql = "SELECT
					w.id AS w_id,
					m.id AS m_id,
					COALESCE(w.date_weighed,m.date_measured) AS date_measured,
					w.weight_kg AS weight_kg,
					m.height_cm AS height_cm,
					m.armspan_cm AS armspan_cm
					
				FROM rower

					LEFT JOIN weight AS w ON rower.id=w.rower_id
					LEFT JOIN measurement AS m ON rower.id=w.rower_id
				
				WHERE rower.id='".$rower_id."';";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
		
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				echo "<tr>"
					. "<td>" . $row["date_measured"] . "</td>"
					. "<td>" . $row["weight_kg"] . "</td>"
					. "<td><label><input type='checkbox' name='delete_weight[".$row["w_id"]."]' value='1' />delete</label></td>"
					. "<td>" . $row["height_cm"] . "</td>"
					. "<td><label><input type='checkbox' name='delete_height[".$row["m_id"]."]' value='1' />delete</label></td>"
					. "<td>" . $row["armspan_cm"] . "</td>"
					. "<td><label><input type='checkbox' name='delete_armspan[".$row["m_id"]."]' value='1' />delete</label></td>"
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

