<?php

/**
 * creation of DB queries - accepts one domain object and associated mapper for UPDATE/INSERTS
 * @author John Skrzypek
 * @copyright 
 */

defined('DIRACCESS') or die('Cannot access this directly');

require_once(FUNC . '/common/common.functions.php');

class QueryBuilder
{
	const QUERYSELECT = "SELECT";
	const QUERYUPDATE = "UPDATE";
	const QUERYINSERT = "INSERT";
	
	private $queryString;
	private $valobj;
	private $mapper;
	private $table;
	private $keyName;
	private $key;
	private $filter;
	private $condition;
	
	public function __construct($query = self::QUERYSELECT,$filter='*'){
		$this->queryString = $query;
		$this->filter = $filter;
	}
	
	public function setCondition(Filter $condition){
		$this->condition = $condition;
	}
	
	public function setValueobj($obj=''){
		$this->valobj = $obj;
	}
	
	public function setMapper($mapper = ''){
		$this->mapper = $mapper;
	}
	
	public function setTable($table=''){
		$this->table = $table;
	}
	
	public function setKey($id = 0){
		$this->key = $id;
	}
	
	public function setKeyName($keyName = 'id'){
		$this->keyName = $keyName;
	}
	
	public function parse(){
		switch ($this->queryString) {
			case self::QUERYSELECT:
				return $this->select();
			case self::QUERYUPDATE:
				return $this->update();
			case self::QUERYINSERT:
				return $this->insert();
			default:
				return '';
		}
			 
	}
	
	private function select(){
		$str = '';
		if($this->table){
			$str = self::QUERYSELECT . " " . $this->filter . " FROM " . $this->table;
			
			if($this->key) {
				//$primary = DBMapper::getPrimaryKey() - something like this
				$primary = $this->keyName;
				$str .= " WHERE $primary = ". $this->makeQuotes($this->key,$primary);
			}
			
			if($this->condition)
				$str .= " WHERE " . $this->condition->getWhere();
		}
		
		return $str;
	}
	
	private function update(){
		$str = '';
		
		if($this->table && $this->mapper && $this->valobj){
			$str = self::QUERYUPDATE . " " . $this->table . " SET ";
	
			foreach($this->mapper as $property){
				if(isset($property['mutator'])){
					//set property
					$name = $property['name'];
					$accessor = $property['accessor'];
					
					//handled modifyDate
					//Temporary
					if($name == 'modifyDate') {
						$str .= " $name = '" . date('Y-m-d H:i:s',time()) . "',";
					} elseif($name == 'expiration'){
						$str .= " $name = '" . dateConvert($this->valobj->$accessor(),Constants::DATETIMEFORMAT,'Y-m-d') . "',";
					} else {
						$str .= " $name = '" . addslashes($this->valobj->$accessor()) . "',";
					}
				}
			}
	
			$str = $this->striplast($str);
			
			if($this->key) {
				//$primary = DBMapper::getPrimaryKey() - something like this
				$primary = $this->keyName;
				$str .= " WHERE $primary = ". $this->makeQuotes($this->key,$primary);
			}			
		}
		return $str;
	}
	
	private function insert(){
		$str = '';
		
		if($this->table && $this->mapper && $this->valobj){
			$str = self::QUERYINSERT . " INTO " . $this->table;
			foreach($this->mapper as $property){
				if(isset($property['mutator'])){
					//set property
					$name[] = $property['name'];
					$accessor = $property['accessor'];
					
					//Temporary
					if($property['name'] == 'modifyDate') {
						$values[] = "'" .  date('Y-m-d H:i:s',time()) . "'";
					} elseif ($property['name'] == 'createDate'){
						$values[] = "'" .  date('Y-m-d H:i:s',time()) . "'";
					} elseif($property['name'] == 'expiration') {
						$values[] = "'" . dateConvert($this->valobj->$accessor(),Constants::DATETIMEFORMAT,'Y-m-d') . "'";
					} else {
						$values[] = "'" . addslashes($this->valobj->$accessor()) . "'";
					}
					
				}
			}
		}
		
		$str .= "(" . implode(',',$name). ") VALUES (" . implode(",", $values) . ")";
					
		return $str;
	}
	
	public function delete(){
		$str = '';
		return $str;
	}
	
	private function striplast($str = ''){
		return substr($str,0,strlen($str)-1);
	}
	/**
	 * temp hardcoded to handle non int keys
	 * @return 
	 * @todo change to table properties accessed from db or mapper xml 
	 */
	private function makeQuotes($val,$keycol){
		
		if($keycol != 'id')
			$val = "'" . $val . "'";
		
		return $val;
	}
}

?>