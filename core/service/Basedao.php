<?php

/**
 * Primarily used to return multiple sets of inherited dao object
 * @todo add minimum required fields to save method - dynamic
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Basedao
{
	/**
	 * returns associated array of objects populated with db results
	 * @return array
	 */
	protected $table;
	protected $wrapper;
	private $keyName;
	protected $firephp; //FirePHP console logging for FireFox
	
	public function __construct($id=0,$keyName='id',$table=''){
		$this->firephp = FirePHP::getInstance(true);
		
		if($id){
			$this->table = $table;			
			$this->keyName = $keyName;			
			$this->loadItem($id);
		}
	}
	
	/**
	 * Load single item, return false if item not found
	 * @return 
	 * @param object $id
	 */
	
	public function loadItem($id){
		if($id){

			//$this->firephp->info($id);
			
			//get some db credentials
			$session = new Session();
			
			if(!$session->get('dbcreds')){
				$dbcreds = xmlToArray::getArray(CONFIG.'/'.Constants::config,'config-root','config','database');
				//$session->set('dbcreds',$dbcreds);
			} else {
				$dbcreds = $session->get('dbcreds');
			}
			
			//load from db
			$mode = QueryBuilder::QUERYSELECT;
			$query = new QueryBuilder($mode);
			$query->setTable($this->table);	
			$query->setKey($id);
			$query->setKeyName($this->keyName);	
			$query_str = $query->parse();
			//$this->firephp->info($query_str);

			$this->wrapper = DBConn::getInstance();
			$this->wrapper->query($query_str);

			
			if($this->wrapper->getNumrows() == 0) {
				Logger::logError('WARNING - ' . $this->wrapper->getError() . " (QUERY:$query_str)");
				throw new yafException($this->wrapper->getError(),yafException::WARNING);
			}
		
			$this->attributes = $this->wrapper->fetch_assoc_row();			

		}
		
		
	}	
	
	/**
	 * Return an array of objects based on child instance
	 * @return 
	 * @param object $filter
	 */
	 
	public function loadSet(Filter $filter = null){
		$mode = QueryBuilder::QUERYSELECT;
		$query = new QueryBuilder($mode);
				
		$query->setTable($this->table);	
		
		if($filter) {
			$query->setCondition($filter);
		}
		
		$query_str = $query->parse();		
		//$this->firephp = FirePHP::getInstance(true);
		//$this->firephp->info($query_str);

		//print $query_str;
		
		$this->wrapper = ObjFactory::getObject(Constants::dbtype);
		$result = $this->wrapper->query($query_str,DBConn::getInstance());
				
		if($this->wrapper->getNumrows() == 0){
			return false;
		} else {
			while($row = $this->wrapper->fetch_assoc_row()){
				$item = new $this->dataClass;
				$item->mapRow($row);
				$output[] = $item;
			}
			//$this->firephp->info($output);
			return $output;
		}
	}
	
	public function save($domainobj){
			$this->firephp = FirePHP::getInstance(true);
			$this->wrapper = DBConn::getInstance();

			

			if($domainobj->getId() && $this->wrapper->query("select id from {$this->table} where id = " .$domainobj->getId()))
				$mode = QueryBuilder::QUERYUPDATE;
			else
				$mode = QueryBuilder::QUERYINSERT;

			//$this->firephp->info($domainobj);
			
			
			$query = new QueryBuilder($mode);
			$query->setValueobj($domainobj);
			$query->setMapper(xmlToArray::getArray(CONFIG.'/'.Constants::dataMapper,'mapper',$this->dataClass,'field'));
			$query->setTable($this->table);
			$query->setKey($this->attributes[$this->keyName]);
			$query->setKeyName($this->keyName);
			
			$query_str = $query->parse();	
			
			
			if(!$this->wrapper->query($query_str,DBConn::getInstance())){
				Logger::logError('WARNING - ' .$this->wrapper->getError() . ": (QUERY:$query_str)");
				throw new yafException($this->wrapper->getError(),yafException::WARNING);

			}
			
			if($mode == QueryBuilder::QUERYINSERT) //give new entries assigned db key
				$domainobj->setId($this->wrapper->insert_id());

			return true;
		
	}
}

?>