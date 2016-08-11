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
class TypeDescriptor {
	
	public static $TYPE_PRIMITIVE = 1;
	
	public static $TYPE_ARRAY = 2;
	
	public static $TYPE_OBJ = 3;
	
	public $type;
	
	public $className;
	
	public $name;
	
	public $default;
	
	public $hasDefault = false;
}

class Runtime {
	
	/**
	 * 
	 * @var array
	 */
	private $params;
	
	public function __construct(array $params, $url) {
		
		if($params == null) {
			
			return;
		}

		$this->params = array();
		
		foreach ($_GET as $index=>$value) {
				
			$this->params[$index] = $value;
		}

		foreach ($_POST as $index=>$value) {

			$this->params[$index] = $value;
		}
		
		include_once 'FileBase.php';
		
		foreach ($_FILES as $index=>$value) {
			
			if(is_array($value['name'])) {
				
				$arrFile = $this->diverse_array($value);
				
				$previousValue = $this->params[$index];
				
				if(isset($previousValue)) {

					if(!is_array($previousValue)) {

						$xx = array();
						$xx[$index] = $previousValue;
						$previousValue = $xx;
					}
				}
				else {
					
					$previousValue = array();
				}
				
				foreach ($arrFile as $index2=>$theFile) {
					
					$fileBase = new FileBase();
						
					$fileBase->name = $theFile['name'];
					$fileBase->tmpName = $theFile['tmp_name'];
					$fileBase->error = $theFile['error'];
					$fileBase->type = $theFile['type'];
					$fileBase->size = $theFile['size'];
					
					$previousValue[$index2] = $fileBase;
				}
				$this->params[$index] = $previousValue;
			}
			else {

				$fileBase = new FileBase();
					
				$fileBase->name = $value['name'];
				$fileBase->tmpName = $value['tmp_name'];
				$fileBase->error = $value['error'];
				$fileBase->type = $value['type'];
				$fileBase->size = $value['size'];

				$this->params[$index] = $fileBase;
			}
		}

		try {
			
			$pCount = count($params);
			
			if( $pCount > 1) {
				
				$contrl = $params[$pCount-2];
				
				$view = $params[$pCount-1];
				
				@include_once 'Ctrl.php';

				$ctrl_path = "App/Control/$contrl.php";
				
				if (file_exists($ctrl_path)) {

					@include_once $ctrl_path;

					chdir('App');
					
					try {
						
						$this->populate($contrl, $view, $params);
						
					} catch (ReflectionException $e) {
						
						chdir('../');
						include_once 'Route.php';
						include_once 'App/Route/DefaultRoute.php';
						
						$route = new DefaultRoute();
						
						$path = $route->getNotFoundPath($url);
						
						if(isset($path) && !is_null($path)) {
						
							$params = preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);//explode('/', $path);
						
							if(count($params) > 1) {
						
								new Runtime($params, $url);
							}
						}
					}
				}
				else {

					include_once 'Route.php';
					include_once 'App/Route/DefaultRoute.php';
					
					$route = new DefaultRoute();
					
					$notFound = false;
					
					$path = $route->getNotFoundPath($url, $notFound);
					
					if(isset($path) && !is_null($path)) {
					
						$params = preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);//explode('/', $path);
					
						if(count($params) > 1) {
					
							if ($notFound) {
								
								header("HTTP/1.0 404 Not Found");
							}
							
							new Runtime($params, $url);
						}
						else {
							
							header("HTTP/1.0 404 Not Found");
						}
					}
					else {
						
						header("HTTP/1.0 404 Not Found");
					}
				}
			}
		}
		catch (Exception $e) {}
	}
	
	private function getParamDescriptor(ReflectionParameter $param) {
		
		$td = new TypeDescriptor();
		
		$td->name = $param->name;
		
		if ($param->isOptional()) {
			
			$td->default = $param->getDefaultValue();
			
			$td->hasDefault = true;
		}
		else {
			
			$td->hasDefault = false;
			unset($td->default);
		}
		
		try {
		
			if($param->isArray()) {
				
				$td->type = TypeDescriptor::$TYPE_ARRAY;
				
				return $td;
			}
			else {
					
				$type = $param->getClass();

				if(is_object($type)) {

					$hint = $param->getClass()->getName();
				}
				
				if(isset($hint)) {
						
					$td->type = TypeDescriptor::$TYPE_OBJ;
					$td->className = $hint;
				}
				else {
						
					$td->type = TypeDescriptor::$TYPE_PRIMITIVE;
				}
				return $td;
			}
		}
		catch (Exception $e) {

			$parts = explode(' ', $e->getMessage(), 3);
			
			$td->type = TypeDescriptor::$TYPE_PRIMITIVE;
			return $td;
		}
	}
	
	private function populate($control, $view, array $path) {
		

		$reflector = new ReflectionClass($control);

		/* @var $method = ReflectionMethod */
		$method = $reflector->getMethod($view);

		$parameters = $method->getParameters();
		
		$params = array();
		
		$routeInfo = array('path'=>$path);
		
		/*This should get called here*/
		$c = $reflector->newInstanceArgs($routeInfo);
		
		/* @var $checkRendered = ReflectionMethod */
		$checkRendered = $reflector->getMethod("isRendered");
		
		$isRendered = $checkRendered->invokeArgs($c, array());
		
		if($isRendered) {
				
			return;
		}
		
		foreach($parameters as $param) {
			
			/* @var $td TypeDescriptor */
			$td = $this->getParamDescriptor($param);
			
			$is_found = false;
			
			if($td->type == TypeDescriptor::$TYPE_OBJ) {

				$nameIndex = $td->name.'->';
				
				$objArr = array();
				
				foreach ($this->params as $var => $value) {
					
					$idx = strpos($var, $nameIndex, 0);
					if( $idx !== false && $idx == 0 ) {

						$objArr[$var] = $value;
						$is_found = true;
					}
					/* else if(strpos($var, $td->name, 0) !== false) {
						$objArr[$var] = $value;
						$is_found = true;
					} */
				}
				
				if($is_found) {

					$root = new $td->className;
					$this->populateComplex($objArr, $root, null, null);
					$params[] = $root;
				}
				else if($td->hasDefault) {
					
					$params[] = $td->default;
				}
				else {
					
					throw new Exception("Wrong object parameters for ".$view);
				}
			}
			else if($td->type == TypeDescriptor::$TYPE_ARRAY) {
				
				$name = $td->name;
				$is_found = false;

				$nameIndex = $td->name.'{';
				
				$objArr = array();
				
				foreach ($this->params as $var => $value) {
				
					$idx = strpos($var, $nameIndex, 0);
					if( $idx !== false && $idx == 0 ) {
				
						$objArr[$var] = $value;
						$is_found = true;
					}
				}
				
				if($is_found) {
					
					$root = array();
					$this->populateComplex($objArr, $root, null, null);
					$params[] = $root;
				}
				else if($td->hasDefault) {
						
					$params[] = $td->default;
				}
				else {
				
					throw new Exception("Wrong array parameters for ".$view);
				}
			}
			
			else if ($td->type == TypeDescriptor::$TYPE_PRIMITIVE) {
				
				if(isset($this->params[$td->name])) {
					
					$params[] = $this->params[$td->name];
				}
				else if($td->hasDefault) {
					
					$params[] = $td->default;
				}
				else {

					throw new Exception("Wrong primtive parameters for ".$view);
				}
			}
		}
			
		try {
			
			$resul = $method->invokeArgs($c,$params);
			
		} catch (Exception $e) {
			
			print $e->getMessage().' - '.$e->getFile().':'.$e->getLine();
		}
		
		if(isset($resul) && !is_null($resul) && $resul != '' && $resul !== '') {
			
			print $resul;
		}
	}
	
	public function populateComplex($params, & $root, $chains, $value) {
		
		if(!is_null($params)) {
				
			foreach ($params as $var => $value) {
				
				$arrIndex = strpos($var, '{', 0);
				$objIndex = strpos($var, '->', 0);

				if( $objIndex !== false && ($arrIndex === false || $arrIndex>$objIndex) ) {
				
					$chains = explode('->', $var, 2);

					$this->populateComplex(null,$root, $chains[1], $value);
				}
				else if( $arrIndex !== false && ($objIndex === false || $arrIndex < $objIndex)) {
				
					$chains = explode('{', $var, 2);
					
					if(isset($chains[1])) {
						
						$chains[1] = '{'.$chains[1];

						$this->populateComplex(null,$root, $chains[1], $value);
					}
				}
				else {
						
					if(is_object($value)) {
						
						if(is_array($root)) {

							$root[$chains[0]] = $value;
						}
						else {

							$root = $value;
						}
					}
					else {
							
						if(is_array($root)) {
						
							$root[$chains[0]] = trim($value);
						}
						else {
						
							$root = trim($value);
						}
					}
				}
			}
		}
		else {

			$arrIndex = strpos($chains, '{', 0);
			$objIndex = strpos($chains, '->', 0);
			
			if( $objIndex !== false && ($arrIndex === false || $arrIndex>$objIndex) ) {
				
				$chains = explode('->', $chains, 2);
				
				if(!isset($chains[0])) {
		
				}
				
				if(!isset($root->{$chains[0]})) {
						
					$root->{$chains[0]} = new stdClass();
				}
				
				return $this->populateComplex(null,$root->{$chains[0]}, $chains[1], $value);
			}
			else if( $arrIndex !== false && ($objIndex === false || $arrIndex < $objIndex)) {
				
				$chains = explode('{', $chains, 2);

				if(isset($chains[0]) && $chains[0] != '') {
					
					$property = $chains[0];

					if(!isset($root->{$property})) {

						$root->{$property} = array();
					}
					
					$chains[1] = '{'.$chains[1];
					
					return $this->populateComplex(null,$root->{$property}, $chains[1], $value);
				}
				else {

					$chains = explode('}', $chains[1], 2);
					
					if(!isset($root[$chains[0]])) {
						
						if(isset($chains[1]) && $chains[1] != '' && $chains[1][0] == '{') {
							
							$root[$chains[0]] = array();
						}
						else {
							
							$root[$chains[0]] = new stdClass();
						}
					}
					
					if(isset($chains[1]) && $chains[1] != '') {

						if($chains[1][0] == '-' && $chains[1][1] == '>') {
								
							$chains[1] = substr($chains[1],2);
						}
					}
					
					return $this->populateComplex(null,$root[$chains[0]], $chains[1], $value);
				}
			}
			else {

				if(is_array($value)) {

					$root->{$chains} = array();
					return $this->populateComplex($value,$root->{$chains}, null, null);
				}
				
				if(is_object($value)) {

					if($chains == '') {
							
						return $root = $value;
					}
					
					if(is_array($root)) {
						
						return $root[$chains] = $value;
					}
					
					return $root->{$chains} = $value;
				}
				else {

					if($chains == '') {
							
						return $root = trim($value);
					}
					
					if(is_array($root)) {

						return $root[$chains] = trim($value);
					}

					return $root->{$chains} = trim($value);
				}
			}
		}
	}
	
	function diverse_array($vector) {
		 
	    $result = array(); 
	    foreach($vector as $key1 => $value1) 
	        foreach($value1 as $key2 => $value2) 
	            $result[$key2][$key1] = $value2; 
	    return $result; 
	}
	
	function endsWith($haystack, $needle) {
		
		$length = strlen($needle);
		
		if ($length == 0) {
			
			return true;
		}
	
		return (substr($haystack, -$length) === $needle);
	}
}