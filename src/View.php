<?php

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