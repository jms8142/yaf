<?php

require_once(CLASSROOT . '/service/Basedao.php');

defined('DIRACCESS') or die('Cannot access this directly');

class Userdao extends Basedao
{
	protected $dataClass = 'User';
	protected $itemError = yafException::USERNOTFOUND;

	public $attributes;
	
	public function __construct($id=0,$keyName = 'id',$table='users'){
		if($id){
			parent::__construct($id, $keyName, $table);
		}
	}
}

?>