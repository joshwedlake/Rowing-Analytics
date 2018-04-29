<?php

include 'common.php';
include 'menu.php';

$title_page='Edit Rowers';

// Functions

function save_rowers(){
	global $conn, $show_debug;
	// load from $_POST[]
	
	// are we adding new rowers?
	if(array_key_exists('name_last',$_POST)){
		$values_strings = array();
		$values_string="";
		
		foreach($_POST['name_last'] as $key => $name_last ){
			// sanitize
			$name_last = $conn->real_escape_string($name_last);
			$name_first = $conn->real_escape_string($_POST['name_first'][$key]);
			$date_birth = $_POST['date_birth'][$key] == "" ? "NULL" : "'".date('Y-m-d', strtotime($_POST['date_birth'][$key]))."'";
			$schoolyear_offset = (is_numeric($_POST['schoolyear_offset'][$key]) ? $_POST['schoolyear_offset'][$key] : 0);
			
			$values_strings[] = "('" . $name_last . "','" . $name_first . "'," . $date_birth . "," . $schoolyear_offset . ",NULL,NULL)";
		}
		
		// insert here
		$values_string = implode(',',$values_strings);
		$sql = "INSERT INTO rower (name_last,name_first,date_birth,schoolyear_offset,season_joined_id,season_novice_id) values " . $values_string;
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
	
	// see if any existing entries need editing
	if(array_key_exists('update_name_last',$_POST)){
		$updates = array();
		
		foreach($_POST['update_name_last'] as $key => $update_name_last ){
			$update_name_first=$_POST['update_name_first'][$key];
			$update_date_birth=$_POST['update_date_birth'][$key];
			$update_schoolyear_offset = $_POST['update_schoolyear_offset'][$key];
			
			if($update_name_last!="")$updates[]="name_last='".$conn->real_escape_string($update_name_last)."'";
			if($update_name_first!="")$updates[]="name_first='".$conn->real_escape_string($update_name_first)."'";
			if($update_date_birth!=""){
				$update_date_birth=date('Y-m-d', strtotime($_POST['update_date_birth'][$key]));
				if($update_date_birth!=false)$updates[]="date_birth='".$update_date_birth."'";
			}
			if($update_schoolyear_offset!="" && is_numeric($update_schoolyear_offset))$updates[]="schoolyear_offset='".$update_schoolyear_offset."'";
			
			if(sizeof($updates)>0){
				$sql = "UPDATE rower SET " . implode(',',$updates) . " WHERE id = " . $key . ";";
				$result = $conn->query($sql);
				if($show_debug && !$result)echo mysqli_error($conn);
			}
		}
	}
	
	// do any entries need activating
	if(array_key_exists('enable_rower',$_POST)){
		if(sizeof($_POST['enable_rower'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['enable_rower'] as $key => $value){
				if(!is_numeric($key))unset($_POST['enable_rower'][$key]);
			}
			$sql = "UPDATE rower SET is_active=1 WHERE id IN (" . implode(',',array_keys($_POST['enable_rower'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// do any entries need deactivating
	if(array_key_exists('disable_rower',$_POST)){
		if(sizeof($_POST['disable_rower'])>0){
			// sanitize by deleting non numeric keys
			foreach($_POST['disable_rower'] as $key => $value){
				if(!is_numeric($key))unset($_POST['disable_rower'][$key]);
			}
			$sql = "UPDATE rower SET is_active=0 WHERE id IN (" . implode(',',array_keys($_POST['disable_rower'])) . ");";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
	
	// find out if any delete boxes were ticked
	if(array_key_exists('delete',$_POST)){
		// sanitize by deleting non numeric keys
		foreach($_POST['delete'] as $key => $value){
			if(!is_numeric($key))unset($_POST['delete'][$key]);
		}
	
		if(sizeof($_POST['delete'])>0){
			$sql = "DELETE FROM rower WHERE id IN (" . implode(',',array_keys($_POST['delete'])) . ")";
			$result = $conn->query($sql);
			if($show_debug && !$result)echo mysqli_error($conn);
		}
	}
}

function show_rowers_page(){
	global $conn, $show_debug;
	global $title_software, $title_page;
	global $config_season_date_agegroup,
		$config_month_schoolyear_begins,
		$config_day_schoolyear_begins;

	?>
	<html>
		<head>
			<title><?php echo $title_software." : ".$title_page; ?></title>
			<script src="script/jquery-3.3.1.min.js"></script>
			<script src="script/edit_rowers.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show rowers table
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
				<th>DOB</th>
				<th>Age Group</th>
				<th>School Year</th>
				<th>School Year Offset</th>
				<th>Active</th>
			</tr>
	<?php
	
	$sql = "SELECT rower.id as id,
			name_last,
			name_first,
			date_birth,
			is_active,
			floor((season.date_agegroup-date_birth)/10000) as age_group,
			floor(((
						if(month(now())>config.month_schoolyear_begins OR
								(month(now())=config.month_schoolyear_begins AND day(now())>=config.day_schoolyear_begins)
							,year(now()),year(now())-1)*10000)
						+(config.month_schoolyear_begins*100)+config.day_schoolyear_begins-date_birth)
					/10000)
                -5+schoolyear_offset as schoolyear,
			schoolyear_offset
		FROM rower
		JOIN config
		JOIN season on config.current_season_id=season.id
		ORDER BY name_last,name_first;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<tr>"
				. "<td><button class='button-edit-rower' type='button' data-id='" . $row["id"] . "' >edit</button></td>"
				. "<td><label id='delete-rower-" . $row["id"] . "'><input type='checkbox' name='delete[".$row["id"]."]' value='1' />delete</label></td>"
				. "<td>" . $row["id"] . "</td>"
				. "<td>" . $row["name_last"] . "</td>"
				. "<td>" . $row["name_first"] . "</td>"
				. "<td>" . $row["date_birth"] . "</td>"
				. "<td>" . $row["age_group"] . "</td>"
				. "<td>" . $row["schoolyear"] . "</td>"
				. "<td>" . $row["schoolyear_offset"] . "</td>"
				. "<td>" 
					. ($row["is_active"]==1 ?
						"Active <label><input type='checkbox' name='disable_rower[".$row["id"]."]' value='1' />disable</label>"
						: "Inactive <label><input type='checkbox' name='enable_rower[".$row["id"]."]' value='1' />enable</label>" )
					. "</td>"
				. "</tr>";
		}
	} else {
		echo "<tr><td>No Rowers</td></tr>";
	}
	?>
					<tr>
						<td><button id="button-new-rower" type="button">+</button></td>
					</tr>
				</table>
				<button type="submit" name="action" value="save">Save</button>
			</form>
			<script>
				// Convert PHP date to Javascript
				var season_date_agegroup=new Date("<?php
					$sd = new DateTime($config_season_date_agegroup);
					echo $sd->format('D M d Y'); 
				?>");
				
				var date_schoolyear_begins=new Date("<?php 
					$date_now=new DateTime();
					$year_now = (int)$date_now->format('Y');
					$month_now = (int)$date_now->format('n');
					$day_now = (int)$date_now->format('j');
					
					if($month_now>$config_month_schoolyear_begins
							|| ($month_now==$config_month_schoolyear_begins && $day_now>=$config_day_schoolyear_begins))
						$year_schoolyear_begins=$year_now;
					else $year_schoolyear_begins=$year_now-1;
					
					$date_schoolyear_begins=DateTime::createFromFormat('Y-n-j', $year_schoolyear_begins."-".$config_month_schoolyear_begins."-".$config_day_schoolyear_begins);
					
					echo $date_schoolyear_begins->format('D M d Y');
				?>");
				
				
				
			</script>
		</body>
	</html>
	<?php
}

// connect to the database
connect_db();

load_config();

// Handle POST action
if(isset($_POST) && array_key_exists('action',$_POST)){
	switch($_POST['action']){
		case 'save':
			save_rowers();
			break;
	}
}

// Show page
show_rowers_page();

$conn->close();

?>

