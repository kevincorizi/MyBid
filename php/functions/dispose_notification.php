<?php
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
    // Get the username: one specific user can only delete his own notification
    $username = $_SESSION['username'];
    // Get the ID of the notification to be deleted
    $notification_id = $_POST['notification_id'];
    // username must be an email
    if (filter_var($username, FILTER_VALIDATE_EMAIL) == false) {
        throw new Exception(json_encode(array("status" => "notification_error", "value" => "Invalid parameters in notification delete", "time" => toDate(date('Y-m-d H:i:s'), 'long'))));
    }
    // notification_id must be a number greater than 0 (by definition in SQL)
    $notification_id = intval($notification_id);
    if ($notification_id == false) {
        throw new Exception(json_encode(array("status" => "notification_error", "value" => "Invalid parameters in notification delete", "time" => toDate(date('Y-m-d H:i:s'), 'long'))));
    }

    $conn = new DatabaseInterface();
    $conn->start_transaction();
    $transaction_started = true;

    $target_notification = get_notifications($conn->query("SELECT * FROM notifications WHERE id=$notification_id FOR UPDATE;"));
    // Check if the notification ID is valid
    if (count($target_notification) != 1) {
        // Auction ID was tampered during the execution of the script (maybe via javascript)?
        throw new Exception(json_encode(array("status" => "notification_error", "value" => "Invalid notification ID", "time" => toDate(date('Y-m-d H:i:s'), 'long'))));
    }
    // Check if the notification belongs to the user (permission to delete is not granted otherwise)
    if ($target_notification[0]->user != $username) {
        // Offer cannot be placed because it is smaller than current BID
        throw new Exception(json_encode(array("status" => "notification_error", "value" => "You cannot delete this notification", "time" => toDate(date('Y-m-d H:i:s'), 'long'))));
    }
    // If the script if here, the notification can be safely deleted by the user
    $conn->query("DELETE FROM notifications WHERE id=$notification_id;");
    $conn->end_transaction();
    echo json_encode(array("status" => "notification_deleted", "value" => "The notification was succesfully deleted", "time" => toDate(date('Y-m-d H:i:s'), 'long')));

} catch (Exception $e) {
    if ($transaction_started) {
        $conn->rollback_transaction();
    }
    if (!json_decode($e->getMessage())) {
        // An exception thrown by the database or by an uncontrolled environment
        echo json_encode(array("status" => "notification_error", "value" => "Server error in notification delete", "time" => toDate(date('Y-m-d H:i:s'), 'long')));
    } else {
        echo $e->getMessage();
    }
}
?>