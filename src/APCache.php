<?php
/*
 * CQMvc A PHP MVC Framework
 *
 * Copyright 2015 by Mohamad Zeinali mohamad.basu@gmail.com
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

include_once 'ICache.php';
class APCache implements ICache {
	
	private $ttl = 600; // Time To Live
	
	private $bEnabled = false; // APC enabled?
	
	// constructor
	function __construct($ttl) {
		
		$this->bEnabled = extension_loaded('apc');
		
		$this->ttl = $ttl;
	}
	
	public function get($key) {
		
		if($this->bEnabled) {
			
			$bRes = false;
			$vData = apc_fetch($key, $bRes);
			return ($bRes) ? $vData :null;
		}
		
		throw new Exception("No underlying caching found. I need APCU or APC");
	}
	
	public function update($key, $data) {
		
		if(!$this->bEnabled) {
			
			throw new Exception("No underlying caching found. I need APCU or APC");
		}
		
		return apc_store($key, $data, $this->ttl);
	}
	
	public function delete($key) {
		
		if(!$this->bEnabled)  {
			
			throw new Exception("No underlying caching found. I need APCU or APC");
		}
		
		return (apc_exists($key)) ? apc_delete($key) : true;
	}
}