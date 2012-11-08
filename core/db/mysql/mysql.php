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
		if(get_class($this->mysqli)==='mysqli')
			return $this->result->num_rows;

		return 0;
	}
	
	public function connect($host,$user,$password,$db){
		echo 'connecting';
		//if(!$link = @mysql_connect($host,$user,$password)){
		if(!$this->mysqli = new mysqli($host,$user,$password,$db)){
			//Logger::logError('FATAL - Cannot connect to ' . $host);
			Logger::logError("Failed to connect to MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error);
			throw new yafException(yafException::DBCONN,yafException::FATAL);
		}
		return $this->mysqli;
	}
	
	public function query($query_str){
		$this->firephp->info('query');
		$this->firephp->info($this->mysqli);
		$result = null;
		if(get_class($this->mysqli)==='mysqli'){
			//$this->result->free();

			if(!$this->result = $this->mysqli->query($query_str)) {
				//$this->firephp->info(mysql_error($link),"FAILED");
				echo 'error' . mysqli_error($resource);
				Logger::logError("FATAL: (" . $resource->connect_errno . ") " . $resource->connect_error);
				throw new yafException(yafException::QUERY,yafException::FATAL);			
			}
		}
		//return $this->result;
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