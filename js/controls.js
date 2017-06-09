$(document).ready(function() {

    /* Check if the selected username is already registered */
    $('input[name=username_register]').focusout( function(){
        var username = $('input[name=username_register]').val();
        //use ajax to run the check
        $.post("/php/functions/check_username.php", { username: username },
            function(result){
                //if the result is 1
                if(result == 1){
                    //show that the username is available
                    $('.message_header').html(username + ' is Available');
                }else{
                    //show that the username is NOT available
                    $('.message_header').html(username + ' is not Available');
                }
                console.log(result);
            });
    });
});
