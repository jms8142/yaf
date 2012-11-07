<?php
defined('DIRACCESS') or die('Cannot access this directly');

class ObjFactory
{
	public static function getObject($objType){
		if(!class_exists($objType))
			throw new yafException(yafException::NOCLASS,yafException::FATAL);	
		
		return new $objType;
		
	}
	
}

?>