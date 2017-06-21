<?php
require_once "../../config.php";
require_once DIR_PHP_FUNCTIONS . 'db_manager.php';
require_once DIR_PHP_FUNCTIONS . 'lib.php';

$username = $_POST['username'];
// Username must be an email
if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    // Notify invalid email
    echo -1;
    exit();
}

try {
    $conn = new DatabaseInterface();
    // Get the username
    $username = $conn->secure($username);
    // mysql query to select field username if it's equal to the username that we check
    $result = get_users($conn->query("SELECT * FROM users WHERE email='$username';"));
    //if number of rows fields is bigger them 0 that means the username exists
    echo count($result);
} catch (Exception $e) {
    // Notify database error
    echo -2;
}
?>