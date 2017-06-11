<?php
    /**
     * This script performs the update of the value THR_i for the logged user.
     * There are four possible outcomes for this script:
     * 1) invalid_auction: the user is trying to change THR_i for a non-existing auction. This is a protection check to avoid JavaScript tampering
     * 2) smaller_than_bid: the user is trying to change THR_i to a value smaller than the current BID value
     * 3) bid_exceeded: the THR_i specified by the user is valid but it is not the new BID value
     * 4) highest_bidder: the THR_i specified by the user is big enough to make him the new highest bidder in the auction
     */
    require_once "../../config.php";
    require_once DIR_PHP_FUNCTIONS . 'session_manager.php';
    require_once DIR_PHP_FUNCTIONS. 'db_manager.php';
    require_once DIR_PHP_FUNCTIONS. 'lib.php';

    start_session();

    global $conn;

    // Get the username
    $username = $_SESSION['username'];
    // Get the auction
    $auction = $conn->secure($_POST['auction']);
    // Get the new THR_i value
    $thri = $conn->secure($_POST['thri']);

    $target_auction = get_auctions("SELECT * FROM auction WHERE id=".$auction);
    if(count($target_auction) != 1) {
        // Auction ID was tampered during the execution of the script (maybe via javascript)?
        echo 'invalid_auction';
        exit();
    }

    if($target_auction[0]->bid > $thri) {
        // Offer cannot be placed because it is smaller than current BID
        echo 'smaller_than_bid';
        exit();
    }

    // Offer can be accepted, modify the THR_i for current user
    $offers = get_offers("SELECT * FROM offer WHERE user='".$username."'");
    $update_query = '';
    if(count($offers) == 0) {
        $update_query .= "INSERT INTO offer (user, auction, value) VALUES ('".$username."', ".$auction.", ".$thri.");";
    } else {
        $update_query .= "UPDATE offer SET value=".$thri." WHERE user='".$username."' AND auction=".$auction;
    }
    console_log($update_query);
    $result = $conn->query($update_query);

    // After having changed the THR_i, check if the user's bid is maximum or exceeded
    $offers = get_offers("SELECT * FROM offer WHERE auction=".$auction." ORDER BY value DESC, timestamp ASC");
    if(count($offers) == 1) {
        // If the one we just registered is the only offer for the auction we set the user as the bidder
        $query = "UPDATE auction SET bidder='".$username."' WHERE id=".$auction;
        $result = $conn->query($query);
        echo 'highest_bidder';
        exit();
    } else {
        // There were other offers
        $new_bidder = $offers[0]->user;
        $new_bid_value = ($offers[1]->value != $offers[0]->value) ? $offers[1]->value + 0.01 : $offers[1]->value;
        $query = "UPDATE auction SET bid=".$new_bid_value.", bidder='".$new_bidder."' WHERE id=".$auction;
        $result = $conn->query($query);

        // SEND NOTIFICATIONS TO ALL USERS EXCEPT new_bidder AND THE CURRENT USER (WHICH IS IMMEDIATELY NOTIFIED)
        $users_to_notify = get_users("SELECT u.* FROM users u JOIN offer o WHERE u.email=o.user AND u.email!='".$username."' AND u.email!='".$new_bidder."';");
        foreach ($users_to_notify as $user) {
            $query = "INSERT INTO notifications (user, auction, type, message)
                VALUES ('".$user->username."', ".$auction.", 0, 'bid_exceeded')";
            $result = $conn->query($query);
        }

        if($new_bidder == $username) {
            echo 'highest_bidder';
        } else {
            echo 'bid_exceeded';
        }
    }
?>