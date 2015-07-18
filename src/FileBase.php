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
class FileBase {
	
	public $name;
	
	public $type;
	
	public $tmpName;
	
	public $error;
	
	public $size;
	
	private $overwriteExiste = true;
	
	public function save($path, $name = null) {
		
		if($this->size<=0) {
			
			return -1;
		}
		
		if(!validateBeforeSave()) {
			
			return -2;
		}
		
		if(is_null($path) || $path == '') {
			
			return -3;
		}
		
		if(is_null($name) || !isset($name) || $name == '') {
			
			if($path[strlen($path)-1] == '/') {

				$path = "$path$this->name";
			}
		}
		
		if(!$this->overwriteExiste) {
			
			if (file_exists($target_file)) {
				
				return -4;
			}
		}
		
		return move_uploaded_file( $_FILES['userFile']['tmp_name'], $path) === true? 1:0;
	}
	
	public function getBytes() {
		
		if($this->size<=0) {
			
			return -1;
		}
		
		$handle = fopen($this->tmpName, "r");
		$contents = fread($handle, $this->size);
		fclose($handle);
		
		return $contents;
	}
	
	public function getExtention() {
		
		$info = pathinfo($this->name);
		return $info['extension'];
	}
	
	public function isSafeImage() {
		
		return getimagesize($this->tmpName) !== false;
	}
	
	protected function validateBeforeSave() {
		
		return true;
	}
	
	public function overwriteIfExists($override) {
	
		$this->overwriteExiste = $override;
	}
}