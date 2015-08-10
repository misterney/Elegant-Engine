<?php
class Model {
	function __construct() {
		$this->data = array();
		$this->libraries = Libraries::GetInstanse();
		$this->database =  new Database();

	}
 
	function __destruct() {
	}
} 