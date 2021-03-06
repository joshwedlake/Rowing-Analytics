<?php

// display menu items

function show_menu(){
	if(TRUE){
		?>
			<div class="menu-inline">
				<h2>People</h2>
				<div><a href="edit_rowers.php">Rowers</a></div>
				<div><a href="measure_rowers.php">Measure Rowers</a></div>
				<div><a href="measure_rowers_history.php">Edit Previous Measurements</a></div>
				<div><a href="edit_squads.php">Squads</a></div>
				<div><a href="assign_rowers_squads.php">Assign Rowers to Squads</a></div>
				<div><a href="edit_coaches.php">Coaches</a></div>
			</div>
			<div class="menu-inline">
				<h2>Plant</h2>
				<div><a href="edit_boats.php">Edit Boats & Ergs</a></div>
				<div><a href="edit_boat_rigs_history.php">Rig Boats</a></div>
				<div><a href="edit_boat_rigs.php">Edit Shared Boat Rigs</a></div>
				<div><a href="edit_oars.php">Edit Oars</a></div>
				<div><a href="edit_oar_rigs_history.php">Rig Oars</a></div>
				<div><a href="edit_oar_rigs.php">Edit Shared Oar Rigs</a></div>
				<div><a href="edit_locations.php">Edit Locations</a></div>
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
				<div><a href="edit_config.php">Configure</a></div>
				<div><a href="edit_seasons.php">Seasons</a></div>
				<div><a href="edit_activity_types.php">Activity Types</a></div>
				<div><a href="edit_sports.php">Sports</a></div>
				<div><a href="edit_classes.php">Classes & Grades</a></div>
				<div><a href="edit_prognostics.php">Prognostic Targets</a></div>
			</div>
			<br class="menu-clear" />
		<?php
	}
}
?>