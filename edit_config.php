<?php

include 'common.php';
include 'menu.php';

$title_page = 'Edit Configuration';

// Config

// load_config lives in common.php

// Global variables
$seasons=null;

// Functions

function save_config(){
	global $conn, $show_debug;
	
	// should only be one entry to update
	
	// are we updating the current season id?
	if(array_key_exists('current_season_id',$_POST) && is_numeric($_POST['current_season_id'])){
		$sql = "UPDATE config SET current_season_id=".$_POST['current_season_id']." WHERE id = 0;";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
	
	// update day schoolyear begins
	if(array_key_exists('day_schoolyear_begins',$_POST) && is_numeric($_POST['day_schoolyear_begins'])){
		// sanity check day
		if(array_key_exists('month_schoolyear_begins',$_POST) && is_numeric($_POST['month_schoolyear_begins'])){
			if($_POST['month_schoolyear_begins']==2){
				// limit to 29 days
				if($_POST['day_schoolyear_begins']>29)$_POST['day_schoolyear_begins']=29;
			}
			else if(in_array($_POST['month_schoolyear_begins'], array(4,6,9,11))){
				// limit to 30 days
				if($_POST['day_schoolyear_begins']>30)$_POST['day_schoolyear_begins']=30;
			}
			else {
				// limit to 31 days
				if($_POST['day_schoolyear_begins']>31)$_POST['day_schoolyear_begins']=31;
			}
		}
	
		$sql = "UPDATE config SET day_schoolyear_begins=".$_POST['day_schoolyear_begins']." WHERE id = 0;";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
	
	// update month schoolyear begins
	if(array_key_exists('month_schoolyear_begins',$_POST) && is_numeric($_POST['month_schoolyear_begins'])){
		$sql = "UPDATE config SET month_schoolyear_begins=".$_POST['month_schoolyear_begins']." WHERE id = 0;";
		$result = $conn->query($sql);
		if($show_debug && !$result)echo mysqli_error($conn);
	}
	
}

function load_seasons(){
	global $conn, $show_debug, $seasons;
	
	$seasons=array();
	
	$sql = "SELECT id,
			description,
			date_begins,
			date_agegroup
		FROM season;";
	$result = $conn->query($sql);
	if($show_debug && !$result)echo mysqli_error($conn);
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			if(is_numeric($row["id"])){
				$seasons[$row["id"]]=array(
					"description" => $row["description"],
					"date_begins" => $row["date_begins"],
					"date_agegroup" => $row["date_agegroup"]
				);
			}
		}
	}
}

function show_config_page(){
	global $conn,$show_debug;
	global $title_software, $title_page;
	global $seasons;
	global $config_current_season_id,
		$config_month_schoolyear_begins,
		$config_day_schoolyear_begins;

	?>
	<html>
		<head>
			<title><?php echo $title_software." : ".$title_page; ?></title>
			<script src="script/jquery-3.3.1.min.js"></script>
			<link rel="stylesheet" type="text/css" href="style/main.css">
		</head>
		<body>
	<?php

	// show top menu
	show_menu();

	// Show seasons table
	// build table header
	?>
	<h1><?php echo $title_page; ?></h1>
	<form method="post">
	<?php
	
	// PHP code to build config form here
	
	// load config
	load_config();
	
	// get seasons list to $seasons
	load_seasons();
	
	?>
				<div>
					Default Season: 
					<select name="current_season_id">
					<?php
						foreach($seasons as $season_id => $season){
							echo "<option value='".$season_id."'"
								. ( $config_current_season_id==$season_id ? " selected='selected' " : "")
								.">"
								.$season["description"]
								.", beginning "
								.$season["date_begins"]
								.", age on "
								.$season["date_agegroup"]
								."</option>";
						
						}
					?>
					</select>
				</div>
				<div>
					School Year Begins:
					<input type="number" name="day_schoolyear_begins" value="<?php echo ($config_day_schoolyear_begins!=""?$config_day_schoolyear_begins:"1"); ?>" min="1" max="31" />
					<select name="month_schoolyear_begins">
						<option value='1' <?php if($config_month_schoolyear_begins==1)echo "selected='selected'"; ?>>January</option>
						<option value='2' <?php if($config_month_schoolyear_begins==2)echo "selected='selected'"; ?>>February</option>
						<option value='3' <?php if($config_month_schoolyear_begins==3)echo "selected='selected'"; ?>>March</option>
						<option value='4' <?php if($config_month_schoolyear_begins==4)echo "selected='selected'"; ?>>April</option>
						<option value='5' <?php if($config_month_schoolyear_begins==5)echo "selected='selected'"; ?>>May</option>
						<option value='6' <?php if($config_month_schoolyear_begins==6)echo "selected='selected'"; ?>>June</option>
						<option value='7' <?php if($config_month_schoolyear_begins==7)echo "selected='selected'"; ?>>July</option>
						<option value='8' <?php if($config_month_schoolyear_begins==8)echo "selected='selected'"; ?>>August</option>
						<option value='9' <?php if($config_month_schoolyear_begins==9)echo "selected='selected'"; ?>>September</option>
						<option value='10' <?php if($config_month_schoolyear_begins==10)echo "selected='selected'"; ?>>October</option>
						<option value='11' <?php if($config_month_schoolyear_begins==11)echo "selected='selected'"; ?>>November</option>
						<option value='12' <?php if($config_month_schoolyear_begins==12)echo "selected='selected'"; ?>>December</option>
					</select>
				</div>
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
			save_config();
			break;
	}
}

// Show page

show_config_page();

$conn->close();

?>

