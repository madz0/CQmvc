<?php

class Store {
	
	private $obj;
	
	public function __construct($in) {
		
		$this->obj = $in;
	}
	
	public function serialize() {
		
		/* if(isset($this->obj)) {
			
			if(gettype($this->obj) == "object") {
				
				return base64_encode(serialize($this->obj));
			}
			
			return $this->obj; 
		} */
		
		if(isset($this->obj)) {
				
			return base64_encode(serialize($this->obj));
		}
	}
	
	public function desrialize() {
		
		/* if(isset($this->obj)) {
				
			if(gettype($this->obj) == "string") {
		
				return unserialize(base64_decode($this->obj));
			}
				
			return $this->obj;
		} */

		if(isset($this->obj)) {
		
			return unserialize(base64_decode($this->obj));
		}
	}
}
?>