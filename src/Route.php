<?php
@session_start();
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
class Route {
	
	private $clientAddress;
	
	public function setClientsAddress($addr) {
		
		$this->clientAddress = $addr;
	}
	
	public function getClientsAddress() {
		
		return $this->clientAddress;
	}
	
	protected function storeSession($key, $value) {
	
		$_SESSION[$key] = $value;
	}
	
	protected function restoreSession($key) {
	
		return @$_SESSION[$key];
	}
	
	protected function storeCookie($key, $value = '', $expire = 0, $path = '/', $domain = '', $secure = '', $httponly = '') {
	
		setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
	}
	
	protected function restoreCookie($key) {
	
		if(!isset($_COOKIE[$key])) {
				
			return null;
		}
	
		return $_COOKIE[$key];
	}
	
	protected function getDefaultPath($path = null) {
		
		return null;
	}
	
	protected function getNotFoundPath($path = null) {
	
		return null;
	}
}
?>