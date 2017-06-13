<?php
    /* Utility functions for session handling */
    function start_session(){
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    /* $_SESSION global variable initializer */
    function session_fields($result_set){
        $_SESSION['username'] = $result_set[0]->username;
    }

    function is_logged(){
        return (isset($_SESSION['username']));
    }

    function logout(){
		$_SESSION = array();
		if (session_id() != "" || isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time()-2592000, '/');		
		session_destroy();
    }
	
	function check_timeout() {
		
	}
?>