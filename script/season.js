var new_seasons=0;

$(document).ready( function () {

	$(".button-edit-season").click(function () {
		$button_edit=$(this);
		
		// get id
		var id = $button_edit.data("id");
		
		// see if an edit row exists, if it does, do nothing
		if(!$("#tr-edit-"+id).length){
			// select delete label
			var $delete_label = $("#delete-season-"+id);
			
			// uncheck checkbox
			$delete_label.children().prop('checked', false);
			
			// hide
			$button_edit.hide();
			$delete_label.hide();
			
			// insert edit row
			$button_edit.parent().parent().after("<tr id='tr-edit-" + id + "'>"
				+ "<td><button id='button-cancel-edit-" + id + "' type='button' >cancel</button></td>"
				+ "<td></td><td></td><td><input type='text' name='update_description[" + id + "]' ></input></td>"
				+ "<td><input type='date' name='update_date_begins[" + id + "]' ></input></td>"
				+ "<td><input type='date' name='update_date_agegroup[" + id + "]' ></input></td>"
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

	$("#button-new-season").click(function () {
		// get parent
		$(this).parent().parent().before("<tr>"
			// removal button
			+ "<td><button id='button-remove-season-"+new_seasons+"' type='button'>x</button></td>"
			+ "<td></td><td></td><td><input type='text' name='description[]' ></input></td>"
			+ "<td><input type='date' name='date_begins[]' ></input></td>"
			+ "<td><input type='date' name='date_agegroup[]' ></input></td>"
			+" </tr>");
		
		// add function to remove button
		$("#button-remove-season-"+new_seasons).click(function () {
			$(this).parent().parent().remove();
		});		
		
		new_seasons++;
	});
	
});