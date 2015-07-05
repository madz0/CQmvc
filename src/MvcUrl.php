<?php

class MvcUrl {
	
	private $url;
	
	public function __construct($view, $controler = '', $area = '', $params = array()) {
		
		if($controler == '') {
			
			if(strpos($view, '/') !== false) {
			
				$this->url = "/$view";
				return;
			}
			
			return new Exception("Invalid url");
		}
		
		$querystring = "";
		
		if(count($params)>0) {

			$querystring = "?";
				
			foreach ($params as $key => $val) {
			
				$querystring .= $key."=".$val."&";
			}
				
			$querystring = substr($querystring, 0,strlen($querystring)-1);
		}
		
		if($area == '') {
			
			$this->url = "/$controler/$view$querystring";
		}
		else {
			
			$this->url = "/$area/$controler/$view$querystring";
		}
	}
	
	public function getURL() {
		
		return $this->url;
	}
}
?>