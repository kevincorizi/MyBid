<?php
    /* Utility functions for database management */
    $conn = new DatabaseInterface();

    class DatabaseInterface{
        private $dbConnector = null;

        function __construct(){
            $this->dbConnector = new mysqli(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
            if (mysqli_connect_errno()) {
				die("Error when connecting to the db: ".mysqli_connect_errno()."-".mysqli_connect_error());
			}
			$this->dbConnector->set_charset("utf8");
        }

        function query($query){
			$result = $this->dbConnector->query($query);
            if(!$result) {
				printf("Error: %s\n", $mysqli->sqlstate);
			}
            return $result;
        }
		
		function start_transaction() {
			$this->dbConnector->autocommit(false);
			return $this->dbConnector->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		}
		
		function end_transaction() {
			return $this->dbConnector->commit();
		}

        function secure($input){
			$input = strip_tags($input);
			$input = htmlentities($input);
			$input = stripcslashes($input);
            return $this->dbConnector->real_escape_string($input);
        }

        function get_error(){
            return $this->dbConnector->error;
        }
    }
?>