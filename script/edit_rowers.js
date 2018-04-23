var new_rowers=0;


$(document).ready( function () {

	$(".button-edit-rower").click(function () {
		var $button_edit=$(this);
		
		// get id
		var id = $button_edit.data("id");
		
		// see if an edit row exists, if it does, do nothing
		if(!$("#tr-edit-"+id).length){
			// select delete label
			var $delete_label = $("#delete-rower-"+id);
			
			// uncheck checkbox
			$delete_label.children().prop('checked', false);
			
			// hide
			$button_edit.hide();
			$delete_label.hide();
			
			// insert edit row
			$button_edit.parent().parent().after("<tr id='tr-edit-" + id + "'>"
				+ "<td><button id='button-cancel-edit-" + id + "' type='button' >cancel</button></td>"
				+ "<td></td><td></td><td><input type='text' name='update_name_last[" + id + "]' ></input></td>"
				+ "<td><input type='text' name='update_name_first[" + id + "]' ></input></td>"
				+ "<td><input type='date' name='update_date_birth[" + id + "]' ></input></td>"
				+ "<td></td><td></td><td><input type='number' min='0' name='update_schoolyear_offset[" + id + "]' ></input></td>"
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

	$("#button-new-rower").click(function () {
		// get parent
		$(this).parent().parent().before("<tr>"
			// removal button
			+ "<td><button id='button-remove-rower-"+new_rowers+"' type='button'>x</button></td>"
			+ "<td></td><td></td><td><input type='text' name='name_last[]' ></input></td>"
			+ "<td><input type='text' name='name_first[]' ></input></td>"
			+ "<td><input type='date' name='date_birth[]' ></input></td>"
			+ "<td></td><td></td><td><input type='number' min='0' name='schoolyear_offset[]' ></input></td>"
			+" </tr>");
		
		// add function to remove button
		$("#button-remove-rower-"+new_rowers).click(function () {
			$(this).parent().parent().remove();
		});		
		
		new_rowers++;
	});
	
});