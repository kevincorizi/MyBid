function onload_handler() {
    if (!navigator.cookieEnabled) {
        // I perform the check unless i'm already on nocookie page (to avoid loops)
        if(window.location.toString().indexOf("nocookie.php") == -1)
            window.location = "/nocookie.php";
    } else {
        // If i am in nocookie.php and cookies are enabled, I go back to index.php
        if(window.location.toString().indexOf("nocookie.php") != -1)
            window.location = "/index.php";
    }
}

$(document).ready(function() {
    // Check if the selected username is already registered
    $('input[name=username_register]').focusout( function(){
        var username = $('input[name=username_register]').val();
        if(username == "")
            return;
        console.log(username);
        //use ajax to run the check
        $.post("/php/functions/check_username.php", { username: username },
            function(result){
                //if the result is 1
                switch(result) {
                    case "1":
                        // Remove any error message
                        $('#register_panel .message_container').remove();
                        break;
                    case "0":
                        //show that the username is NOT available
                        $('#register_panel .message_container').remove();
                        $('#register_panel').prepend(
                            "<div class='message_container error_message_container'>" +
                            "<p class='message_header'>Error</p>" +
                            "<p class='message_text'>This username is already use. Please choose another.</p>" +
                            "</div>");
                        break;
                    case "-1":
                        //show that the username must be an email
                        $('#register_panel .message_container').remove();
                        $('#register_panel').prepend(
                            "<div class='message_container error_message_container'>" +
                            "<p class='message_header'>Error</p>" +
                            "<p class='message_text'>The username must be a valid email</p>" +
                            "</div>");
                        break;
                    default:
                        console.log(result);
                        break;
                }
            });
    });

    // Check if the selected username is already registered
    $('input[name=username_login]').focusout( function(){
        var username = $('input[name=username_login]').val();
        if(username == "")
            return;

        //use ajax to run the check
        $.post("/php/functions/check_username.php", { username: username },
            function(result){
                switch(result) {
                    case "0":
                        // Remove any error message
                        $('#login_panel .message_container').remove();
                        break;
                    case "1":
                        //show that the username is NOT available
                        $('#login_panel .message_container').remove();
                        $('#login_panel').prepend(
                            "<div class='message_container error_message_container'>" +
                            "<p class='message_header'>Error</p>" +
                            "<p class='message_text'>This username does not exist? Maybe you want to register?</p>" +
                            "</div>");
                        break;
                    case "-1":
                        //show that the username must be an email
                        $('#login_panel .message_container').remove();
                        $('#login_panel').prepend(
                            "<div class='message_container error_message_container'>" +
                            "<p class='message_header'>Error</p>" +
                            "<p class='message_text'>The username must be a valid email</p>" +
                            "</div>");
                        break;
                    default:
                        console.log(result);
                        break;
                }
            });
    });

    $('input[name=username_login]').focusin( function(){
        $('#login_panel .message_container').remove();
    });

    // Register event handler for thri update popup opening
    $('button#show_thri_popup').click(function() {
        // Show the overlay
        $(".overlay").css("visibility", "visible");
    });

    // Register event handler for thri update popup closing
    $('button#cancel_thri_button').click(function() {
        // Reset the input fields of the form
        $("#new_thri_form form")[0].reset();

        // Hide the form
        $(".overlay").css("visibility", "hidden");
    });

    // Register event handler for thri update action
    $('button#update_thri_button').click(function() {
        var new_thri = Number($("#thri_value").val());
        var auction_id = $("#new_thri_form form")[0].name.split("_")[1];
        if(isNaN(auction_id)) {
            display_result("{\"status\": \"thri_error\", \"value\": \"Invalid auction ID\"}");
            return;
        }
        if(!isNaN(new_thri)) {
            update_thri_async(auction_id, new_thri, display_result);
        } else {
            display_result("{\"status\": \"thri_error\", \"value\": \"Invalid bid value\"}");
        }

        // Reset the input fields of the form
        $("#new_thri_form form")[0].reset();

        // Hide the form
        $(".overlay").css("visibility", "hidden");
    });

    // Register event handler for notification closing and disposal from database
    $('.notification_message_container .message_close').click(function(){
        var notification_id = $(this).parent().attr('id').split("_")[1];
        console.log(notification_id);
        if(isNaN(notification_id)) {
            display_result("{\"status\": \"notification_error\", \"value\": \"Invalid notification ID\"}");
            return;
        }
        delete_notification_async(notification_id, display_result);
        $(this).parent().hide();
        $count = parseInt($('#notification_count').text());
        $('#notification_count').text($count - 1);
    });
});

// Function to asynchronously perform thri update
function update_thri_async(auction_id, new_thri, callback) {
    if (new_thri != null && auction_id != null) {
        return $.post("/php/functions/update_thri.php",
            {auction: auction_id, thri: new_thri },
            function(result){
                callback(result);
            }
        );
    }
}

// Function to asynchronously perform notification disposal
function delete_notification_async(notification_id, callback) {
    if (!isNaN(notification_id) && callback != null) {
        return $.post("/php/functions/dispose_notification.php",
            {notification_id: notification_id},
            function(result){
                callback(result);
            }
        );
    }
}

function display_result(result) {
    console.log(result);
    var response = jQuery.parseJSON(result);
    var response_ok = 0;
    var banner_title = "";
    var banner_text = "";
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
            response_ok = 1;
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
            response_ok = 0;
            banner_title = "Success!";
            break;
        default:
            console.log("Update THR_i: unexpected response from server: " + result);
            response_ok = 0;
            banner_title = "Internal error";
            banner_text = "We are experiencing some technical issues, please try again later!";
            break;
    }
    if(banner_text == "")
        banner_text = response.value;

    window.alert(banner_title + "\n" + banner_text);
}

// Function for registration validation
function validate_register() {
    $('#register_panel .message_container').remove();
    var $pass = $('#password').val();
    var $repeat = $('#password_repeat').val();
    console.log($pass);
    console.log($repeat);
    if($pass === $repeat) {
        if($pass.match(/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/)) {
            return true;
        } else {
            // At least one char and one number
            $('#register_panel').prepend(
                "<div class='message_container error_message_container'>" +
                "<p class='message_header'>Error</p>" +
                "<p class='message_text'>The password must contain at least one character and one number</p>" +
                "</div>");
            return false;
        }
    } else {
        // Password mismatch
        $('#register_panel').prepend(
            "<div class='message_container error_message_container'>" +
            "<p class='message_header'>Error</p>" +
            "<p class='message_text'>The passwords you enter do not match</p>" +
            "</div>");
        return false;
    }
}