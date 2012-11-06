<?php
require_once('testHeader.php');

/*
* Objects that don't except throw a yafException
*/ 

class TestDomainObjectCreation extends UnitTestCase {

	function testNewBadObject(){
		try {
			$address = new NonExistentClass;
			$this->assertIsA($address,'NonExistentClass');
		} catch (Exception $e){
			$this->assertIsA($e,'yafException');
		}
	}

	function testNewGoodObject(){
		try {
			$user = new User;
		} catch (Exception $e){
			print $e;
		}

		$this->assertIsA($user,'User');
	}


}