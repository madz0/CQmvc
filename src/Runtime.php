<?php

class TypeDescriptor {
	
	public static $TYPE_PRIMITIVE = 1;
	
	public static $TYPE_ARRAY = 2;
	
	public static $TYPE_OBJ = 3;
	
	public $type;
	
	public $className;
	
	public $name;
	
	public $default;
	
	public $hasDefault = false;
}

class Runtime {
	
	/**
	 * 
	 * @var array
	 */
	private $params;
	
	private $url;
	
	public static $PARAMS_QUERY="url_9876786554333AdxZsssErCCCPPRFAwds_X_e";
	
	public function __construct($url) {
		
		if($url==null) {
			
			return;
		}
		
		$this->url = $url;
		
		$this->params = array();
		
		foreach ($_GET as $index=>$value) {

			if($index != self::$PARAMS_QUERY) {
				
				$this->params[$index] = $value;
			}
		}
		
		foreach ($_POST as $index=>$value) {

			$this->params[$index] = $value;
		}
		
		include_once 'FileBase.php';
		
		foreach ($_FILES as $index=>$value) {
			
			if(is_array($value['name'])) {

				$arrFile = $this->diverse_array($value);
				
				$previousValue = $this->params[$index];
				
				if(isset($previousValue)) {

					if(!is_array($previousValue)) {

						$xx = array();
						$xx[$index] = $previousValue;
						$previousValue = $xx;
					}
				}
				else {
					
					$previousValue = array();
				}
				
				foreach ($arrFile as $index2=>$theFile) {
					
					$fileBase = new FileBase();
						
					$fileBase->name = $theFile['name'];
					$fileBase->tmpName = $theFile['tmp_name'];
					$fileBase->error = $theFile['error'];
					$fileBase->type = $theFile['type'];
					$fileBase->size = $theFile['size'];
					
					$previousValue[$index2] = $fileBase;
				}
				$this->params[$index] = $previousValue;
			}
			else {

				$fileBase = new FileBase();
					
				$fileBase->name = $value['name'];
				$fileBase->tmpName = $value['tmp_name'];
				$fileBase->error = $value['error'];
				$fileBase->type = $value['type'];
				$fileBase->size = $value['size'];
					
				$this->params[$index] = $fileBase;
			}
		}

		try {
			
			$params = explode('/', $url);
			
			$pCount = count($params);
			
			if( $pCount > 1) {
				
				$contrl = $params[$pCount-2];
				
				$view = $params[$pCount-1];
				
				include_once 'Ctrl.php';

				$ctrl_path = "App/Control/$contrl.php";
				
				if (file_exists($ctrl_path)) {

					include_once $ctrl_path;

					chdir('App');
					$this->populate($contrl, $view, $params);
				}
			}
		}
		catch (Exception $e) {}
	}
	
	private function getParamDescriptor(ReflectionParameter $param) {
		
		$td = new TypeDescriptor();
		
		$td->name = $param->name;
		
		if ($param->isOptional()) {
			
			$td->default = $param->getDefaultValue();
			
			$td->hasDefault = true;
		}
		else {
			
			$td->hasDefault = false;
			unset($td->default);
		}
		
		try {
		
			if($param->isArray()) {
				
				$td->type = TypeDescriptor::$TYPE_ARRAY;
				
				return $td;
			}
			else {
					
				$type = $param->getClass();

				if(is_object($type)) {

					$hint = $param->getClass()->getName();
				}
				
				if(isset($hint)) {
						
					$td->type = TypeDescriptor::$TYPE_OBJ;
					$td->className = $hint;
				}
				else {
						
					$td->type = TypeDescriptor::$TYPE_PRIMITIVE;
				}
				return $td;
			}
		}
		catch (Exception $e) {

			$parts = explode(' ', $e->getMessage(), 3);
			
			$td->type = TypeDescriptor::$TYPE_PRIMITIVE;
			return $td;
		}
	}
	
	private function populate($control, $view, array $path) {
		

		$reflector = new ReflectionClass($control);

		/* @var $method = ReflectionMethod */
		$method = $reflector->getMethod($view);

		$parameters = $method->getParameters();
		
		$params = array();
		
		$routeInfo = array('path'=>$path);
		
		/*This should get called here*/
		$c = $reflector->newInstanceArgs($routeInfo);
		
		/* @var $checkRendered = ReflectionMethod */
		$checkRendered = $reflector->getMethod("isRendered");
		
		$isRendered = $checkRendered->invokeArgs($c, array());
		
		if($isRendered) {
				
			return;
		}
		
		foreach($parameters as $param) {
			
			/* @var $td TypeDescriptor */
			$td = $this->getParamDescriptor($param);
			
			$is_found = false;
			
			if($td->type == TypeDescriptor::$TYPE_OBJ) {
					
				$nameIndex = $td->name.'->';
				
				$objArr = array();
				
				foreach ($this->params as $var => $value) {

					$idx = strpos($var, $nameIndex, 0);
					if( $idx !== false && $idx == 0 ) {

						$objArr[$var] = $value;
						$is_found = true;
					}
				}
				
				if($is_found) {

					$root = new $td->className;
					$this->populateComplex($objArr, $root, null, null);
					$params[] = $root;
				}
				else if($td->hasDefault) {
					
					$params[] = $td->default;
				}
				else {
					
					throw new Exception("Wrong object parameters for ".$view);
				}
			}
			else if($td->type == TypeDescriptor::$TYPE_ARRAY) {
				
				$name = $td->name;
				$is_found = false;
					
				$nameIndex = $td->name.'{';
				
				$objArr = array();
				
				foreach ($this->params as $var => $value) {
				
					$idx = strpos($var, $nameIndex, 0);
					if( $idx !== false && $idx == 0 ) {
				
						$objArr[$var] = $value;
						$is_found = true;
					}
				}
				
				if($is_found) {

					$root = array();
					$this->populateComplex($objArr, $root, null, null);
					$params[] = $root;
				}
				else if($td->hasDefault) {
						
					$params[] = $td->default;
				}
				else {
				
					throw new Exception("Wrong array parameters for ".$view);
				}
			}
			
			else if ($td->type == TypeDescriptor::$TYPE_PRIMITIVE) {
				
				if(isset($this->params[$td->name])) {
					
					$params[] = $this->params[$td->name];
				}
				else if($td->hasDefault) {
					
					$params[] = $td->default;
				}
				else {

					throw new Exception("Wrong primtive parameters for ".$view);
				}
			}
		}
			
		$method->invokeArgs($c,$params);
	}
	
	public function populateComplex($params, & $root, $chains, $value) {
		
		if(!is_null($params)) {
				
			foreach ($params as $var => $value) {
				
				$arrIndex = strpos($var, '{', 0);
				$objIndex = strpos($var, '->', 0);

				if( $objIndex !== false && ($arrIndex === false || $arrIndex>$objIndex) ) {
				
					$chains = explode('->', $var, 2);

					$this->populateComplex(null,$root, $chains[1], $value);
				}
				else if( $arrIndex !== false && ($objIndex === false || $arrIndex < $objIndex)) {
				
					$chains = explode('{', $var, 2);
					
					if(isset($chains[1])) {
						
						/*$chains = explode('}', $chains[1], 2);
						$property = $chains[0];
						
						$newRoot = null;
						
						if(isset($property)) {

							if(!isset($root[$property])) {
								
								$newRoot = new stdClass();
								$root[$property] = $newRoot;
							}
							else {
							
								$newRoot = $root[$property];
							}	
						}
						else {

							$newRoot = new stdClass();
							$root[] = $newRoot;
						}
						
						if(isset($chains[1]) && $chains[1] != '') {
						
							if($chains[1][0] == '-' && $chains[1][1] == '>') {
						
								$chains[1] = substr($chains[1],2);
							}
						} */
						
						$chains[1] = '{'.$chains[1];

						$this->populateComplex(null,$root, $chains[1], $value);
					}
				}
				else {
						
					if(is_object($value)) {
							
						$root[$chains[0]] = $value;
					}
					else {
							
						$root[$chains[0]] = trim($value);
					}
				}
			}
		}
		else {

			$arrIndex = strpos($chains, '{', 0);
			$objIndex = strpos($chains, '->', 0);
			
			if( $objIndex !== false && ($arrIndex === false || $arrIndex>$objIndex) ) {
				
				$chains = explode('->', $chains, 2);
				
				if(!isset($chains[0])) {
					
						
				}
				
				if(!isset($root->{$chains[0]})) {
						
					$root->{$chains[0]} = new stdClass();
				}
				
				return $this->populateComplex(null,$root->{$chains[0]}, $chains[1], $value);
			}
			else if( $arrIndex !== false && ($objIndex === false || $arrIndex < $objIndex)) {
				
				$chains = explode('{', $chains, 2);

				if(isset($chains[0]) && $chains[0] != '') {
					
					$property = $chains[0];

					if(!isset($root->{$property})) {

						$root->{$property} = array();
					}
					
					$chains[1] = '{'.$chains[1];
					
					return $this->populateComplex(null,$root->{$property}, $chains[1], $value);
				}
				else {

					$chains = explode('}', $chains[1], 2);
					
					if(!isset($root[$chains[0]])) {
						
						if(isset($chains[1]) && $chains[1] != '' && $chains[1][0] == '{') {
							
							$root[$chains[0]] = array();
						}
						else {
							
							$root[$chains[0]] = new stdClass();
						}
					}
					
					if(isset($chains[1]) && $chains[1] != '') {

						if($chains[1][0] == '-' && $chains[1][1] == '>') {
								
							$chains[1] = substr($chains[1],2);
						}
					}
					
					return $this->populateComplex(null,$root[$chains[0]], $chains[1], $value);
				}
			}
			else {

				if(is_array($value)) {

					$root->{$chains} = array();
					return $this->populateComplex($value,$root->{$chains}, null, null);
				}
				
				if(is_object($value)) {

					if($chains == '') {
							
						return $root = $value;
					}
					
					if(is_array($root)) {
						
						return $root[$chains] = $value;
					}
					
					return $root->{$chains} = $value;
				}
				else {

					if($chains == '') {
							
						return $root = trim($value);
					}
					
					if(is_array($root)) {

						return $root[$chains] = trim($value);
					}

					return $root->{$chains} = trim($value);
				}
			}
		}
	}
	
	function diverse_array($vector) {
		 
	    $result = array(); 
	    foreach($vector as $key1 => $value1) 
	        foreach($value1 as $key2 => $value2) 
	            $result[$key2][$key1] = $value2; 
	    return $result; 
	}
	
	function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
	
		return (substr($haystack, -$length) === $needle);
	}
}

$url = $_GET[Runtime::$PARAMS_QUERY];

if(isset($url) && !is_null($url) && $url != "") {

	new Runtime($url);
}
else {

	include_once 'Route.php';
	include_once 'App/Route/DefaultRoute.php';
	
	$route = new DefaultRoute();
	
	$route->setClientsAddress($_SERVER['REMOTE_ADDR']);

	new Runtime($route->getDefaultPath());
}