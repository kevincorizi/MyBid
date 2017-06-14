<?php
    require_once DIR_PHP_FUNCTIONS.'db_manager.php';
    require_once DIR_PHP_OBJECTS.'auction.php';
    require_once DIR_PHP_OBJECTS.'notification.php';
    require_once DIR_PHP_OBJECTS.'offer.php';
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

    /* Returns auctions from the product table */
    function get_auctions($result){
        if($result instanceof mysqli_result){
            $result_set = array();
            while ($row = $result->fetch_assoc()) {
                $a = new Auction($row['id'], $row['name'], $row['bid'], $row['bidder']);
                array_push($result_set, $a);
            }
            return $result_set;
        }
        else{
            return array();
        }
    }

    /* Return notifications from the notification table */
    function get_notifications($result){
        if($result instanceof mysqli_result){
            $result_set = array();
            while ($row = $result->fetch_assoc()) {
                $n = new Notification($row['user'], $row['auction'], $row['type'], $row['message']);
                array_push($result_set, $n);
            }
            return $result_set;
        }
        else{
            return array();
        }
    }

    /* Return offers from the offer table */
    function get_offers($result){
        if($result instanceof mysqli_result){
            $result_set = array();
            while ($row = $result->fetch_assoc()) {
                $o = new Offer($row['user'], $row['auction'], $row['value'], $row['timestamp']);
                array_push($result_set, $o);
            }
            return $result_set;
        }
        else{
            return array();
        }
    }

    /* Returns users from User table */
    function get_users($result){
        if($result instanceof mysqli_result){
            $result_set = array();
            while ($row = $result->fetch_assoc()) {
                $u = new User($row['email'], $row['password']);
                array_push($result_set, $u);
            }
            return $result_set;
        }
        else{
            return array();
        }
    }

    /* DEBUG */
    function console_log( $data ){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }
?>