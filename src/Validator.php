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