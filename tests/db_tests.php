<?php require_once('testHeader.php');


class TestDBConnection extends UnitTestCase {

	
	function testDBConn(){
		$this->conn = DBConn::getInstance();
		$this->assertEqual(get_resource_type($this->conn),'mysql link',"Resource is not a mysql link");
	}

	function testSingleton(){
	    $this->conn2 = DBConn::getInstance();
	    $this->assertEqual($this->conn,$this->conn2); //assertReference won't work on resources
	}

}