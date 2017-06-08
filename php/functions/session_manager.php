<?php
    /* Utility functions for session handling */
    function start_session(){
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    function is_logged(){
        return (isset($_SESSION['id']));
    }

    function logout(){
        if (isset($_SESSION)) {
            session_destroy();
            header('Location: ../../index.php');
        }
    }
?>