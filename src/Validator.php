<?php
/*
 * CQMvc A PHP MVC Framework
 *
 * Copyright 2015 by Mohamad Zeinali mohammad.basu@gmail.com
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
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
	
	public function isLengthBetween($length1 = 0, $length2=0) {
	
	    $length = strlen($this->words);
	    
	    if($length<= $length2 && $length >= $length1) {
	        	
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
		
		if (!preg_match("/^[a-zA-Z ]{3,}$/",$this->words)) {
			
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
	
	public function isValidIpv4() {
	
		if (!preg_match("/\b(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/",$this->words)) {
				
			return false;
		}
	
		return true;
	}
	
	public function isValidIpv6() {
	
		if (!preg_match("/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$|^(([a-zA-Z]|[a-zA-Z][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$|^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/",$this->words)) {
	
			return false;
		}
	
		return true;
	}
	
	public function isValidDnsLable() {
		
		$w = $this->words;
		
		if($w == '*') {
				
			return true;
		}
		
		if(strrpos($this->words,"*.") === 0) {
			
			$w = substr($w, 2);
		}
		
		if($w === '') {
			
			return true;
		}
	
		if (!preg_match("/^[a-z0-9][a-z0-9\-\.]*?(?<=[a-z0-9])$/i",$w)) {
	
			return false;
		}
	
		return true;
	}
	
	public function isValidDnsLableNoWildCard() {
	
		$w = $this->words;
	
		if($w === '') {
				
			return true;
		}
	
		if (!preg_match("/^[a-z0-9][a-z0-9\-\.]*?(?<=[a-z0-9])$/i",$w)) {
	
			return false;
		}
	
		return true;
	}
	
	public function isValidDnsHostname() {
		
		$w = $this->words;
		
		if(strrpos($this->words,"*.") === 0) {
				
			$w = substr($w, 2);
		}
		
		if($this->endsWith(".")) {
			
			$w = substr($w, 0, strlen($w) -1);
		}
		
		if($w === '') {
				
			return false;
		}
	
		if (!preg_match("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/",$w)) {
	
			return false;
		}
	
		return true;
	}
	
	public function isValidDnsHostnameNoWildCard() {
	
		$w = $this->words;
	
		if($this->endsWith(".")) {
				
			$w = substr($w, 0, strlen($w) -1);
		}
	
		if($w === '') {
	
			return false;
		}
	
		if (!preg_match("/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/",$w)) {
	
			return false;
		}
	
		return true;
	}
	
	function startsWith($needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($this->words, $needle, -strlen($this->words)) !== FALSE;
	}
	
	function endsWith($needle) {
		// search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($this->words) - strlen($needle)) >= 0 && strpos($this->words, $needle, $temp) !== FALSE);
	}
}
?>