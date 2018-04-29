var new_measurements=0;


$(document).ready( function () {

	$(".button-edit-measurement").click(function () {
		var $button_edit=$(this);
		
		// get ids
		var id = $button_edit.data("id");
		var wid = $button_edit.data("wid");
		var mid = $button_edit.data("mid");
		var d = $button_edit.data("d");
		
		// see if an edit row exists, if it does, do nothing
		if(!$("#tr-edit-"+id).length){
			// select delete label
			var $delete_labels = $("#delete-weight-"+wid+",#delete-height-"+mid+",#delete-armspan-"+mid);
			
			// uncheck checkbox
			$delete_labels.children().prop('checked', false);
			
			// hide
			$button_edit.hide();
			$delete_labels.hide();
			
			// TODO!!!
			// insert edit row
			$button_edit.parent().parent().after("<tr id='tr-edit-" + id + "'>"
				+ "<td><button id='button-cancel-edit-" + id + "' type='button' >cancel</button></td>"
				+ "<td><input type='date' name='update_measurement_date[" + id + "]' ></input></td>"
				+ "<td><input type='number' name='update_weight[" + id + "]' ></input></td><td></td>"
				+ "<td><input type='number' name='update_height[" + id + "]' ></input></td><td></td>"
				+ "<td><input type='number' name='update_armspan[" + id + "]' ></input></td><td></td>"
				+ "<input type='hidden' name='wid[" + id + "]' value='" + wid + "' />"
				+ "<input type='hidden' name='mid[" + id + "]' value='" + mid + "' />"
				+ "<input type='hidden' name='d[" + id + "]' value='" + d + "' />"
				+ "</tr>");
				
			// add function to cancel editing
			$("#button-cancel-edit-"+id).click(function () {
				// cancel edit
				$(this).parent().parent().remove();
				// show edit button
				$button_edit.show();
				$delete_labels.show();
			});
		}
	});

	$("#button-new-measurement").click(function () {
		// get parent
		$(this).parent().parent().before("<tr>"
			// removal button
			+ "<td><button id='button-remove-measurement-"+new_measurements+"' type='button'>x</button></td>"
			+ "<td><input type='date' name='new_measurement_date[]' ></input></td>"
			+ "<td><input type='number' name='new_weight[]' ></input></td><td></td>"
			+ "<td><input type='number' name='new_height[]' ></input></td><td></td>"
			+ "<td><input type='number' name='new_armspan[]' ></input></td><td></td>"
			+" </tr>");
		
		// add function to remove button
		$("#button-remove-measurement-"+new_measurements).click(function () {
			$(this).parent().parent().remove();
		});		
		
		new_measurements++;
	});
	
});