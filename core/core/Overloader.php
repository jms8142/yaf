<?php
/**
 * For Dynamic gets and sets - properties setup in format: (dao object) $entity->attributes['name']
 * Also contains mapper for db calls and loading functions
 * @author One Eleven Interactive
 * @copyright
 */

defined('DIRACCESS') or die('Cannot access this directly');

abstract class Overloader 
{
	/**
	 * @return property if called with get
	 * @param string $name
	 * @param string $val
	 */
	protected $entity;
	protected $firephp; //FirePHP console logging for FireFox

	/**
	 * catched get and set calls
	 * @return if set, returns property value
	 * @param object $name 
	 * @param object $val
	 */
	 
	public function __construct(){
		$this->firephp = FirePHP::getInstance(true);
	}
	
	public function __call($name,$val)
	{
	
		if(strpos($name,'set')===0){
			if($val){
				
				//set up dao if called first without loading an object
				if(!$this->entity){
					$dao = get_class($this) . 'dao';
					$this->entity = new $dao();		
				}
			
				$indexname = strtolower($name[3]) . substr($name,4);
				$this->entity->attributes[$indexname] = current($val);
			}
		} elseif (strpos($name,'get')===0) {
			$property = strtolower($name[3]) . substr($name,4);
			if(isset($this->entity->attributes[$property])){
				return $this->entity->attributes[$property];
			}
		} else {
			echo 'method/property: ' . $name . ' not found';
		}
	}
	
	/**
	 * assign associative array items to this object's attributes
	 * @return 
	 * @param Array $row
	 */
	
	public function mapRow($row){
		foreach($row as $field => $value){
			$this->entity->attributes[$field] = $value;
		}
	}
	
	/**
	 * print out attributes - may be temporary
	 * @return 
	 */
	
	public function __toString(){
		$output = '<b>Attributes:</b><pre>';
		$output .= print_r($this->entity->attributes,true);
		$output .=  '</pre>';
		return $output;
	}
	
	public function getAttributes(){
		return $this->entity->attributes;
	}
	
	/**
	 * Loads child object into instance
	 * @return 
	 * @param object $id
	 */
	
	public function loadById($id){
		if(!$this->entity->attributes['id']){ //don't double load
			try {
				$dao = get_class($this) . 'dao';
				return ($this->entity = new $dao($id));
			} catch (oeiSampleServerException $e){
				print $e;
				return false;
			}
		}
	}
	
	public function loadByField($key,$val){
		if(!isset($this->entity->attributes['id'])){
			$dao = get_class($this) . 'dao';

			try {
				$this->entity = new $dao($val,$key);
				return true;
			} catch (oeiSampleServerException $e){
				return false;
			}
		}		
	}
		
	
	public function save(){
		if($this->entity){  //check that dao exists
			$this->entity->save($this); //pass this object back to dao
			return true;
		}
	}
	
	/**
	 * 
	 * @return true if all required fields are populated and valid 
	 */
	
	public function checkRequired(){
		return true;
	}
	
	
}




?>