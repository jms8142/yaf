<?php
defined('DIRACCESS') or die('Cannot access this directly');

require_once(CLASSROOT . '/exception/oeiSampleServerException.php');
require_once(CLASSROOT . '/core/ObjFactory.php');
require_once(CLASSROOT . '/config/Constants.php');

/**
 * DB Connection Object
 * @author One Eleven Interactive
 * @copyright
 * @todo revisit $dbcreds check - error
 */

class DBConn
{
	private static $instance = false;
	protected static $firephp; //FirePHP console logging for FireFox
	
	private function __construct() {}
	private function __clone() {}
	
	private function getConnection(){

		if(!self::$instance) {
			
		$session = new Session();
			if(!$session->get('dbcreds')){
				$dbcreds = xmlToArray::getArray(INCLUDES.'/'.Constants::config,'config-root','config','database');
				$session->set('dbcreds',$dbcreds);
			} else {
				$dbcreds = $session->get('dbcreds');
			}
			
			if(!is_array($dbcreds)){
				throw new oeiSampleServerException(oeiSampleServerException::DBCONN);
			}
			
			//create appropriate DB object
			$wrapper = ObjFactory::getObject($dbcreds['DBType']);
			
			if(!(self::$instance = $wrapper->connect($dbcreds['DBHost'],$dbcreds['DBUser'],$dbcreds['DBPassword']))){
				throw new oeiSampleServerException(oeiSampleServerException::DBCONN,oeiSampleServerException::FATAL);
			}
			
			if(!$wrapper->select_db($dbcreds['DBName'],self::$instance)){
				throw new oeiSampleServerException(oeiSampleServerException::SELECTDB . " -" . $dbcreds['DBName'],oeiSampleServerException::FATAL); 
			}
			
		}
		//FirePHP::getInstance(true)->info(self::$instance,"instance");
		return self::$instance;	
	}
	
	public static function getInstance() {
		return self::getConnection();
	}
	
}


?>