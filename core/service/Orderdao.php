<?php

require_once(CLASSROOT . '/core/ObjFactory.php');
require_once(CLASSROOT . '/db/DBConn.php');
require_once(CLASSROOT . '/config/Constants.php');

/**
 * @todo exception if multiple or no rows found
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Orderdao extends Basedao
{
	
	protected $dataClass = 'Order';
	protected $itemError = oeiSampleServerException::ORDERNOTFOUND;	

	public $_attributes;
	
	public function __construct($id=0,$keyName = 'id',$table='orders'){
		if($id){
			parent::__construct($id, $keyName, $table);
		}
	}

}

?>