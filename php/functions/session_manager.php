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
        $_SESSION['last_interaction'] = time();
    }

    function is_logged(){
        // Session timeout set to 2 minutes
        $expiration_time = 2 * 60;
        if(isset($_SESSION['username'])) {
            $now = time();
            // If more than $expiration_time passed we logout and return false
            if($now - $_SESSION['last_interaction'] > $expiration_time) {
                logout();
                return false;
            }
            // Otherwise we update the last_interaction timestamp
            $_SESSION['last_interaction'] = $now;
            return true;
        }
        // If we never logged in, we are sure that the user is not logged
        return false;
    }

    function logout(){
		$_SESSION = array();
		if (session_id() != "" || isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time()-2592000, '/');		
		session_destroy();
    }
?>