<?php

require_once(CLASSROOT . '/dao/Basedao.php');

defined('DIRACCESS') or die('Cannot access this directly');

class Userdao extends Basedao
{
	protected $table = 'users';
	protected $dataClass = 'User';
	protected $itemError = oeiSampleServerException::USERNOTFOUND;

	public $attributes;
	
	public function __construct($id=0,$keyName = 'id'){
		if($id){
			parent::__construct($id, $keyName);
		}
	}	
}

?>