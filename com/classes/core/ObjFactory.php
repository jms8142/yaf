<?php
require_once(INCLUDES . '/autoload.php');

defined('DIRACCESS') or die('Cannot access this directly');

class ObjFactory
{
	public static function getObject($objType){
		if(!class_exists($objType))
			throw new oeiSampleServerException(oeiSampleServerException::NOCLASS,oeiSampleServerException::FATAL);	
		
		return new $objType;
		
	}
	
}

?>