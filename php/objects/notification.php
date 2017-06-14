<?php
    class Notification
    {
        public $id;
        public $user;
        public $auction;
        public $type;
        public $message;

        function __construct($id, $user, $auction, $type, $message)
        {
            $this->id = $id;
            $this->user = $user;
            $this->auction = $auction;
            $this->type = $type;
            $this->message = $message;
        }
    }
?>