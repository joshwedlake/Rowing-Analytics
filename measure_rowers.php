<?php

include 'common.php';
include 'menu.php';

// Functions

function save_rowers_measurements(){
	global $conn;

	// add updated measurements
	if(array_key_exists('update_weight',$_POST)){
		$date_measured = $_POST['measure_date'];
		$updates = array();
		
		// if date not set, assume today
		if($date_measured=="")$date_measured=date('Y-m-d');
		
		foreach($_POST['update_weight'] as $rower_id => $update_weight ){
			$update_height = $_POST['update_height'][$rower_id];
			$update_armspan = $_POST['update_armspan'][$rower_id];
			
			// see if the date given exists, if it does, drop it
			// TODO!!!
			if($update_weight!=""){
				$sql = "INSERT INTO weight (rower_id,weight_kg,date_weighed) "
					."values ('".$rower_id."','".$update_weight."','".$date_measured."');";
				$result = $conn->query($sql);
			}
			
			// see if the date given exists, if it does load the values armspan and height
			// TODO!!!
			// do height and armspan
			if($update_height!="" && $update_armspan!=""){
				$sql = "INSERT INTO measurement (rower_id,height_cm,armspan_cm,date_measured) "
					. "values ('".$rower_id."','".$update_height."','".$update_armspan."','".$date_measured."');";
				$result = $conn->query($sql);
			}
		}
	}
}

function show_measure_rowers_page(){
	global $conn;

	?>
	<html>
		<head>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/measure_rowers.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show rowers table
	// build table header
	?>
	<h1>Measure Rowers</h1>
	<form method="post">
		<table>
			<tr>
				<th>Measure</th>
				<th>ID</th>
				<th>Last Name</th>
				<th>First Name</th>
				<th>Weight</th>
				<th>Weight Recorded</th>
				<th>Height</th>
				<th>Height Recorded</th>
				<th>Armspan</th>
				<th>Armspan Recorded</th>
			</tr>
	<?php
	
	// get latest measurements
	$sql = "SELECT rower.id AS id,
				rower.name_last AS name_last,
				rower.name_first AS name_first,
				wd.mwd AS date_weight_measured,
				w.weight_kg AS weight_kg,
				hd.mhd AS date_height_measured,
				mh.height_cm AS height_cm,
				ad.mad AS date_armspan_measured,
				ma.armspan_cm AS armspan_cm
				
			FROM rower

				LEFT JOIN (
					SELECT rower_id,
						MAX(date_weighed) AS mwd
					FROM weight
					GROUP BY rower_id
				) wd
					ON rower.id=wd.rower_id
					
				LEFT JOIN weight AS w
					ON rower.id=w.rower_id
						AND wd.mwd=w.date_weighed
						
				LEFT JOIN (
					SELECT rower_id,
						MAX(date_measured) AS mhd
					FROM measurement
					WHERE height_cm IS NOT NULL
					GROUP BY rower_id
				) hd
					ON rower.id=hd.rower_id
					
				LEFT JOIN measurement AS mh
					ON rower.id=mh.rower_id
						AND hd.mhd=mh.date_measured
						
				LEFT JOIN (
					SELECT rower_id,
						MAX(date_measured) AS mad
					FROM measurement
					WHERE armspan_cm IS NOT NULL
					GROUP BY rower_id
				) ad
					ON rower.id=ad.rower_id
					
				LEFT JOIN measurement AS ma
					ON rower.id=ma.rower_id
						AND ad.mad=ma.date_measured
				
			GROUP BY rower.id;";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<tr>"
				. "<td><button class='button-measure-rower' type='button' data-id='" . $row["id"] . "' >measure</button></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["name_last"] . "</td>"
				. "<td>" . $row["name_first"] . "</td>"
				. "<td>" . $row["weight_kg"] . "</td>"
				. "<td>" . $row["date_weight_measured"] . "</td>"
				. "<td>" . $row["height_cm"] . "</td>"
				. "<td>" . $row["date_height_measured"] . "</td>"
				. "<td>" . $row["armspan_cm"] . "</td>"
				. "<td>" . $row["date_armspan_measured"] . "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Rowers to Measure</td></tr>";
	}
	?>
				</table>
				Measurement Date:<input type='date' name='measure_date' ></input>
				<button type="submit" name="action" value="save">Save Measurements</button>
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
			save_rowers_measurements();
			break;
	}
}

// Show page

show_measure_rowers_page();

$conn->close();

?>

