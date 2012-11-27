<?php
defined('DIRACCESS') or die('Cannot access this directly');

require_once(CLASSROOT . '/exception/yafException.php');
require_once(CLASSROOT . '/base/ObjFactory.php');
require_once(CLASSROOT . '/config/Constants.php');

/**
 * DB Connection Object
 * @author John Skrzypek
 * @copyright
 * @todo revisit $dbcreds check - error
 */

class DBConn
{
	private static $connectionWrapper = false;
	//private $wrapper = false;
	
	private function __construct() {}
	private function __clone() {}
	
	private function getConnection(){

		//if(!self::$instance) {
		if(!self::$connectionWrapper) {
			
			$dbcreds = xmlToArray::getArray(CONFIG.'/'.Constants::config,'config-root','config','database');

			if(!is_array($dbcreds)){
				throw new yafException(yafException::DBCONN);
			}
			
			//create appropriate DB object
			self::$connectionWrapper = ObjFactory::getObject($dbcreds['DBType']);
			
			/*if(!(self::$connectionWrapper->connect($dbcreds['DBHost'],$dbcreds['DBUser'],$dbcreds['DBPassword'],$dbcreds['DBName']))){
				throw new yafException(yafException::DBCONN,yafException::FATAL);
			}*/
			try {
				self::$connectionWrapper->connect($dbcreds['DBHost'],$dbcreds['DBUser'],$dbcreds['DBPassword'],$dbcreds['DBName']);
			} catch (yafException $e){
				print $e;
			}
		}
		
		return self::$connectionWrapper;
	}
	
	public static function getInstance() {
		return self::getConnection();
	}
	
}


?>