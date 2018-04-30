<?php

// BOAT
// is_sweep_scull BIT(2)
//		sweep 10
//		scull 01
//	use this to show available rigs


// RIG

// seats_count: number of seats, max 8 (edit_boats interface then knows what to show it as available for)

// is_sweep_side
//		null for sculling
//		Array of BIT(8) for SIDE


// footsteer_seat_index
//		null for no footsteer

// rig_seat
//		rig_id (foreign key) (UI)
//		seat_index (UI)
//		span_cm
//		feet_through_work_cm
//		height_deck_heel_cm
//		height_deck_seat_cm

// rig_seat_side
//		rig_id (foreign key) (UI)
//		seat_index (UI)
//		side_index (UI)
//			follows constants STROKE=0; BOW=1;
//		height_seat_gate_cm
//		height_water_gate_cm
//		pitch_stern_degrees
//		pitch_out_degrees



	
?>