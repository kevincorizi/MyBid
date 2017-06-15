<?php
    require_once 'config.php';
    require_once DIR_PHP_FUNCTIONS . 'force_https.php';
    require_once DIR_PHP_FUNCTIONS . 'session_manager.php';
    require_once DIR_PHP_FUNCTIONS . 'db_manager.php';
    require_once DIR_PHP_FUNCTIONS . 'lib.php';

    // If the user is already logged in, redirect to home page
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

			// MUST CHECK FOR EMAIL AND PASS REGEX (but must i?)

			// Only reads the user table, so no need for a transaction
			$result = $conn->query("SELECT * FROM users WHERE email='$username';");
			if(!$result) {
			    $auth_error = "A problem occurred while logging you in. Please try again later.";
			    exit();
            }
			$result_set = get_users($result);
			if(count($result_set) != 1){
			    // Username does not exist
				$auth_error = "The username does not exist. Want to join us? Register here!";
				exit();
			}
            if($result_set[0]->password != $password){
			    // Username exists but password is wrong
                $auth_error = "Wrong password, please try again.";
                exit();
            }
            session_fields($result_set);
            redirect('index.php');
		} else {
			$auth_error = "All fields are required for login.";
		}
    }
    /* If the user wants to sign up */
    else if(isset($_POST['submit_register'])){
		if($_POST['username_register'] != "" && $_POST['password_register'] != "") {
		    $conn = new DatabaseInterface();
			$username = $conn->secure($_POST['username_register']);
			$password = md5($_POST['password_register']);

			// MUST CHECK EMAIL AND PASS REGEX

			// We have to read and write according to the read, so we start a transaction
            $conn->start_transaction();
            $result = $conn->query("SELECT * FROM users WHERE email='$username' FOR UPDATE;");
            if(!$result) {
                $conn->rollback_transaction();
                $auth_error = "An error occurred during the registration process. Please try again.";
                exit();
            }
			$previous_user = get_users($result);
			if(count($previous_user) != 0) {
                $auth_error = "This username is already use. Please choose another.";
                $conn->rollback_transaction();
                exit();
            }
            // If the username is available, proceed with registration
            $register_query = "INSERT INTO users (email, password) VALUES ('$username','$password');";
            $result = $conn->query($register_query);
            if(!$result){
                $conn->rollback_transaction();
                $auth_error = "An error occurred during the registration process. Please try again.";
                exit();
            }
            // Here the registration ends, so we can close the transaction
            $conn->end_transaction();

            // After the registration process, automatically login
            $result = $conn->query("SELECT * FROM users WHERE email='$username';");
            if(!$result) {
                $auth_error = "A problem occurred while logging you in. Please try again later.";
                exit();
            }

            $new_user = get_users($result)[0];
            session_fields($new_user);
            redirect('index.php');
		} else {
			$auth_error = "All fields are required for registration.";
		}
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>PoliBid - Login</title>
    <?php require_once('./php/fragments/head_tags.php'); ?>
</head>
<body onload="onload_handler()">
    <?php require_once('./php/fragments/header.php'); ?>
    <main id="auth_main">
        <div id='login_panel'>
            <p class='message_header'>Login</p>
            <?php if($auth_error != ""): ?>
                <div class="message_container error_message_container">
                    <p class="message_header">Error</p>
                    <p class="message_text"><?php echo $auth_error; ?></p>
                </div>
            <?php endif; ?>
            <form id='login' action='auth.php' method='POST'>
                <input class='large_field login_input' type='email' name='username_login' required placeholder='Username'>
                <input class='large_field login_input' type='password' name='password_login' required placeholder='Password'>
                <button type='submit' name='submit_login' class='button large_button'>Login</button>
            </form>
        </div>
        <div id='register_panel'>
            <p class='message_header'>Still not registered? Sign up now!</p>
            <?php if($auth_error != ""): ?>
                <div class="message_container error_message_container">
                    <p class="message_header">Error</p>
                    <p class="message_text"><?php echo $auth_error; ?></p>
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