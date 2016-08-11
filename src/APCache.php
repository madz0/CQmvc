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

if (!function_exists("apcu_fetch")) {

	function apcu_fetch() {}
	function apcu_store() {}
	function apcu_exists() {}
	function apcu_delete() {}
}

if (!function_exists("apc_fetch")) {

	function apc_fetch() {}
	function apc_store() {}
	function apc_exists() {}
	function apc_delete() {}
}

class APCache implements ICache {
	
	private $ttl = 600; // Time To Live
	
	private $bEnabled = false; // APC enabled?
	
	private $cacheType = 0;
	
	// constructor
	function __construct($ttl) {

		$this->bEnabled = extension_loaded('apc') || extension_loaded('apcu');
		
		if(extension_loaded('apc')) {
			
			$this->cacheType = 1;
		}
		else if(extension_loaded('apcu')) {
			
			$this->cacheType = 2;
		}
		
		$this->ttl = $ttl;
	}
	
	public function get($key) {
		
		if($this->bEnabled) {
			
			if($this->cacheType == 2) {
				
				$bRes = false;
				$vData = apcu_fetch($key, $bRes);
				return ($bRes) ? $vData :null;
			}
			else if($this->cacheType == 1) {
				
				$bRes = false;
				$vData = apc_fetch($key, $bRes);
				return ($bRes) ? $vData :null;
			}
			
			throw new Exception("Cache type is not implemented");
		}
		
		throw new Exception("No underlying caching found. I need APCU or APC");
	}
	
	public function update($key, $data) {
		
		if(!$this->bEnabled) {
			
			throw new Exception("No underlying caching found. I need APCU or APC");
		}
		
		if($this->cacheType == 2) {

			return apcu_store($key, $data, $this->ttl);
		}
		else if($this->cacheType == 1) {

			return apc_store($key, $data, $this->ttl);
		}
		
		throw new Exception("Cache type is not implemented");
	}
	
	public function delete($key) {
		
		if(!$this->bEnabled)  {
			
			throw new Exception("No underlying caching found. I need APCU or APC");
		}
		
		if($this->cacheType == 2) {
		
			return (apcu_exists($key)) ? apcu_delete($key) : true;
		}
		else if($this->cacheType == 1) {
		
			return (apcu_exists($key)) ? apc_delete($key) : true;
		}
		
		throw new Exception("Cache type is not implemented");
	}
}