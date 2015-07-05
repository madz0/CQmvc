<?php

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