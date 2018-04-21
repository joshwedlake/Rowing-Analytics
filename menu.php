<?php

// display menu items

function show_menu(){
	if(TRUE){
		?>
			<div class="menu-inline">
				<h2>People</h2>
				<div><a href="edit_rowers.php">Rowers</a></div>
				<div><a href="measure_rowers.php">Measure Rowers</a></div>
				<div><a href="edit_squads.php">Squads</a></div>
				<div><a href="edit_coaches.php">Coaches</a></div>
			</div>
			<div class="menu-inline">
				<h2>Plant</h2>
				<div><a href="edit_boats.php">Boats & Ergs</a></div>
				<div><a href="edit_rigs.php">Rigs</a></div>
				<div><a href="edit_locations.php">Locations</a></div>
			</div>
			<div class="menu-inline">
				<h2>Train</h2>
				<div><a href="edit_schedule.php">Schedule</a></div>
				<div><a href="edit_sessions.php">Sessions</a></div>
				<div><a href="edit_results.php">Results</a></div>
			</div>
			<div class="menu-inline">
				<h2>Analyse</h2>
				<div></div>
			</div>
			<div class="menu-inline">
				<h2>Configure</h2>
				<div><a href="edit_seasons.php">Season</a></div>
			</div>
			<br class="menu-clear" />
		<?php
	}
}
?>