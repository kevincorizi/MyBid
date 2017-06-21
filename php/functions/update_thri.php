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
require_once DIR_PHP_FUNCTIONS . 'db_manager.php';
require_once DIR_PHP_FUNCTIONS . 'lib.php';

start_session();

if (!is_logged()) {
    redirect("../../auth.php");
}

$transaction_started = false;
try {
    // Get the username
    $username = $_SESSION['username'];
    // Get the auction
    $auction = $_POST['auction'];
    // Get the new THR_i value
    $thri = $_POST['thri'];

    // username must be an email
    if (filter_var($username, FILTER_VALIDATE_EMAIL) == false) {
        throw new Exception(json_encode(array("status" => "thri_error", "value" => "Invalid parameters in bid update", "time" => toDate(date('Y-m-d H:i:s'), 'long'))));
    }
    // auction_id must be a number greater than 0 (by definition in SQL)
    $auction = intval($auction);
    if ($auction == false) {
        throw new Exception(json_encode(array("status" => "thri_error", "value" => "Invalid parameters in bid update", "time" => toDate(date('Y-m-d H:i:s'), 'long'))));
    }
    // thri must be a decimal number
    $thri = floatval($thri);
    if ($thri == false) {
        throw new Exception(json_encode(array("status" => "thri_error", "value" => "Invalid parameters in bid update", "time" => toDate(date('Y-m-d H:i:s'), 'long'))));
    }

    $conn = new DatabaseInterface();
    $conn->start_transaction();
    $transaction_started = true;
    $target_auction = get_auctions($conn->query("SELECT * FROM auction WHERE id=$auction FOR UPDATE;"));
    if (count($target_auction) != 1) {
        // Auction ID was tampered during the execution of the script (maybe via javascript)?
        throw new Exception(json_encode(array("status" => "thri_error", "value" => "Invalid auction ID in bid update", "time" => toDate(date('Y-m-d H:i:s'), 'long'))));
    }

    if ($target_auction[0]->bid > $thri) {
        // Offer cannot be placed because it is smaller than current BID
        throw new Exception(json_encode(array("status" => "smaller_than_bid", "value" => "Your bid is smaller than current winning bid (" . $target_auction[0]->bid . " €)", "time" => toDate(date('Y-m-d H:i:s'), 'long'))));
    }

    // Offer can be accepted, modify the THR_i for current user
    $offers = get_offers($conn->query("SELECT * FROM offer WHERE user='$username' FOR UPDATE"));
    if (count($offers) == 0) {
        $conn->query("INSERT INTO offer (user, auction, value) VALUES ('$username', $auction, $thri);");
    } else {
        $conn->query("UPDATE offer SET value=$thri WHERE user='$username' AND auction=$auction;");
    }
    // After having changed the THR_i, check if the user's bid is maximum or exceeded
    $offers = get_offers($conn->query("SELECT * FROM offer WHERE auction=$auction ORDER BY value DESC, timestamp ASC"));
    if (count($offers) == 1) {
        // If the one we just registered is the only offer for the auction we set the user as the bidder
        $conn->end_transaction();
        echo json_encode(array("status" => "highest_bidder", "value" => "Congrats! Now you are the highest bidder! Your bid is " . $offers[0]->value . " €", "time" => toDate(date('Y-m-d H:i:s'), 'long')));
    } else {
        // There were other offers
        $new_bidder = $offers[0]->user;
        $new_bid_value = ($offers[1]->value != $offers[0]->value) ? $offers[1]->value + 0.01 : $offers[1]->value;
        $conn->query("UPDATE auction SET bid=$new_bid_value, bidder='$new_bidder' WHERE id=$auction;");

        // SEND NOTIFICATIONS TO ALL USERS EXCEPT new_bidder AND THE CURRENT USER (WHICH IS IMMEDIATELY NOTIFIED)
        $users_to_notify = get_users($conn->query("SELECT u.* FROM users u JOIN offer o WHERE u.email=o.user AND u.email!='$username' AND u.email!='$new_bidder';"));
        foreach ($users_to_notify as $user) {
            $query = "INSERT INTO notifications (user, auction, type, message) VALUES ('$user->username', $auction, 'Bid exceeded', '$new_bidder\'s bid exceeded yours for auction " . $target_auction[0]->name . "');";
            $conn->query($query);
        }
        $conn->end_transaction();
        if ($new_bidder == $username) {
            echo json_encode(array("status" => "highest_bidder", "value" => "Congrats! Now you are the highest bidder! Your bid is " . $offers[0]->value . " €", "time" => toDate(date('Y-m-d H:i:s'), 'long')));
        } else {
            echo json_encode(array("status" => "bid_exceeded", "value" => "Your new bid is $thri €, but it was exceeded by $new_bidder's :(", "time" => toDate(date('Y-m-d H:i:s'), 'long')));
        }
    }
} catch (Exception $e) {
    if ($transaction_started) {
        $conn->rollback_transaction();
    }
    if (!json_decode($e->getMessage())) {
        // An exception thrown by the database or by an uncontrolled environment
        echo json_encode(array("status" => "thri_error", "value" => "Server error in bid update", "time" => toDate(date('Y-m-d H:i:s'), 'long')));
    } else {
        echo $e->getMessage();
    }
    exit();
}

?>