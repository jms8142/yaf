<?php require_once('../testHeader.php');


class TestDBConnection extends UnitTestCase {

	
	function testDBConn(){
		$this->conn = DBConn::getInstance();
		$this->assertEqual(get_class($this->conn),'mysql',"Resource is not a mysql object");
	}

	function testSingleton(){
	    $this->conn2 = DBConn::getInstance();
	    $this->assertReference($this->conn,$this->conn2,"Seperate connection objects created");
	}

}