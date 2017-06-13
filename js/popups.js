$(document).ready(function() {
	$('button#show_thri_popup').click(function() {
		// Show the overlay
		$(".overlay").css("visibility", "visible");
	});
	
	$('button#cancel_thri_button').click(function() {
		// Reset the input fields of the form
		$("#new_thri_form form")[0].reset();
		
		// Hide the form
		$(".overlay").css("visibility", "hidden");
	});
	
	$('button#update_thri_button').click(function() {
		var new_thri = $("#thri_value").val();
		var auction_id = $("#new_thri_form form")[0].name.split("_")[1];
		if(isNaN(auction_id)) {
			display_update_result("invalid_auction");
			return;
		}
		if(new_thri.match(/(?:\d+[\.,\,])?\d+/)) {
			update_thri_async(auction_id, new_thri, display_update_result);
		} else {
			console.log("thri vale must be a number");
		}
	});
	
	
});

function update_thri_async(auction_id, new_thri, callback) {
    if (new_thri != null && auction_id != null) {
        return $.post("/php/functions/update_thri.php",
            {auction: auction_id, thri: new_thri },
            function(result){
                callback(result);
            }
        );
    }
}

function display_update_result(result) {
	console.log(result);
	var response = jQuery.parseJSON(result);
	var response_ok = 0;
	var banner_title = "";
	var banner_text = "";
    switch (response.status) {
        case 'invalid_auction':
			response_ok = 0;
			banner_title = "Error";
			banner_text = "We experienced a problem with the auction. Please try again later.";
            break;
        case 'smaller_than_bid':
			response_ok = 0;
			banner_title = "Error";
			banner_text = "You cannot specify a value smaller than the current auction winning bid.";
            break;
        case 'bid_exceeded':
			$("#current_thri_value").text(response.value);
			$("#current_thri_date").text(response.time);
			response_ok = 1;
			banner_title = "Success!";
			banner_text = "The bid value you entered has already been exceeded. You can change your bid as you like.";
            break;
        case 'highest_bidder':
			$("#current_thri_value").text(response.value);
			$("#current_thri_date").text(response.time);
			response_ok = 2;
			banner_title = "Success!";
			banner_text = "You are the current best bidder! Woohoo!";
            break;
        default:
            console.log("Update THR_i: unexpected response from server: " + result);
			response_ok = 0;
			banner_title = "Internal error";
			banner_text = "We are experiencing some technical issues, please try again later!";
            break;
    }
	
	setTimeout(function() {
		// Hide the form
		$(".overlay").css("visibility", "hidden");
	}, 2000);
}

