<?php
class Model {
	function __construct() {
		$this->data = array();
		$this->libraries = Libraries::GetInstanse();
		$this->database =  DataBase::GetInstanse();

		$this->database = $this->database->connect();
	}
 
	function __destruct() {
	}
} 