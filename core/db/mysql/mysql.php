<?php
require_once(CLASSROOT . '/db/DBWrapper.php');
require_once(CLASSROOT . '/logger/Logger.php');
require_once(CLASSROOT . '/exception/yafException.php');
/**
 * wrapper for mysql
 * @author John Skrzypek
 */

/**
 * TTD: finish error check / logging
 */

defined('DIRACCESS') or die('Cannot access this directly');

class mysql implements DBWrapper
{
	public $num_rows = 0;
	private $result;
	protected $firephp; //FirePHP console logging for FireFox
	protected $mysqli;
	
	public function __construct(){
		$this->firephp = FirePHP::getInstance(true);
	}
	
	public function getNumrows(){
		$this->num_rows = mysql_num_rows($this->result);
		return $this->num_rows;
	}
	
	public function connect($host,$user,$password,$db){
		//if(!$link = @mysql_connect($host,$user,$password)){
		if(!$this->mysqli = new mysqli($host,$user,$password,$db)){
			//Logger::logError('FATAL - Cannot connect to ' . $host);
			Logger::logError("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error);
			throw new yafException(yafException::DBCONN,yafException::FATAL);
		}
		//$this->firephp->info('connnceted!');
		return $this->mysqli;
	}
	
	public function select_db($database){

		if(!$this->mysqli->select_db($database)){
			Logger::logError("Failed to select database $database: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error);
			throw new yafException(yafException::SELECTDB,yafException::FATAL);
		}
		
		return true;
	}
	
	public function query($query_str,$link){
		//	$this->firephp->info($query_str,"query");
		//$this->firephp->info($link,"dblink");

		if(!$this->result = @mysql_query($query_str,$link)) {
			$this->firephp->info(mysql_error($link),"FAILED");
			Logger::logError('FATAL - ' . @mysql_errno($link).": " . @mysql_error($link) . " (QUERY:$query_str)");
			throw new yafException(yafException::QUERY,yafException::FATAL);			
		}

		return $this->result;
	}
	
	public function fetch_assoc_row(){
		return mysql_fetch_assoc($this->result);
	}
	
	public function data_seek($num){
		return mysql_data_seek($this->result,$num);
	}
	
	public function insert_id(){
		return mysql_insert_id();
	}
	
	public function getError($link){
		return mysql_error($link);
	}
	
	public function getErrno($link){
		return mysql_errno($link);
	}
	
	public function getResult(){
		return $this->result;	
	}
}

?>