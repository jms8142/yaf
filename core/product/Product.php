<?php
require_once(CLASSROOT . '/dao/Productdao.php');
require_once(CLASSROOT . '/core/Overloader.php');

/**
 * Product = Drug
 * @author One Eleven Interactive
 * @copyright
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Product extends Overloader
{
	
	public function save(){
		try {
			$this->isValid();
		} catch (oeiSampleServerException $e){
			print $e;
			return false;
		}
		
		print 'about to update';
	}
	
	private function isValid(){
		
		if(!isset($this->entity)) {
			throw new oeiSampleServerException(oeiSampleServerException::NORECORD,oeiSampleServerException::WARNING);
		} elseif(is_int(intval($this->entity->attributes['id'])) && intval($this->entity->attributes['id']) > 0){
			return true;
		}
		
		throw new oeiSampleServerException(oeiSampleServerException::VALIDATE,oeiSampleServerException::WARNING);
	}
	
	public function getProductList(){
			
	}
	
	
}

?>