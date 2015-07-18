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
class Store {
	
	private $obj;
	
	public function __construct($in) {
		
		$this->obj = $in;
	}
	
	public function serialize() {
		
		/* if(isset($this->obj)) {
			
			if(gettype($this->obj) == "object") {
				
				return base64_encode(serialize($this->obj));
			}
			
			return $this->obj; 
		} */
		
		if(isset($this->obj)) {
				
			return base64_encode(serialize($this->obj));
		}
	}
	
	public function desrialize() {
		
		/* if(isset($this->obj)) {
				
			if(gettype($this->obj) == "string") {
		
				return unserialize(base64_decode($this->obj));
			}
				
			return $this->obj;
		} */

		if(isset($this->obj)) {
		
			return unserialize(base64_decode($this->obj));
		}
	}
}
?>