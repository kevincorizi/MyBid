<?php
    require_once "../../config.php";
    require_once DIR_PHP_FUNCTIONS . 'session_manager.php';
    require_once DIR_PHP_FUNCTIONS. 'db_manager.php';
    require_once DIR_PHP_FUNCTIONS. 'lib.php';

    start_session();

    if(!is_logged()) {
        redirect("../../auth.php");
    }

    $conn = new DatabaseInterface();

    // Get the username: one specific user can only delete his own notification
    $username = $_SESSION['username'];
    // Get the ID of the notification to be deleted
    $notification_id = $conn->secure($_POST['notification_id']);

    $valid = true;

    // username must be an email
    if(filter_var($username, FILTER_VALIDATE_EMAIL) == false)
        $valid = false;

    // notification_id must be a number greater than 0 (by definition in SQL)
    $notification_id = intval($notification_id);
    if($notification_id == false)
        $valid = false;

    if(!$valid) {
        $response = array("status" => "notification_error", "value"=>"Invalid parameters in notification delete", "time"=>toDate(date('Y-m-d H:i:s'), 'long'));
        echo json_encode($response);
        exit();
    }

    if(!$conn->start_transaction()) {
        $response = array("status" => "notification_error", "value"=>"Server error in notification delete", "time"=>toDate(date('Y-m-d H:i:s'), 'long'));
        echo json_encode($response);
        exit();
    }

    $target_notification = get_notifications($conn->query("SELECT * FROM notifications WHERE id=$notification_id;"));
    if(count($target_notification) != 1) {
        // Auction ID was tampered during the execution of the script (maybe via javascript)?
        $response = array("status" => "notification_error", "value"=>"Invalid notification ID", "time"=>toDate(date('Y-m-d H:i:s'), 'long'));
        echo json_encode($response);
        $conn->end_transaction();
        exit();
    }

    if($target_notification[0]->user != $username) {
        // Offer cannot be placed because it is smaller than current BID
        $response = array("status" => "notification_error", "value"=>"You cannot delete this notification", "time"=>toDate(date('Y-m-d H:i:s'), 'long'));
        echo json_encode($response);
        $conn->end_transaction();
        exit();
    }

    // If the script if here, the notification can be safely deleted by the user
    $delete_query = "DELETE FROM notifications WHERE id=$notification_id;";
    if(!$conn->query($delete_query)) {
        $response = array("status" => "notification_error", "value"=>"Server error in notification delete", "time"=>toDate(date('Y-m-d H:i:s'), 'long'));
        echo json_encode($response);
    } else {
        $response = array("status" => "notification_deleted", "value"=>"The notification was succesfully deleted", "time"=>toDate(date('Y-m-d H:i:s'), 'long'));
        echo json_encode($response);
    }
    $conn->end_transaction();
?>