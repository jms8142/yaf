<?php



require_once('../testHeader.php');




class TestAPI extends UnitTestCase {

	private $server, $timeout, $userAgent, $ch;


	function __construct(){
		$this->server = 'http://yaf:8888/api/';
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
		$request = '?m=testMethod';
		curl_setopt($this->ch, CURLOPT_URL, $this->server . $request);
		$this->assertEqual(curl_exec($this->ch),'{ "valid" : "1" , "msg" : "Success", "longmsg" : "", "data" : {"MethodRun":true} }');
	}



}