<?php

require_once('testHeader.php');
require_once('ddl/all.php');


/*
* Create a new user and save to database
*/ 

class TestUserDAO extends UnitTestCase {

	protected $mysqli; //mysqli object

	function __construct(){
		$this->mysqli = DBConn::getInstance();
		/* to do
		save user to db and reload
		*/


		
	}

	function setup(){
		$this->assertTrue($this->mysqli->multi_query(USERS_TABLE),$this->mysqli->error); //setup test users table - multi query since we're dropping table first
	}

	function testUserAccessors(){
		$user = new User;
		$user->setFname("John");
		$user->setLname("Smith");
		$user->setEmail("jms8142@gmail.com");


		$this->assertEqual($user->getFname(),"John");
		$this->assertEqual($user->getLname(),"Smith");
		$this->assertEqual($user->getEmail(),"jms8142@gmail.com");
	}
	/*
	function testUserSave(){
		
	}

	function testUserPersistence() {

	}
*/
}
