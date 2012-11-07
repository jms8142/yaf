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
	
	public function __construct(){
		$this->firephp = FirePHP::getInstance(true);
	}
	
	public function getNumrows(){
		$this->num_rows = mysql_num_rows($this->result);
		return $this->num_rows;
	}
	
	public function connect($host,$user,$password){
		if(!$link = @mysql_connect($host,$user,$password)){
			Logger::logError('FATAL - Cannot connect to ' . $host);
			throw new oeiSampleServerException(oeiSampleServerException::DBCONN,oeiSampleServerException::FATAL);
		}
		//$this->firephp->info('connnceted!');
		return $link;
	}
	
	public function select_db($database,$link){
		if(!mysql_select_db($database,$link)){
			Logger::logError('FATAL - ' . @mysql_errno($link).": " . @mysql_error($link));
			throw new oeiSampleServerException(oeiSampleServerException::SELECTDB,oeiSampleServerException::FATAL);
		}
		
		return mysql_select_db($database,$link);
	}
	
	public function query($query_str,$link){
		//	$this->firephp->info($query_str,"query");
		//$this->firephp->info($link,"dblink");

		if(!$this->result = @mysql_query($query_str,$link)) {
			$this->firephp->info(mysql_error($link),"FAILED");
			Logger::logError('FATAL - ' . @mysql_errno($link).": " . @mysql_error($link) . " (QUERY:$query_str)");
			throw new oeiSampleServerException(oeiSampleServerException::QUERY,oeiSampleServerException::FATAL);			
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