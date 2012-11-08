<?php
/**
 * database error catching
 */


require_once(CLASSROOT . '/base/Overloader.php');

defined('DIRACCESS') or die('Cannot access this directly');

class User extends Overloader
{	
	protected $firephp; //debug

	public function __construct(){
		$this->firephp = FirePHP::getInstance(true);
	}
	
	

}

?>