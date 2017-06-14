<?php
    require_once "../../config.php";
    require_once DIR_PHP_FUNCTIONS. 'db_manager.php';
    require_once DIR_PHP_FUNCTIONS. 'lib.php';

    $conn = new DatabaseInterface();
    // Get the username
    $username = $conn->secure($_POST['username']);
    // Username must be an email
    if(!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        // Notify error
        echo -1;
        exit();
    }

    // mysql query to select field username if it's equal to the username that we check
    $result = get_users($conn->query("SELECT * FROM users WHERE email='$username';"));
    //if number of rows fields is bigger them 0 that means it's NOT available '
    if(count($result)>0){
        //and we send 0 to the ajax request
        echo 0;
    }else{
        //else if it's not bigger then 0, then it's available '
        //and we send 1 to the ajax request
        echo 1;
    }
?>