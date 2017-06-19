<?php
require_once 'config.php';
require_once DIR_PHP_FUNCTIONS . 'force_https.php';
require_once DIR_PHP_FUNCTIONS . 'session_manager.php';
require_once DIR_PHP_FUNCTIONS . 'db_manager.php';
require_once DIR_PHP_FUNCTIONS . 'lib.php';

// If the user is already logged in, redirect to home page
start_session();
if (is_logged()) {
    redirect('index.php');
}

$login_error = "";
$register_error = "";

/* If user wants to login */
if (isset($_POST['submit_login'])) {
    try {
        if ($_POST['username_login'] == "" || $_POST['password_login'] == "") {
            throw new Exception("All fields are required for login.");
        }
        $username = $_POST['username_login'];
        $password = $_POST['password_login'];
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("The username is not a valid email.");
        }
        if (!preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', $password)) {
            // I know for sure that there will be no password that does not respect this format, so i save one useless DB query
            throw new Exception("The password must contain at least one number and one character");
        }

        // If the checks are passed
        $conn = new DatabaseInterface();
        $username = $conn->secure($username);
        $password = md5($password);

        // Only reads the user table, so no need for a transaction
        $result_set = get_users($conn->query("SELECT * FROM users WHERE email='$username';"));
        if (count($result_set) != 1) {
            // Username does not exist
            throw new Exception("The username does not exist. Want to join us? Register here!");
        }
        if ($result_set[0]->password != $password) {
            // Username exists but password is wrong
            throw new Exception("Wrong password, please try again.");
        }

        session_fields($result_set[0]);
        redirect('index.php');
    } catch (Exception $e) {
        $login_error = $e->getMessage();
    }
} /* If the user wants to sign up */
else if (isset($_POST['submit_register'])) {
    $transaction_started = false;
    try {
        if ($_POST['username_register'] == "" || $_POST['password_register'] == "" || $_POST['password_register_confirm'] == "") {
            throw new Exception("All fields are required for registration.");
        }

        $username = $_POST['username_register'];
        $password = $_POST['password_register'];
        $confirm = $_POST['password_register_confirm'];

        // I need to check all of this, because in case javascript is disabled the browser
        // cannot check them
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("The username is not a valid email.");
        }

        if($password != $confirm) {
            throw new Exception("Passwords mismatch, please try again.");
        }

        if (!preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', $password)) {
            throw new Exception("The password must contain at least one number and one character.");
        }

        $conn = new DatabaseInterface();
        $username = $conn->secure($username);
        $password = md5($password);

        // We have to read and write according to the read, so we start a transaction
        $conn->start_transaction();
        $transaction_started = true;

        $previous_user = get_users($conn->query("SELECT * FROM users WHERE email='$username' FOR UPDATE;"));
        if (count($previous_user) != 0) {
            throw new Exception("This username is already use. Please choose another.");
        }
        // If the username is available, proceed with registration
        $conn->query("INSERT INTO users (email, password) VALUES ('$username','$password');");

        $new_user = get_users($conn->query("SELECT * FROM users WHERE email='$username';"))[0];
        session_fields($new_user);
        $conn->end_transaction();

        redirect('index.php');
    } catch (Exception $e) {
        $register_error = $e->getMessage();
        if ($transaction_started)
            $conn->rollback_transaction();
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
        <?php if ($login_error != ""): ?>
            <div class="message_container error_message_container">
                <p class="message_header">Error</p>
                <p class="message_text"><?php echo $login_error; ?></p>
            </div>
        <?php endif; ?>
        <form id='login' action='auth.php' method='POST' onsubmit="return validate_login();">
            <input class='large_field login_input' type='email' name='username_login' required placeholder='Username'>
            <input class='large_field login_input' type='password' name='password_login' id='password_login' required
                   placeholder='Password'>
            <button type='submit' name='submit_login' class='button large_button'>Login</button>
        </form>
    </div>
    <div id='register_panel'>
        <p class='message_header'>Still not registered? Sign up now!</p>
        <?php if ($register_error != ""): ?>
            <div class="message_container error_message_container">
                <p class="message_header">Error</p>
                <p class="message_text"><?php echo $register_error; ?></p>
            </div>
        <?php endif; ?>
        <form id='register' action='auth.php' method='POST' onsubmit="return validate_register();">
            <input class='large_field' type='email' name='username_register' maxlength=45 required
                   placeholder='Email (will be the username)'>
            <input class='large_field' type='password' name='password_register' id='password_register' maxlength=45 required
                   placeholder='Password'>
            <input class='large_field' type='password' name='password_register_confirm' id='password_register_confirm' maxlength=45 required
                   placeholder='Repeat password'>
            <button type='submit' name='submit_register' class='button large_button'>Register</button>
        </form>
    </div>
</main>
<?php require_once('./php/fragments/footer.php'); ?>
</body>
</html>