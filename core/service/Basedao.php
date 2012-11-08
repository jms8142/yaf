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
	protected $wrapper;
	private $keyName;
	protected $firephp; //FirePHP console logging for FireFox
	
	public function __construct($id=0,$keyName='id'){
		$this->firephp = FirePHP::getInstance(true);
		
		//echo 'basedao:' . $id . ' keyName: ' . $keyName;
		if($id){
			$this->keyName = $keyName;			
			$this->loadItem($id);
		}
	}
	
	/**
	 * Load single item
	 * @return 
	 * @param object $id
	 */
	
	public function loadItem($id){
		if($id){
			
			//get some db credentials
			$session = new Session();
			
			if(!$session->get('dbcreds')){
				$dbcreds = xmlToArray::getArray(CONFIG.'/'.Constants::config,'config-root','config','database');

				print_r($dbcreds);
				//$session->set('dbcreds',$dbcreds);
			} else {
				$dbcreds = $session->get('dbcreds');
			}
			
			//$this->firephp->info($dbcreds);
			
			
			//load from db
			$mode = QueryBuilder::QUERYSELECT;
			$query = new QueryBuilder($mode);
			$query->setTable($this->table);	
			$query->setKey($id);
			$query->setKeyName($this->keyName);	
			$query_str = $query->parse();
			$this->firephp->info($query_str);
			//print '<hr>' . $query_str . '<hr>';
			$this->wrapper = DBConn::getInstance();
			//$this->firephp->info($this->wrapper,"wrapper");
			//$result = $this->wrapper->query($query_str,DBConn::getInstance());
			//$this->firephp->info($this->wrapper->getNumrows(),"rows");
			$this->firephp->info(get_class($this->wrapper));
			$this->wrapper->query($query_str);

			//$wrapper2 = new mysql();
			//$wrapper2->connect()



		
			if($this->wrapper->getNumrows() == 0) {
				Logger::logError('WARNING - ' .$this->wrapper->getError(DBConn::getInstance()) . ": " . 
				$this->wrapper->getErrno(DBConn::getInstance()) . " (QUERY:$query_str)");
	
				throw new yafException($this->itemError,yafException::WARNING);
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
		
		//if($domainobj->getEmail()){  //check a required field - update this to check all
		//if ($domainobj->checkRequired()){
			$this->wrapper = ObjFactory::getObject(Constants::dbtype);
			
			if($domainobj->getId() && $result = $this->wrapper->query("select id from {$this->table} where id = " .$domainobj->getId(),DBConn::getInstance()))
				$mode = QueryBuilder::QUERYUPDATE;
			else
				$mode = QueryBuilder::QUERYINSERT;
			
			$query = new QueryBuilder($mode);
			$query->setValueobj($domainobj);
			$query->setMapper(xmlToArray::getArray(CONFIG.'/'.Constants::dataMapper,'mapper',$this->dataClass,'field'));
			$query->setTable($this->table);
			$query->setKey($this->attributes[$this->keyName]);
			$query->setKeyName($this->keyName);
			
			$query_str = $query->parse();	
			
			//print $query_str;
			
			if(!$result = $this->wrapper->query($query_str,DBConn::getInstance())){
				Logger::logError('WARNING - ' .$this->wrapper->getError(DBConn::getInstance()) . ": " . 
				$this->wrapper->getErrno(DBConn::getInstance()) . " (QUERY:$query_str)");
				throw new oeiSampleServerException($this->itemError,oeiSampleServerException::WARNING);
			}
			
			if($mode == QueryBuilder::QUERYINSERT) //give new entries assigned db key
				$domainobj->setId($this->wrapper->insert_id());
		//}
		
	}
}

?>