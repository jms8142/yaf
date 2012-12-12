<?php
require_once('testHeader.php');
require_once('ddl/all.php');


/*
* Create a new user and save to database
*/ 

init(); //start the app - this needs to start before the simple test to preserve the session creation


class TestUserDAO extends UnitTestCase {

	protected $conn; //mysqli object

	function __construct(){
		//create in-memory database here
		$this->conn = DBConn::getInstance();
		
	}

	function setup(){
		$this->conn->getConn()->query(DROP_USERS); 
		$this->conn->getConn()->query(USERS_TABLE); //setup test users table -
	}

	function testUserAccessors(){
		//mock object here
		$user = new User;
		$user->setFname("John");
		$user->setLname("Smith");
		$user->setEmail("jms8142@gmail.com");


		$this->assertEqual($user->getFname(),"John");
		$this->assertEqual($user->getLname(),"Smith");
		$this->assertEqual($user->getEmail(),"jms8142@gmail.com");
	}
	
	function testLoaderFalse(){
		$user = new User;
		$this->assertFalse($user->loadByField('lname','Smith'),'New user should return false');
	}


	
	function testUserPersistence() {
		$user = new User;
		$user->setFname('John');
		$user->setLname('Smith');
		$user->setPassword('12345');
		$user->setEmail('jms8142@gmail.com');

		try {
			$res = $user->save();
		} catch (yafException $e) {
			print $e;
		}

		$this->assertTrue($res,"User didn't save properly");

		//load new user into new object
		$user2 = new User;
		$user2->loadByField('lname','Smith');

		$this->assertEqual($user2->getFname(),$user->getFname());
		$this->assertEqual($user2->getLname(),$user->getLname());
		$this->assertEqual($user2->getPassword(),$user->getPassword());
		$this->assertEqual($user2->getEmail(),$user->getEmail());

	}

	function testUserProperties(){
		$userProperties = array('id','fname','lname','email','password');
		$user = new User;

		foreach($user->getProperties as $property){
			$this->assertTrue(array_key_exists($property,$testUserProperties));
		}

	}
	//seperate file
	function testDomainCreation(){
		$cat = yaf::newObject('cat',(IN_MEMORY|PERSISTENT));

		$this->assertTrue(file_exists('cat.php'));

		$this->assertTrue($cat instanceof 'cat');

		$cat->setColor('blue');
		$cat->setBreed('Calico');
		$cat->setName('Muffin')

		$catProperties = array('color','breed','name');

		foreach($cat->getProperties as $property){
			$this->assertTrue(array_key_exists($property,$catProperties));
		}

	}

	/*
	next:
	function testUpdateandPersistence(){

	}
	*/

}
