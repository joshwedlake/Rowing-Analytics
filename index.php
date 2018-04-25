<?php

include 'common.php';
include 'menu.php';

$title_page='Home';

function show_home_page(){
	global $conn, $show_debug;
	global $title_software, $title_page;
	
	// build header
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
	
	// show page title
	?><h1><?php echo $title_page; ?></h1><?php
	
	// close page
	?>
		</body>
	</html>
	<?php
}

// begin
show_home_page();

?>