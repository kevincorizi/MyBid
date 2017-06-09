<?php
    /* Utility functions for database management */
    $conn = new DatabaseInterface();

    class DatabaseInterface{
        private $dbConnector = null;

        function __construct(){
            $this->dbConnector = new mysqli(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
            $this->dbConnector->set_charset("utf8");
        }

        function query($query){
            $result = $this->dbConnector->query($query);
            return $result;
        }

        function secure($input){
            return $this->dbConnector->real_escape_string($input);
        }

        function get_error(){
            return $this->dbConnector->error;
        }
    }
?>