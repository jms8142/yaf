<?php

require_once(CLASSROOT . '/core/ObjFactory.php');
require_once(CLASSROOT . '/db/DBConn.php');
require_once(CLASSROOT . '/config/Constants.php');

/**
 * reconsider this approach
 * public attributes?
 * @todo exception if multiple or no rows found
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Productdao extends Basedao
{
	//possible active record method
	//const INSERT_SQL = "INSERT INTO Users (fname,lname,description,etc...) VALUES (?,?,?,?)";
	//const SELECT_BY
	//also, protect ID in Basedao
	
	protected $dataClass = 'Product';
	protected $itemError = oeiSampleServerException::PRODUCTNOTFOUND;	

	public $_attributes;
	
	public function __construct($id=0,$keyName = 'id',$table='products'){
		if($id){
			parent::__construct($id, $keyName, $table);
		}
	}

	public function save($id){
		//into parent?
//		$wrapper->query(self::INSERT_SQL,array($this->getFname(),$this->getLname, etc...)
			//update timestamps seperately?
	}
}

?>