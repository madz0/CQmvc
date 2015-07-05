<?php

@session_start();
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
}
?>