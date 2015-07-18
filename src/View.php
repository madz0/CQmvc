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
include_once 'Ctrl.php';

class View {
	
	protected $VIEW_PATH;
	
	public static function my_autoloader($class) {
	
		$callingClass = Ctrl::$CURRENT_CTRL;
	
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
		else if(file_exists('Res/' . $class . '.php')) {
		
			include 'Res/' . $class . '.php';
		}
		else if(file_exists('Res/'.  $callingClass . '/' . $class . '.php')) {
		
			include 'Res/'.  $callingClass . '/' . $class . '.php';
		}
	}
	
	public function __construct() {

		spl_autoload_register('View::my_autoloader');
	}
	
	protected function getDir() {
		
		$reflector = new ReflectionClass(get_class($this));
		return dirname($reflector->getFileName());
	}
	
	public function render() {
		
		if($this->VIEW_PATH == null) {
			
			try {
				
				include ($this->getDir()."/_".get_class($this).".php");
			}
			catch (Exception $e) {}
		}
		else{
			
			include ($this->VIEW_PATH);
		}
	}
}
?>