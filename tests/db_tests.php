<?php require_once('testHeader.php');


class TestDBConnection extends UnitTestCase {

	
	function testDBConn(){
		$this->conn = DBConn::getInstance();
		$this->assertEqual(get_class($this->conn),'mysqli',"Resource is not a mysqli object");
	}

	function testSingleton(){
	    $this->conn2 = DBConn::getInstance();
	    $this->assertEqual($this->conn,$this->conn2); //assertReference won't work on resources
	}

}