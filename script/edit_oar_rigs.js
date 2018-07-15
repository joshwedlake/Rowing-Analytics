// note skeleton code copied from edit_squads
// make sure any edits/fixes are copied across

var new_oarrigs=0;

$(document).ready( function () {

	$(".button-edit-oarrigs").click(function () {
		$button_edit=$(this);
		
		// get id
		var id = $button_edit.data("id");
		
		// see if an edit row exists, if it does, do nothing
		if(!$("#tr-edit-"+id).length){
			// select delete label
			var $delete_label = $("#delete-oarrigs-"+id);
			
			// uncheck checkbox
			$delete_label.children().prop('checked', false);
			
			// hide
			$button_edit.hide();
			$delete_label.hide();
			
			// insert edit row
			$button_edit.parent().parent().after("<tr id='tr-edit-" + id + "' class='tr-edit'>"
				+ "<td><button id='button-cancel-edit-" + id + "' type='button' >cancel</button></td>"
				+ "<td></td><td></td><td><input type='text' name='update_description[" + id + "]' ></input></td>"
				+ "<td><input type='number' step='0.1' name='update_overall_cm[" + id + "]' ></input></td>"
				+ "<td><input type='number' step='0.1' name='update_inboard_cm[" + id + "]' ></input></td>"
				+ "</tr>");
				
			// add function to cancel editing
			$("#button-cancel-edit-"+id).click(function () {
				// cancel edit
				$(this).parent().parent().remove();
				// show edit button
				$button_edit.show();
				$delete_label.show();
			});
		}
	});
	
	$("#button-new-oarrigs").click(function () {
		// get parent
		$(this).parent().parent().before("<tr class='tr-new'>"
			// removal button
			+ "<td><button id='button-remove-oarrigs-"+new_oarrigs+"' type='button'>x</button></td>"
			+ "<td></td><td></td><td><input type='text' name='description[]' ></input></td>"
			+ "<td><input type='number' step='0.1' name='overall_cm[]' ></input></td>"
			+ "<td><input type='number' step='0.1' name='inboard_cm[]' ></input></td>"
			+ " </tr>");
		
		// add function to remove button
		$("#button-remove-oarrigs-"+new_oarrigs).click(function () {
			$(this).parent().parent().remove();
		});		
		
		new_oarrigs++;
	});
	
	$(".button-move-up").click(function () {
		// get this row and previous row
		var $this_row=$(this).parent().parent();
		
		// find previous row and possible edit row
		var $edit_row=$this_row.nextAll('.tr-edit');
		var $prev_row=$this_row.prevAll('.tr-display');
		
		// move row
		if($prev_row.length>0){
			$prev_row=$prev_row.first();
		
			$this_row.insertBefore($prev_row);
			if($edit_row.length>0 && $edit_row[0].id==('tr-edit-'+$this_row.data('id')))$edit_row.first().insertAfter($this_row);
		
			// update index
			var $this_index_input=$this_row.find('.input-display-index')[0];
			var $swap_index_input=$prev_row.find('.input-display-index')[0];
			
			var i=$this_index_input.value;
			$this_index_input.value=$swap_index_input.value;
			$swap_index_input.value=i;
			$this_index_input.disabled=false;
			$swap_index_input.disabled=false;
		}
	});
	
	$(".button-move-down").click(function () {
		// get this row and previous row
		var $this_row=$(this).parent().parent();
		
		// find previous row and possible edit row
		var $edit_row=$this_row.nextAll('.tr-edit');
		var $next_row=$this_row.nextAll('.tr-display');
		
		// move row
		if($next_row.length>0){
			$next_row=$next_row.first();
			
			// should we be moving to after an edit row?
			var $next_edit_row=$next_row.nextAll('.tr-edit');
			if($next_edit_row.length>0 && $next_edit_row[0].id==('tr-edit-'+$next_row.data('id'))) $this_row.insertAfter($next_edit_row.first());
			else $this_row.insertAfter($next_row);
			
			// do we have an edit row to move?
			if($edit_row.length>0 && $edit_row[0].id==('tr-edit-'+$this_row.data('id')))$edit_row.first().insertAfter($this_row);
		
			// update index
			var $this_index_input=$this_row.find('.input-display-index')[0];
			var $swap_index_input=$next_row.find('.input-display-index')[0];
			
			var i=$this_index_input.value;
			$this_index_input.value=$swap_index_input.value;
			$swap_index_input.value=i;
			$this_index_input.disabled=false;
			$swap_index_input.disabled=false;
		}
	});
	
	
});