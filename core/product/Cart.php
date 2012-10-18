<?php

/**
 * @author John Skrzypek
 * @copyright
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Cart
{
	private $id;
	private $total;
	
	public function getCount(){
		return $this->total;
	}
	
	public function add(Product $product){
		//$this->collection[]
	}
	
	public function remove(Product $product){
		//$this->collection
	}
	
	public function showItems(){
		foreach($this->collection as $product){
			print $product . "<br/>";
		}
	}
}

?>