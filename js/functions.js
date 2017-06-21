/* Function called when the page is loaded */
function onload_handler() {
    // Check for cookies
    if (!navigator.cookieEnabled) {
        // I perform the check unless i'm already on nocookie page (to avoid loops)
        if (window.location.toString().indexOf("nocookie.php") == -1)
            window.location = "./nocookie.php";
    } else {
        // If i am in nocookie.php and cookies are enabled, I go back to index.php
        if (window.location.toString().indexOf("nocookie.php") != -1)
            window.location = "./index.php";
    }

    // Adapt the height of the sidebar to the actual height of the content
    // This is due to a compatibility issue in IE11, which treats min-height in a different way
    $('aside').outerHeight($('main').height());
    $('section').outerHeight($('main').height());
}

/* When the document is ready register these functions */
$(document).ready(function () {

    // Remove all errors from login when register is focused, to clean the view
    $('#register_panel').focusin(function () {
        $('#login_panel .message_container').remove();
    });

    // Remove all errors from register when login is focused, to clean the view
    $('#login_panel').focusin(function () {
        $('#register_panel .message_container').remove();
    });

    // Check if the selected username is already used
    $('input[name=username_register]').focusout(function () {
        var username = $('input[name=username_register]').val();
        if (username == "")
            return;

        // Asynchronously perform the check and call display_errors_register with the result
        check_username(username, display_errors_register);
    });

    // Check if the selected username is already registered and can log in
    $('input[name=username_login]').focusout(function () {
        var username = $('input[name=username_login]').val();
        if (username == "")
            return;

        // Asynchronously perform the check and call display_errors_login with the result
        check_username(username, display_errors_login);
    });

    // Remove any error message from the input when i focus in
    $('input[name=username_login]').focusin(function () {
        $('#login_panel .message_container').remove();
    });

    // Remove any error message from the input when i focus in
    $('input[name=username_register]').focusin(function () {
        $('#register_panel .message_container').remove();
    });

    // Register event handler for thri update popup opening
    $('button#show_thri_popup').click(function () {
        // Show the overlay
        $("#new_thri_form").parent().css("visibility", "visible");
    });

    // Register event handler for thri update popup closing
    $('button#cancel_thri_button').click(function () {
        // Reset the input fields of the form
        $("#new_thri_form form")[0].reset();

        // Hide the form
        $("#new_thri_form").parent().css("visibility", "hidden");
    });

    // Register event handler for thri update action
    $('button#update_thri_button').click(function () {
        var new_thri = $("#thri_value").val();
        var auction_id = $(".overlay form")[0].name.split("_")[1];
        if (isNaN(auction_id)) {
            display_thri_update_result("{\"status\": \"thri_error\", \"value\": \"Invalid auction ID\"}");
            return;
        }
        if (/*new_thri.match(/^(\d{1,5}[\.,\,]\d{2})$/)*/true) {
            update_thri_async(auction_id, new_thri, display_thri_update_result);
        } else {
            display_thri_update_result("{\"status\": \"thri_error\", \"value\": \"Invalid bid value\"}");
        }

        // Reset the input fields of the form
        $("#new_thri_form form")[0].reset();

        // Hide the form
        $("#new_thri_form").parent().css("visibility", "hidden");
    });

    // Register event handler for notification closing and disposal from database
    $('.notification_message_container .message_close').click(function () {
        var notification_id = $(this).parent().attr('id').split("_")[1];
        console.log(notification_id);
        if (isNaN(notification_id)) {
            display_thri_update_result("{\"status\": \"notification_error\", \"value\": \"Invalid notification ID\"}");
            return;
        }
        delete_notification_async(notification_id, display_thri_update_result);
        $(this).parent().hide();
        $count = parseInt($('#notification_count').text());
        $('#notification_count').text($count - 1);
    });
});

// Dedicated function to display errors in the login part after the async call
function display_errors_login(code) {
    var success = false;
    switch (code) {
        case "1":
            // Username exists
            $('#login_panel .message_container').remove();
            success = true;
            break;
        case "0":
            // Username does not exist
            $('#login_panel .message_container').remove();
            $('#login').prepend(
                "<div class='message_container error_message_container'>" +
                "<p class='message_header'>Error</p>" +
                "<p class='message_text'>This username does not exist? Maybe you want to register?</p>" +
                "</div>");
            break;
        case "-1":
            // Invalid email
            $('#login_panel .message_container').remove();
            $('#login').prepend(
                "<div class='message_container error_message_container'>" +
                "<p class='message_header'>Error</p>" +
                "<p class='message_text'>The username must be a valid email</p>" +
                "</div>");
            break;
        case "-2":
            // Database exception
            $('#login_panel .message_container').remove();
            $('#login').prepend(
                "<div class='message_container error_message_container'>" +
                "<p class='message_header'>Error</p>" +
                "<p class='message_text'>There was a problem with your request. Please try again later</p>" +
                "</div>");
            break;
        default:
            console.log(result);
            break;
    }
    return success;
}

// Dedicated function to display errors in the register part after the async call
function display_errors_register(code) {
    var success = false;
    switch (code) {
        case "0":
            // Username available
            $('#register_panel .message_container').remove();
            success = true;
            break;
        case "1":
            // Username not available
            $('#register_panel .message_container').remove();
            $('#register').prepend(
                "<div class='message_container error_message_container'>" +
                "<p class='message_header'>Error</p>" +
                "<p class='message_text'>This username is already use. Please choose another.</p>" +
                "</div>");
            break;
        case "-1":
            // Invalid email
            $('#register_panel .message_container').remove();
            $('#register').prepend(
                "<div class='message_container error_message_container'>" +
                "<p class='message_header'>Error</p>" +
                "<p class='message_text'>The username must be a valid email</p>" +
                "</div>");
            break;
        case "-2":
            // Database exception
            $('#register_panel .message_container').remove();
            $('#register').prepend(
                "<div class='message_container error_message_container'>" +
                "<p class='message_header'>Error</p>" +
                "<p class='message_text'>There was a problem with your request. Please try again later</p>" +
                "</div>");
            break;
        default:
            console.log(result);
            break;
    }
    return success;
}

// Check how many users exist with the username passed as parameter
// Invoke callback on the result
function check_username(username, callback) {
    $.post("./php/functions/check_username.php",
        {username: username},
        function (result) {
            callback(result);
        });
}

// Function to asynchronously perform thri update
function update_thri_async(auction_id, new_thri, callback) {
    if (new_thri != null && auction_id != null) {
        return $.post("./php/functions/update_thri.php",
            {auction: auction_id, thri: new_thri},
            function (result) {
                callback(result);
            }
        );
    }
}

// Function to asynchronously perform notification disposal
function delete_notification_async(notification_id, callback) {
    if (!isNaN(notification_id) && callback != null) {
        return $.post("./php/functions/dispose_notification.php",
            {notification_id: notification_id},
            function (result) {
                callback(result);
            }
        );
    }
}

// Display messages according to the result of the async thri update
function display_thri_update_result(result) {
    var response;
    try {
        console.log("Result :" + result);
        response = jQuery.parseJSON(result);
    } catch (e) {
        // We always receive a proper JSON string unless the user authentication timer expires right before the request is sent
        window.location = "./auth.php";
    }
    var response_ok = 0;
    var banner_title = "";
    var banner_text = "";
    var banner_color = "";
    switch (response.status) {
        // Cases from update_thri script
        case 'thri_error':
            response_ok = 0;
            banner_title = "Bid error";
            break;
        case 'smaller_than_bid':
            response_ok = 0;
            banner_title = "Error";
            break;
        case 'bid_exceeded':
            $("#current_thri_value").text(response.value);
            $("#current_thri_date").text(response.time);
            response_ok = 0;
            banner_title = "Success!";
            break;
        case 'highest_bidder':
            $("#current_thri_value").text(response.value);
            $("#current_thri_date").text(response.time);
            response_ok = 1;
            banner_title = "Success!";
            break;
        // Cases from dispose_notification
        case 'notification_error':
            response_ok = 0;
            banner_title = "Error";
            break;
        case 'notification_deleted':
            response_ok = 1;
            banner_title = "Success!";
            break;
        default:
            console.log("Update THR_i: unexpected response from server: " + result);
            response_ok = 0;
            banner_title = "Internal error";
            banner_text = "We are experiencing some technical issues, please try again later!";
            break;
    }
    if (response_ok == 0)
        banner_color = "red";
    else
        banner_color = "green";
    if (banner_text == "")
        banner_text = response.value;
    $('main').append(
        "<div class='overlay' id='show_outcome_popup' style='visibility: visible;' >" +
        "<div class='overlay_content' style='background-color: " + banner_color + "'>" +
        "<p class='overlay_outcome_message'>" + banner_text + "</p>" +
        "</div>" +
        "</div>"
    );
    setTimeout(function () {
        $('#show_outcome_popup').remove();
    }, 3000);
}

// Function for login validation before sending to server
function validate_login() {
    $('#login_panel .message_container').remove();
    var $username = $('input[name=username_login]').val();
    var $pass = $('#password_login').val();

    if ($pass.match(/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/)) {
        return true;
    } else {
        // At least one char and one number
        $('#login').prepend(
            "<div class='message_container error_message_container'>" +
            "<p class='message_header'>Error</p>" +
            "<p class='message_text'>The password must contain at least one character and one number</p>" +
            "</div>");
        return false;
    }
}

// Function for registration validation
function validate_register() {
    $('#register_panel .message_container').remove();
    var $username = $('input[name=username_register]').val();
    var $pass = $('#password_register').val();
    var $repeat = $('#password_register_confirm').val();

    if ($pass === $repeat) {
        if ($pass.match(/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/)) {
            return true;
        } else {
            // At least one char and one number
            $('#register').prepend(
                "<div class='message_container error_message_container'>" +
                "<p class='message_header'>Error</p>" +
                "<p class='message_text'>The password must contain at least one character and one number</p>" +
                "</div>");
            return false;
        }
    } else {
        // Password mismatch
        $('#register').prepend(
            "<div class='message_container error_message_container'>" +
            "<p class='message_header'>Error</p>" +
            "<p class='message_text'>The passwords you enter do not match</p>" +
            "</div>");
        return false;
    }
}