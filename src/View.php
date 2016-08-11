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
	
	public function manageAsset($path, array $headers = null) {
		
		$cache = new APCache(60);
		
		$cfg = $cache->get($path);
		
		$noNeedToExpire = false;
		
		$noNeedToCacheConttrolMaxAge = false;
		
		$shouldUpdate = true;
		
		$is_header_null = is_null($headers);
		
		if(!$is_header_null) {

			if(!isset($headers['gzip'])) {
				
				$headers['gzip'] = false;
			}
		}
		
		if(!is_null($cfg)) {
			
			if($is_header_null) {
				
				$cache->delete($path);
				$shouldUpdate = true;
			}
			else {
				
				if(@$headers['expires'] == @$cfg['expires_actual']) {
				
					$noNeedToExpire = true;
				}
					
				if (@$headers['cache_control_max_age'] == @$cfg['cache_control_max_age_actual']) {
				
					$noNeedToCacheConttrolMaxAge = true;
				}
					
				if(@$headers['cache_control'] != @$cfg['cache_control']) {
				
					$shouldUpdate = true;
				}
				else if($cfg['gzip'] != $headers['gzip']) {
				
					$shouldUpdate = true;
				}
				else if(@$headers['access_control_allow_origin'] != @$cfg['access_control_allow_origin']) {
				
					$shouldUpdate = true;
				}	
			}
		}
		
		if(!$is_header_null && !$noNeedToExpire && isset($headers['expires'])) {
			
			$maxAge = $headers['expires'];
			
			$vld = new Validator($maxAge);
			
			if(is_numeric($maxAge)) {
				
				$headers['expires'] = $maxAge;
				$headers['expires_actual'] = $maxAge;
			}
			else if($vld->endsWith('d') || $vld->endsWith('D') || $vld->endsWith('m') 
					|| $vld->endsWith('M') || $vld->endsWith('y') || $vld->endsWith('Y')) {
				
				$date = new DateTime();
				$date->add(new DateInterval('P'.$maxAge));
				
				$maxAgeTmp = $maxAge;
				
				$maxAge = $date->format('D, d M Y H:i:s \G\M\T');
				
				$headers['expires'] = $maxAge;
				$headers['expires_actual'] = $maxAgeTmp;
			}
			else {
				
				unset($headers['expires']);
			}

			$shouldUpdate = true;
		}

		if(!$is_header_null && !$noNeedToCacheConttrolMaxAge && isset($headers['cache_control_max_age'])) {
			
			
			$maxAge = $headers['cache_control_max_age'];
			
			$headers['cache_control_max_age_actual'] = $maxAge;
			
			$dv = new DateInterval('P'.$maxAge);
			
			$headers['cache_control_max_age'] = $dv->s + ($dv->i * 60) + ($dv->h * 3600) + ($dv->d * 3600 * 24) +
			 ($dv->m * 30 * 24 * 3600) + ($dv->y * 365 * 24 * 3600);
			
			$shouldUpdate = true;
		}
		
		if ($is_header_null) {
			
			$headers = array();
			$headers['gzip'] = false;
		}
		
		if($shouldUpdate) {
			
			$path_parts = pathinfo($path);
			
			switch ($path_parts['extension']) {
				
				case 'jpeg':
				case 'jpg':
				case 'JPEG':
				case 'JPG':

					$headers['content_type'] = 'image/jpeg';
					$headers['extension'] = 'jpeg';
					break;
				case 'png':
				case 'PNG':
				
					$headers['content_type'] = 'image/png';
					$headers['extension'] = 'png';
					break;
				case 'gif':
				case 'GIF':
					
					$headers['content_type'] = 'image/giff';
					$headers['extension'] = 'gif';
					break;
				case 'ico':
				case 'ICO':
					
					$headers['content_type'] = 'image/x-icon';
					$headers['extension'] = 'ico';
					break;
				case 'css':
				case 'CSS':

					$headers['content_type'] = 'text/css';
					$headers['extension'] = 'css';
					break;
					
				case 'js':
				case 'JS':
						
					$headers['content_type'] = 'application/javascript';
					$headers['extension'] = 'js';
					break;
				case 'ttf':
				case 'TTF':
						
					$headers['content_type'] = 'application/font-ttf';
					$headers['extension'] = 'ttf';
					break;
				case 'otf':
				case 'OTF':
						
					$headers['content_type'] = 'application/font-otf';
					$headers['extension'] = 'otf';
					break;
				case 'eot':
				case 'EOT':
					
					$headers['content_type'] = 'application/font-eot';
					$headers['extension'] = 'eot';
					break;		
				case 'woff':
				case 'WOFF':
						
					$headers['content_type'] = 'application/font-woff';
					$headers['extension'] = 'woff';
					break;	
				case 'woff2':
				case 'WOFF2':
						
					$headers['content_type'] = 'application/font-woff2';
					$headers['extension'] = 'woff2';
					break;
				default:

					$headers['content_type'] = '';
					$headers['extension'] = $path_parts['extension'];
					break;
			}

			try {

				$cache->update('/App'.$path, $headers);
			} catch (Exception $e) {
				
				die($e->getMessage());
			}
		}
		
		return "/Managed".$path;
	}
}
?>