<?php
    class Auction
    {
        public $id;
        public $name;
        public $bid;
        public $bidder;

        function __construct($id, $name, $bid, $bidder)
        {
            $this->id = $id;
            $this->name = $name;
            $this->bid = $bid;
            $this->bidder = $bidder;
        }
    }
?>