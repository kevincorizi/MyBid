function show_thri_popup() {
    $(".overlay").css("visibility", "visible");
}

function close_thri_popup() {
    $(".overlay").css("visibility", "hidden");
}

function update_new_thri(auction_id) {
    var new_thri = $("#thri_value").val();
    if(new_thri.match(/(?:\d+[\.,\,])?\d+/)) {
        var result = update_thri_async(auction_id, new_thri, display_update_result);
    }
}

function display_update_result(result) {
    console.log("2)"+result);
    switch (result) {
        case 'invalid_auction':
            break;
        case 'smaller_than_bid':
            break;
        case 'bid_exceeded':
            break;
        case 'highest_bidder':
            break;
        default:
            console.log("Update THR_i: unexpected response from server: " + result);
            break;
    }
}

function cancel_new_thri() {
    $("#new_thri_form form")[0].reset();
    close_thri_popup();
}

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