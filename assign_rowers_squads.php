<?php

include 'common.php';
include 'menu.php';

$title_page="Assign Rowers to Squads";

$squads=null;
$rowers=array();
$links=array();

// Functions

function save_assignments(){
	global $conn, $show_debug;
	global $squads;
	
	// load from $_POST[]
	
	// are there any leave or join requests
	foreach($squads as $squad_id=>$squad){
		if(array_key_exists("leave_".$squad_id,$_POST)){
			foreach($_POST["leave_".$squad_id] as $rower_id => $value){
				// instruction to leave $squad_id for $rower_id
				echo "got leave squad ".$squad_id." for rower ".$rower_id;
				// TODO
				// need to get all existing memberships for rower-squad with end date null
				// should only be one
				// if more than one save the top one and drop the rest
				// check end date after start date
				
				
			}
		}
		
		if(array_key_exists("join_".$squad_id,$_POST)){
			foreach($_POST["join_".$squad_id] as $rower_id => $value){
				// instruction to join $squad_id for $rower_id
				echo "got join squad ".$squad_id." for rower ".$rower_id;
				// todo
				// check all existing memberships are closed
				// drop any open memberships
				// check join date doesn't overlap with an existing membership, if it does do nothing
				// do this by running a select statement date < > and checking count 0
				
				
				
			}
		}
	}
}

function load_squads(){
	global $conn, $show_debug, $squads;
	
	$squads=array();
	
	$sql = "SELECT id,description FROM squad ORDER BY display_index ASC;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			if(is_numeric($row["id"])){
				$squads[$row["id"]]=array(
					"description" => $row["description"]
				);
			}
		}
	}
}

function show_assignments_page(){
	global $conn, $show_debug;
	global $title_software, $title_page;
	global $squads, $rowers, $links;

	?>
	<html>
		<head>
			<title><?php echo $title_software." : ".$title_page; ?></title>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/assign_rowers_squads.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show table of rowers by squads
	?>
	<h1><?php echo $title_page; ?></h1>
	<form method="post">
		<table>
			<tr>
				<th>ID</th>
				<th>Last Name</th>
				<th>First Name</th>
				<?php
					foreach($squads as $squad_id=>$squad){
						echo "<th>(".$squad_id.") ".$squad["description"]."</th>";
					}
				?>
			</tr>
	<?php
	
	// get rowers list
	$sql = "SELECT id,name_last,name_first FROM rower ORDER BY name_last,name_first;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	while($row = $result->fetch_assoc()) {
		$rowers[$row['id']]=array();
		$rowers[$row['id']]['name_last']=$row['name_last'];
		$rowers[$row['id']]['name_first']=$row['name_first'];
	}
	
	// get links
	$sql = "SELECT rower.id as id,
			squad_id,
			date_begins
		FROM rower
		JOIN rower_squad_link
			ON rower.id=rower_squad_link.rower_id
		WHERE date_ends IS NULL
			AND date_begins IS NOT NULL
			AND date_begins<now()
		ORDER BY rower.id,squad_id;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	while($row = $result->fetch_assoc()) {
		// add rower to links if missing
		if(!array_key_exists($row['id'],$links))$links[$row['id']]=array();
		// save link
		$links[$row['id']][$row['squad_id']]=$row['date_begins'];
	}
	
	// now build the table
	foreach($rowers as $id=>$rower) {
		// add the rower's row
		echo "<tr>"
			. "<td>" . $id . "</td>"
			. "<td>" . $rower["name_last"] . "</td>"
			. "<td>" . $rower["name_first"] . "</td>";
			
		// for each squad add a cell
		foreach($squads as $squad_id => $squad){
			if(array_key_exists($id,$links) && array_key_exists($squad_id,$links[$id])){
				// exists, show join date and remove button
				echo "<td>"
					."<label><input type='checkbox' name='leave_".$squad_id."[".$id."]' value='1' />leave</label>"
					.", joined on ".$links[$id][$squad_id]
					."</td>";
			}
			else {
				echo "<td><label><input type='checkbox' name='join_".$squad_id."[".$id."]' value='1' />join</label></td>";
			}
		}
		
		// close the row
		echo "</tr>";
	}
	?>
				</table>
				Membership Change Date:<input type='date' name='edit_date' ></input>
				<button type="submit" name="action" value="save">Save</button>
			</form>
		</body>
	</html>
	<?php
}

// connect to the database
connect_db();

// load squads
load_squads();

// Handle POST action
if(isset($_POST) && array_key_exists('action',$_POST)){
	switch($_POST['action']){
		case 'save':
			save_assignments();
			break;
	}
}

// Show page
show_assignments_page();

$conn->close();

?>

