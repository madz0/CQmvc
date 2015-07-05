<?php

class Validator {
	
	public $words;
	
	public function __construct($words) {
		
		$this->words = $words;
	}
	
	public function isLengthEnough($length = 8) {
		
		if(strlen($this->words)>= $length) {
			
			return true;
		}
		
		return false;
	}

	public function isUnpredictable() {
		
		/*
		 *  From http://www.rexegg.com/regex-lookarounds.html
		 *  
		 *  1. The password must have between six word characters \w
		 *	2. It must include at least one lowercase character [a-z]
		 *	3. It must include at least two uppercase characters [A-Z]
		 *	4. It must include at least one digit \d
		 */
		
		if (preg_match("/^(?=\w{6,}\z)(?=[^a-z]*[a-z])(?=(?:[^A-Z]*[A-Z]){2})(?=\D*\d)/", $this->words)) {
		
			return true;
		}

		return false;
	}
	
	public function isValidEmail() {
		
		if (!filter_var($this->words, FILTER_VALIDATE_EMAIL)) {
			
			return false;
		}
		
		return true;
	}
	
	public function isValidName() {
		
		if (!preg_match("/^[a-zA-Z ]*$/",$this->words)) {
			
			return false;
		}
		
		return true;
	}
	
	public function isValidUrl() {
		
		if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$this->words)) {
			
			return false;
		}
		
		return true;
	}
}
?>