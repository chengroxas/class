<?php
	
class Dbfactory{
	private static $_classes=NULL;
	private static $_config=NULL;
	
	public static function create($type){
		self::$_config=Loader::config('Db');
		if(!isset(self::$_classes["{$type}"])){
			require_once $type.".class.php";
			self::$_classes["{$type}"]=new $type(self::$_config);
		}
		return self::$_classes["{$type}"];
	}
		
}
