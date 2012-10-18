<?php
/**
 * Iteration handling of objects
 * @author John Skrzypek
 * @copyright
 * @todo review
 */

defined('DIRACCESS') or die('Cannot access this directly');

require_once(INCLUDES . '/autoload.php');

class Collection implements Iterator
{
	public $objCollection = Array();
	protected $valid;
	
	public function __construct($type, Filter $filter = null){

		$objdao = $type . 'dao';
		$obj = new $objdao;
		
		$this->objCollection = $obj->loadSet($filter);	
		$this->firephp = FirePHP::getInstance(true);
		//$this->firephp->info($this->objCollection);
	}
	
	/*
	 * return a result set as an associative array - this bypasses collection object mapping
	 */
	
	public static function getList($table,Filter $filter = null){
		$mode = QueryBuilder::QUERYSELECT;
		$query = new QueryBuilder($mode);	
		$query->setTable($table);	
		
		if($filter) {
			$query->setCondition($filter);
		}
		
		$query_str = $query->parse();	
		//$this->firephp->info($query_str);
		$wrapper = ObjFactory::getObject(Constants::dbtype);
		$result = $wrapper->query($query_str,DBConn::getInstance());
				
		if($wrapper->getNumrows() == 0){
			return false;
		} else {
			while($row = $wrapper->fetch_assoc_row()){
				$output[] = $row;
			}
			return $output;
		}

		
	}
	
	public function count(){	
		if(empty($this->objCollection))
			return 0;
		
		return count($this->objCollection);
			
	}
	
	public function current(){
		return current($this->objCollection);
	}
	
	function next(){
		$this->valid = (false !== next($this->objCollection));
	}
	
	function key(){
		return key($this->objCollection);
	}
	
	function valid(){
		return $this->valid;	
	}
	
	function rewind(){
		$this->valid = (false !== reset($this->objCollection));
	}
}

?>