<?php
    /* Utility functions for session handling */
    function start_session(){
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    /* $_SESSION global variable initializer */
    function session_fields($resultSet){
        $_SESSION['username'] = $resultSet[0]->username;
    }

    function is_logged(){
        return (isset($_SESSION['username']));
    }

    function logout(){
        if (isset($_SESSION)) {
            session_destroy();
            header('Location: ../../index.php');
        }
    }
?>