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
		Logger::logError(get_class($this->result));
		$this->firephp = FirePHP::getInstance(true);
	}
	
	public function getNumrows(){
		return ($this->result instanceof mysqli_result) ? $this->result->num_rows : 0;
	}
	
	public function connect($host,$user,$password,$db){
		if(!$this->mysqli = new mysqli($host,$user,$password,$db)){
			Logger::logError("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error);
			throw new yafException(yafException::DBCONN,yafException::FATAL);
		}
		return true;
	}
	
	public function query($query_str){
		
		$result = null;
		if(get_class($this->mysqli)==='mysqli'){
		
			if(!$res = $this->mysqli->query($query_str)) {
				Logger::logError("FATAL: (" . $this->mysqli->errno . ") " . $this->mysqli->error . ' : query: ' . $query_str);
				throw new yafException(yafException::QUERY . ' : ' . $query_str,yafException::FATAL);			
			}

			$this->result = $res;
			
		}
		return true;
	}

	public function getConn(){ //for pass through functionality
		return $this->mysqli;
	}
	
	public function fetch_assoc_row(){
		return ($this->mysqli instanceof mysqli) ? $this->result->fetch_assoc() : false;
	}
	
	public function data_seek($num){
		return ($this->mysqli instanceof mysqli) ? $this->result->data_seek($num) : false;
	}
	
	public function insert_id(){
		return ($this->mysqli instanceof mysqli) ? $this->mysqli->insert_id : 0;
	}
	
	public function getError(){
		return ($this->mysqli instanceof mysqli) ? $this->mysqli->error : '';
	}
	
	public function getErrno(){
		return ($this->mysqli instanceof mysqli) ? $this->mysqli->errno : 0;
	}
}

?>