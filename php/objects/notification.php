<?php
    class Notification
    {
        public $user;
        public $auction;
        public $type;
        public $message;

        function __construct($user, $auction, $type, $message)
        {
            $this->user = $user;
            $this->auction = $auction;
            $this->type = $type;
            $this->message = $message;
        }
    }
?>