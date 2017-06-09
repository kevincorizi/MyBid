<?php
    require_once DIR_PHP_FUNCTIONS.'db_manager.php';
    require_once DIR_PHP_OBJECTS.'user.php';

    /* Common utility functions */
    /* Redirection with cache emptying */
    function redirect($url){
        header('Location:'.$url, true, 303);
    }

    /*
        Converts a SQL date in one of the following formats:
        SHORT: dd/mm/aaaa hh:mm
        LONG: dd month aaaa hh:mm
    */
    function toDate($string, $format){
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $date = explode(' ', $string)[0];
        $time = substr(explode(' ', $string)[1], 0, 5);

        $day = explode('-', $date)[2];
        $monthS = explode('-', $date)[1];
        $monthL = $months[intval($monthS) - 1];
        $year = explode('-', $date)[0];

        if($format == 'short')
            return $day.'/'.$monthS.'/'.$year.' '.$time;
        else
            return $day.' '.$monthL.' '.$year.' '.$time;
    }

    /* Returns users from User table */
    function get_users($query){
        global $conn;
        $result = $conn->query($query);
        if($result instanceof mysqli_result){
            $result_set = array();
            while ($row = $result->fetch_assoc()) {
                $u = new User($row['email'], $row['password']);
                array_push($result_set, $u);
            }
            return $result_set;
        }
        else{
            return $result;
        }
    }

    /* DEBUG */
    function console_log( $data ){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }
?>