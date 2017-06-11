function show_thri_popup(auctionid) {
    var new_thri = prompt("Please enter your new offer");

    if (new_thri != null) {
        $.post("/php/functions/update_thri.php",
            {auction: auctionid, thri: new_thri },
            function(result){
                console.log(result);
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
        );
    }
}