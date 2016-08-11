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

include_once 'APCache.php';
include_once 'Runtime.php';
include_once 'Validator.php';

$path = $_SERVER['PATH_INFO'];
$url = $path;

$validator = new Validator($path);

if($validator->startsWith("/Managed")) {
	
	$path = substr($path, 8, strlen($path));

	$cache = new APCache(60);
	
	$cfg = $cache->get('/App'.$path);
	
	$file = 'App'.$path;
	
	if(!is_null($cfg)) {
		
		if(!file_exists($file)) {
		
			header("HTTP/1.0 404 Not Found");
			exit;
		}
		
		/* using http://stackoverflow.com/questions/10847157/handling-if-modified-since-header-in-a-php-script */
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
				strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($file)) {
					
			header('HTTP/1.0 304 Not Modified');
			exit;
		}
	
		if($cfg['gzip']) {
	
			if(!ob_start("ob_gzhandler")) ob_start();
		}
		else {
	
			ob_start();
		}
	
		header("Vary: Accept-Encoding");
	
		if(isset($cfg['cache_control'])) {
	
			if(isset($cfg['cache_control_max_age'])) {
	
				header(sprintf("Cache-Control: %s, max-age=%s", $cfg['cache_control'], $cfg['cache_control_max_age']), true);
			}
			else {
					
				header(sprintf("Cache-Control: %s", $cfg['cache_control']), true);
			}
		}
	
		if(isset($cfg['expires'])) {
	
			$exp = $cfg['expires'];
	
			$vld = new Validator($exp);
	
			header(sprintf("Expires: %s", $cfg['expires']), true);
		}
	
		if(isset($cfg['access_control_allow_origin'])) {
	
			header(sprintf("Access-Control-Allow-Origin: %s", $cfg['access_control_allow_origin']));
		}
	
		header(sprintf("Content-Type: %s", $cfg['content_type']));
	
		header('Content-Length: ' . filesize($file));
	
		readfile($file);
	
		ob_end_flush();
	}
	else {
		
		header("HTTP/1.0 404 Not Found");
	}
	
	exit;
}

if(isset($path) && !is_null($path) && $path != "") {

	$params = preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);//explode('/', $url);

	if(count($params) > 1) {

		new Runtime($params, $url);
		return;
	}
}

include_once 'Route.php';
include_once 'App/Route/DefaultRoute.php';

$route = new DefaultRoute();

$route->setClientsAddress($_SERVER['REMOTE_ADDR']);

$path = $route->getDefaultPath($url);

if(isset($path) && !is_null($path)) {

	$params = preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);//explode('/', $path);

	if(count($params) > 1) {

		new Runtime($params, $path);
	}
}
else {

	$path = $route->getNotFoundPath($url);

	if(isset($path) && !is_null($path)) {

		$params = preg_split('/\\//', $url, -1, PREG_SPLIT_NO_EMPTY);//explode('/', $path);

		if(count($params) > 1) {

			new Runtime($params, $path);
		}
	}
}
?>