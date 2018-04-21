
$(document).ready( function () {

	$(".button-measure-rower").click(function () {
		var $button_measure=$(this);
		
		// get id
		var id = $button_measure.data("id");
		
		// see if a measure row exists, if it does, do nothing
		if(!$("#tr-measure-"+id).length){
			// hide
			$button_measure.hide();
			
			// insert measure row
			$button_measure.parent().parent().after("<tr id='tr-measure-" + id + "'>"
				+ "<td><button id='button-cancel-measure-" + id + "' type='button' >cancel</button></td>"
				+ "<td></td><td></td><td></td>"
				+ "<td><input type='number' name='update_weight[" + id + "]' ></input></td><td></td>"
				+ "<td><input type='number' name='update_height[" + id + "]' ></input></td><td></td>"
				+ "<td><input type='number' name='update_armspan[" + id + "]' ></input></td><td></td>"
				+ "</tr>");
				
			// add function to cancel measuring
			$("#button-cancel-measure-"+id).click(function () {
				// cancel measure
				$(this).parent().parent().remove();
				// show measure button
				$button_measure.show();
			});
		}
	});
	
});