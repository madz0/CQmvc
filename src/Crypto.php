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

class Crypto {
	
	private static $bf_secret = 'change me in production';
	
	public static function hashEquals($str1, $str2) {
		
		/* from php.net */
		if(!function_exists('hash_equals')) {
			
			function hash_equals($str1, $str2) {
				
				if(strlen($str1) != strlen($str2)) {
					
					return false;
				} else {
					
					$res = $str1 ^ $str2;
					$ret = 0;
					for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
					return $ret === 0;
				}
			}
		}
		
		return hash_equals($str1, $str2);
	}
	
	public static function hashPassword($pass) {
		
		/*from https://alias.io/2010/01/store-passwords-safely-with-php-and-mysql*/
	
		// A higher "cost" is more secure but consumes more processing power
		$cost = 10;
		
		// Create a random salt
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		
		// Prefix information about the hash so PHP knows how to verify it later.
		// "$2a$" Means we'reusing the Blowfish algorithm. The following two digits are the cost parameter.
		$salt = sprintf("$2a$%02d$", $cost) . $salt;
		
		//Hash the password with the salt
		$hash = crypt($pass, $salt);
		
		return $hash;
	}
	
	public static function comparePassword($pass, $hashedPass) {
	
		/*from https://alias.io/2010/01/store-passwords-safely-with-php-and-mysql/*/
		
		//if (!self::hashEquals($hashedPass, crypt($pass, $hashedPass)) ) {
		if ($hashedPass !== crypt($pass, $hashedPass)) {

			return false;
		}
		
		return true;
	}
	
	public static function bfEncrypt($value) {
		
		/* from SO by Mikelangelo */
		if(!$value){return false;}
	   	$key = self::$bf_secret;
	   	$text = $value;
	   	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
	   	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	   	$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
	   	return trim(base64_encode($crypttext));
	}
	
	public static function bfDecrypt($value) {
	
		/* from SO by Mikelangelo */
		if(!$value){return false;}
   		$key = self::$bf_secret;
   		$crypttext = base64_decode($value); //decode cookie
   		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
   		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   		$decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
   		return trim($decrypttext);
	}
}