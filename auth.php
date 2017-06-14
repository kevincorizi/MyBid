<?php
    require_once 'config.php';
    require_once DIR_PHP_FUNCTIONS.'force_https.php';
    require_once DIR_PHP_FUNCTIONS . 'session_manager.php';
    require_once DIR_PHP_FUNCTIONS . 'db_manager.php';
    require_once DIR_PHP_FUNCTIONS . 'lib.php';

    /* If the user is already logged in, redirect to home page */
    start_session();
    if(is_logged()){
        redirect('index.php');
    }

    $auth_error = "";

    /* If user wants to login */
    if (isset($_POST['submit_login'])) {
		if($_POST['username_login'] != "" && $_POST['password_login'] != "") {
		    // If the checks are passed
		    $conn = new DatabaseInterface();
			$username = $conn->secure($_POST['username_login']);
			$password = md5($_POST['password_login']);
			
			$result_set = get_users($conn->query("SELECT * FROM users WHERE email='$username';"));
			
			if(count($result_set) != 1){
				$auth_error = "username";
			}
			else{
				if($result_set[0]->password == $password){
					session_fields($result_set);
					redirect('index.php');
				}
				else{
					$auth_error = "password";
				}
			}
		} else {
			$auth_error = "login_blank_fields";
		}
    }
    /* If the user wants to sign up */
    else if(isset($_POST['submit_register'])){
		if($_POST['username_register'] != "" && $_POST['password_register'] != "") {
		    // If the checks passed
		    $conn = new DatabaseInterface();
			$username = $conn->secure($_POST['username_register']);
			$password = md5($_POST['password_register']);

			$previous_user = get_users($conn->query("SELECT * FROM users WHERE email='$username';"));
			if(count($previous_user) == 0){
				$register_query = "INSERT INTO users (email, password) VALUES ('$username','$password');";
				$result = $conn->query($register_query);			
				if($result == FALSE){
					$auth_error = "register";
				}
				else{
					$login_query = "SELECT * FROM users WHERE email='$username';";
					$result_login = get_users($conn->query($login_query));
					session_fields($result_login);
					redirect('index.php');
				}
			}
			else{
				$auth_error = "duplicate";
			}
		} else {
			$auth_error = "register_blank_fields";
		}
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>PoliBid - Login</title>
    <?php require_once('./php/fragments/head_tags.php'); ?>
</head>
<body onload="onload_handler()">
    <?php require_once('./php/fragments/header.php'); ?>
    <main id="auth_main">
        <div id='login_panel'>
            <p class='message_header'>Login</p>
            <?php if($auth_error == "username"): ?>
                <div class="message_container error_message_container">
                    <p class="message_header">Error</p>
                    <p class="message_text">The username does not exist. Want to join us? Register here!</p>
                </div>
            <?php elseif ($auth_error == "password"): ?>
                <div class="message_container error_message_container">
                    <p class="message_header">Error</p>
                    <p class="message_text">Wrong password, please try again.</p>
                </div>
            <?php endif; ?>
            <form id='login' action='auth.php' method='POST'>
                <input class='large_field' type='email' name='username_login' class='login_input' required placeholder='Username'>
                <input class='large_field' type='password' name='password_login' class='login_input' required placeholder='Password'>
                <button type='submit' name='submit_login' class='button large_button'>Login</button>
            </form>
        </div>
        <div id='register_panel'>
            <p class='message_header'>Still not registered? Sign up now!</p>
            <?php if($auth_error == "register"): ?>
                <div class="message_container error_message_container">
                    <p class="message_header">Error</p>
                    <p class="message_text">An error occurred during the registration process. Please try again.</p>
                </div>
            <?php elseif ($auth_error == "duplicate"): ?>
                <div class="message_container error_message_container">
                    <p class="message_header">Error</p>
                    <p class="message_text">This username is already use. Please choose another.</p>
                </div>
            <?php endif; ?>
            <form id='register' action='auth.php' method='POST' onsubmit="return validate_register();">
                <input class='large_field' type='email' name='username_register' maxlength=45 required placeholder='Email (will be the username)'>
                <input class='large_field' type='password' name='password_register' id='password' maxlength=45 required placeholder='Password'>
                <input class='large_field' type='password' name='repeat_password' id='password_repeat' maxlength=45 required placeholder='Repeat password'>
                <button type='submit' name='submit_register' class='button large_button'>Register</button>
            </form>
        </div>
    </main>
    <?php require_once('./php/fragments/footer.php'); ?>
</body>
</html>