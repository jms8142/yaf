<?
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors',1);

/**
* API gateway for internal GET requests
*
*
* @author  John Skrzypek <jms8142@gmail.com>
* 
*/

define('DIRACCESS',1);
if(!defined(ROOT))
	define('ROOT', dirname(dirname(__FILE__)));

require_once(ROOT . '/core/config/definitions.php');
require_once(ROOT . '/core/func/autoload.php');
require_once(ROOT . '/core/db/DBConn.php');
require_once(ROOT . '/lib/FirePHPCore/FirePHP.class.php'); //Firefox console reporting


$method = isset($_GET['m']) ? $_GET['m'] : '';


$api = new apiRun;
$api->runMethod($method);

$api->out();

class apiRun {
	private $validMethods = array("testMethod");
	private $response = "";
	private $longresponse = "";
	private $status = "";
	private $valid = false;
	private $data = "{}";
	private $complex = false; //set to true if status is returning a json object and not a string
	private $firephp;

	/*
	* Place you internal methods here - you must register them in the $validMethods array first
	* @param String $method Name of the method being called
	* @return void
	*/
	public function runMethod($method){

		//write to access log
		Logger::logMessage(date("m/d/Y G:i:s") . " : " . http_build_query($_GET) . "\n",'access_log');

		if(!in_array($method,$this->validMethods)){
			$this->response = "No such method";
			return;
		}


		switch ($method){
			//Add method logic here
			case "testMethod":
				//method logic here
				$this->valid = true;
				$this->response = "Success";
				$this->data = json_encode(array("MethodRun" => true));
			break;

		}


	}

	/*
	* Returns a json string - must call runMethod() first to populate
	* @return String json response
	*/

	public function out(){
		$tick = ($this->complex) ? '' : '"';
		if (!headers_sent()) {
      		header('HTTP/1.1 200 OK', true);
      		header("Content-Type: application/json; charset=UTF-8", true);
    	}
		printf('{ "valid" : "%s" , "msg" : "%s", "longmsg" : "%s", "data" : %s }',$this->valid, $this->response,$this->longresponse,$this->data);	
	}

}