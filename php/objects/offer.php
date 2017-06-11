<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 11/06/2017
 * Time: 09:26
 */
class Offer
{
    public $user;
    public $auction;
    public $value;
    public $timestamp;

    function __construct($user, $auction, $value, $timestamp)
    {
        $this->user = $user;
        $this->auction = $auction;
        $this->value = $value;
        $this->timestamp = $timestamp;
    }
}