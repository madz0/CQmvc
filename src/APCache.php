<?php
include_once 'ICache.php';
class APCache implements ICache {
	
	private $ttl = 600; // Time To Live
	
	private $bEnabled = false; // APC enabled?
	
	// constructor
	function __construct($ttl) {
		
		$this->bEnabled = extension_loaded('apc');
		
		$this->ttl = $ttl;
	}
	
	public function get($key) {
		
		if($this->bEnabled) {
			
			$bRes = false;
			$vData = apc_fetch($key, $bRes);
			return ($bRes) ? $vData :null;
		}
		
		return null;
	}
	
	public function update($key, $data) {
		
		if(!$this->bEnabled) {
			
			return false;
		}
		
		return apc_store($key, $data, $this->ttl);
	}
	
	public function delete($key) {
		
		if(!$this->bEnabled)  {
			
			return true;
		}
		
		return (apc_exists($key)) ? apc_delete($key) : true;
	}
}