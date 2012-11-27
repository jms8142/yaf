<?php

require_once(CLASSROOT . '/core/ObjFactory.php');
require_once(CLASSROOT . '/db/DBConn.php');
require_once(CLASSROOT . '/config/Constants.php');

/**
 * @todo exception if multiple or no rows found
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Pagedao extends Basedao
{
	
	protected $dataClass = 'Page';
	protected $itemError = oeiSampleServerException::PAGENOTFOUND;	

	public $_attributes;
	
	public function __construct($id=0,$keyName = 'id',$table='pages'){
		if($id){
			parent::__construct($id, $keyName, $table);
		}
	}
}

?>