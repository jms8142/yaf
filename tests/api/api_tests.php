<?php



require_once('../testHeader.php');




class TestAPI extends UnitTestCase {

	private $server, $timeout, $userAgent, $ch;


	function __construct(){
		$this->server = 'http://intermezzo:8888/api/';
		$this->timeout = 30;
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		$this->ch = curl_init(); 

		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER,false);
	}

	function testCURLExistence(){
		$this->assertTrue(function_exists('curl_init'));
	}

	function testNoMethod(){
		$request = '';
		curl_setopt($this->ch, CURLOPT_URL, $this->server . $request);
		$this->assertEqual(curl_exec($this->ch),'{ "valid" : "" , "msg" : "No such method", "longmsg" : "", "data" : {} }');
	}

	function testfindUser_noData(){
		$request = '?m=findUser';
		curl_setopt($this->ch, CURLOPT_URL, $this->server . $request);
		$this->assertEqual(curl_exec($this->ch),'{ "valid" : "false" , "msg" : "NOT FOUND", "longmsg" : "We couldn\'t find that DEA or Name in our system", "data" : [{"lname":"","dea":""}] }');
	}


	function testfindUser_allWrong(){
		$request = '?m=findUser&lname=Skrzypek&fname=John&dea=123355';
		curl_setopt($this->ch, CURLOPT_URL, $this->server . $request);
		$this->assertEqual(curl_exec($this->ch),'{ "valid" : "false" , "msg" : "NOT FOUND", "longmsg" : "We couldn\'t find that DEA or Name in our system", "data" : [{"lname":"Skrzypek","dea":"123355"}] }');
	}

	//these are from the intermezzo target list specifically - should really be an in memory database config
	function testfindUser_lastName(){
		$request = '?m=findUser&lname=Aaronson&fname=John&dea=123355';
		curl_setopt($this->ch, CURLOPT_URL, $this->server . $request);
		$this->assertEqual(curl_exec($this->ch),'{ "valid" : "false" , "msg" : "NOT FOUND", "longmsg" : "We found your last name but no matching DEA.", "data" : [{"lname":"Aaronson","dea":"123355"}] }');			
	}

	function testfindUser_dea(){
		$request = '?m=findUser&lname=Skrzypek&fname=John&dea=AA2671128';
		curl_setopt($this->ch, CURLOPT_URL, $this->server . $request);
		$this->assertEqual(curl_exec($this->ch),'{ "valid" : "false" , "msg" : "SUPPORT", "longmsg" : "We found your DEA but no matching name.", "data" : [{"lname":"Skrzypek","dea":"AA2671128"}] }');			
	}

	function testfindAll(){
		$request = '?m=findUser&lname=Aaronson&fname=Gary&dea=AA2671128';
		curl_setopt($this->ch, CURLOPT_URL, $this->server . $request);
		$this->assertEqual(curl_exec($this->ch),'{ "valid" : "true" , "msg" : "SUCCESS", "longmsg" : "", "data" : {"addresses":[{"id":"16067","dea":"AA2671128","address1":"MEDICAL OFFICE BLDG II STE 250","address2":"3998 KNIGHTS AND RED LION ROADS","city":"PHILADELPHIA","state":"PA","zip":"19114","newaddress":""}],"user":{"title":"Dr.","designation":"MD","fname":"Gary","lname":"Aaronson"},"0":{"lname":"Aaronson","dea":"AA2671128"}} }');			
	}


}