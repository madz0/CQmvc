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
require_once 'View.php';

include_once 'Store.php';
include_once 'UUID.php';
include_once 'SimpleCaptcha.php';
include_once 'Validator.php';
include_once 'MvcUrl.php';
include_once 'Route.php';
include_once 'Crypto.php';
include_once 'class.phpmailer.php';
include_once 'class.smtp.php';
include_once 'APCache.php';
require_once 'phpexcel/PHPExcel.php';

class Ctrl {

	protected static function getClassName() {
		
		return get_called_class();
	}
	
	public static $CURRENT_CTRL;
	
	protected $doc_root;
	
	protected $request_path;
	
	protected $request_qs;
	
	private $out = "";
	
	private $rendered = false;
	
	public static function my_autoloader($class) {
	
		$callingClass = get_called_class();
		
		if (false !== ($lastNsPos = strripos($class, '\\'))) {
				
			$namespace = substr($class, 0, $lastNsPos);
			$className = substr($class, $lastNsPos + 1);
			$fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
				
			$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

			if (file_exists($fileName)) {

				include_once $fileName;
				return;
			}
		}
		
		if (file_exists('View/' . $class . '.php')) {
	
			include 'View/' . $class . '.php';
		}
		else if(file_exists('View/'.  $callingClass . '/' . $class . '.php')) {
	
			include 'View/'.  $callingClass . '/' . $class . '.php';
		}
		else if(file_exists('Model/' . $class . '.php')) {
	
			include 'Model/' . $class . '.php';
		}
		else if(file_exists('Model/'.  $callingClass . '/' . $class . '.php')) {
	
			include 'Model/'.  $callingClass . '/' . $class . '.php';
		}
		else if(file_exists('ViewModel/'. $class . '.php')) {
	
			include 'ViewModel/'. $class . '.php';
		}
		else if(file_exists('ViewModel/'. $callingClass . '/' . $class . '.php')) {
	
			include 'ViewModel/'.  $callingClass . '/' . $class . '.php';
		}
		else if(file_exists('Helper/'. $class . '.php')) {
	
			include 'Helper/'. $class . '.php';
		}
		else if(file_exists('Helper/'.  $callingClass . '/' . $class . '.php')) {
	
			include 'Helper/'.  $callingClass . '/' . $class . '.php';
		}
		else if(file_exists('Res/' . '/' . $class . '.php')) {
		
			include 'Res/' . '/' . $class . '.php';
		}
		else if(file_exists('Res/'.  $callingClass . '/' . $class . '.php')) {
		
			include 'Res/'.  $callingClass . '/' . $class . '.php';
		}
	}
	
	public function __construct() {
		
		$this->doc_root = $_SERVER['DOCUMENT_ROOT'];
		//$this->request_path = $_SERVER['PATH_INFO'];
		$this->request_qs = $_SERVER['QUERY_STRING'];
		
		Ctrl::$CURRENT_CTRL = self::getClassName();
		
		spl_autoload_register(get_class($this).'::my_autoloader');
	}
	
	protected function startSession($sessionDomain = null, $sessionPath = '/', $sessionLifeTime = 0) {
		
		session_set_cookie_params($sessionLifeTime, $sessionPath, $sessionDomain);
		@session_start();
	}
	
	protected function view($v) {
		
		$this->rendered = true;
		
		ob_start();
		$v->render();
		$out = ob_get_contents();
		@ob_end_clean();
		$this->out .= $out;
		return $out;
	}
	
	protected function render() {
		
		$this->rendered = true;
			
		ob_start();
		
		if(func_num_args()==0) {
			
			print $this->out;
		}
		else {
				
			func_get_arg(0)->render();	
		} 
			
		ob_end_flush();
	}
	
	protected function renderGz() {
	
		$this->rendered = true;
	
		if(!ob_start("ob_gzhandler")) ob_start();
	
		if(func_num_args()==0) {
				
			print $this->out;
		}
		else {
	
			func_get_arg(0)->render();
		}
			
		ob_end_flush();
	}
	
	protected function getMvcUrl() {
		
		$args_num = func_num_args();
		
		if($args_num == 0) {
		
			throw new Exception();
		}
		
		if($args_num == 1 && is_array(func_get_arg(0))) {
				
			$arguments = func_get_arg(0);
			$args_num = count($arguments);
		}
		else {
				
			$arguments = func_get_args();
		}
		
		if($args_num == 1) {
		
			$inArg = $arguments[0];
		
			if(strpos($inArg, '/') !== false) {
		
				return new MvcUrl($inArg);
			}
			else {
		
				$controler = get_class($this);
		
				$view = $inArg;
		
				return new MvcUrl($view, $controler);
			}
		}
		elseif ($args_num == 2) {
		
			$arg1 = $arguments[1];
		
			if(is_array($arg1)) {
		
				$controler = get_class($this);
					
				$view = $arguments[0];
		
				$params_array = $arg1;
					
				return new MvcUrl($view, $controler, '', $params_array);
			}
			else {
		
				$controler = $arg1;
		
				$view = $arguments[0];
		
				return new MvcUrl($view, $controler);
			}
		}
		elseif ($args_num == 3) {
		
			$view = $arguments[0];
		
			$controler = $arguments[1];
			
			$arg3 = $arguments[2];
		
			if(is_array($arg3)) {
				
				$params_array = $arg3;
				
				return new MvcUrl($view, $controler, '', $params_array);
			}
			else {
				
				$area = $arg3;
				
				return new MvcUrl($view, $controler, $area);
			}
		}
		elseif ($args_num == 4) {
		
			$view = $arguments[0];
		
			$controler = $arguments[1];
			
			$area = $arguments[2];
			
			$params_array = $arguments[3];
		
			return new MvcUrl($view, $controler, $area, $params_array);
		}
	}
	
	protected function redirect() {
		
		$this->rendered = true;
		
		$args_num = func_num_args();
		
		if($args_num == 0) {
			
			throw new Exception();
		}
		
		$url = $this->getMvcUrl(func_get_args());
		
		ob_start();

		$u = $url->getURL();
		
		header("Location: $u");
		
		ob_end_flush();
	}
	
	protected function storeSession($key, $value) {
		
		$_SESSION[$key] = $value;
	}
	
	protected function restoreSession($key) {

		return @$_SESSION[$key];
	}
	
	protected function destroySession() {
	
		session_unset();
		session_destroy();
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

	protected function asXml($content = '') {
		
		if($content == '' || is_null($content)) {
			
			return;
		}
		
		Header('Content-type: text/xml');
		print $content;
	}
	
	public function isRendered() {
		
		return $this->rendered;
	}
	
	public function getRequestIPAddr() {
		
		return $_SERVER['REMOTE_ADDR'];
	}
	
	public function getRequestUserAgent() {
	
		return $_SERVER['HTTP_USER_AGENT'];
	}
}

?>