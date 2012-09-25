<?php


error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_NOTICE);
ini_set('display_errors',1);



//look for install
$install = file_exists( dirname(__FILE__) . '/admin/fin');

if(!$install)
	header('Location: /admin/install.php');


/**
 * Main landing page
 * @author John Skrzypek
 * @copyright
 * @todo error catching to loading rules dynamically
 */
define('DIRACCESS',1);

if(!defined(ROOT))
	define('ROOT', dirname(__FILE__));
	
require_once('detect.php');
require_once(ROOT . '/inc/definitions.php');
require_once(INCLUDES . '/messages.php');
require_once(INCLUDES . '/common/common.functions.php');
require_once(INCLUDES . '/autoload.php');
require_once(ROOT . '/lib/FirePHPCore/FirePHP.class.php'); //Firefox command line reporting
$firephp = FirePHP::getInstance(true);

/*
 * AGENT DETECTION HANDLING
 */
require_once(ROOT . '/inc/agent-detection.php');

/**
 * requests
 */
$request = Request::autofill();
$action  = $request->getRequest('action');
$pageID = ($request->getRequest('page')) ? $request->getRequest('page') : 'startPage'; //default page
/**
 * session handling
 */

$session = new Session;

/**
 * DEBUG
 */
//firephp: SESSION, DATABASE, TEMPLATING, CORE
//printRequest(0,SHOWSESSION | SHOWPOST | SHOWGET);
//$firephp->info($_SESSION,"Session");
//$firephp->info($_SESSION['cart']);
//mock requests:
/*
$action = 'display';
$request->set('GET','method','activateuser');
$pageID = 'orderSamples';
*/

/**
 * END DEBUG
 */

/**
 * app initialization
 */
if(!$session->get('init'))
	init();

if($action == 'mrun' && $method = $request->getRequest('method')) //load business rules script
	require_once(RULES . '/methods/run.' . $method . '.php');  	

$redirect = $pageID;

//redirect check for home && loggedin - should go to dashboard
if($pageID == 'startPage' && Session::get('loggedin'))
	$pageID = 'dashboard';


$page = new Page($pageID);


if($pageID <> 'loginPage'){ //no need to check if we've already been sent to logon
	if($page->getLoginrequired() && !(Session::get('loggedin'))){
		$page = new Page('loginPage'); //reload $page as login	
	}
}

$session->set('pageID', $page->getPageID()); //store this for all subcomponents to access

/**
 * page generation
 */
//look for any messages passed from loader scripts
//$firephp->info($alertMessage);
if($alertMessage){
	$page->addTagtoComponent($alertMessage->getComponentName(),$alertMessage->getTag(), $alertMessage->getMessage());
}

if($params){
	foreach($params as $param){
		$page->addTagtoComponent($param['component'],$param['tag'], $param['value']);
	}
}

//$firephp->info('Loading page ' . $pageID);
//$firephp->info($session->get('activeuser'));
require_once(INCLUDES . '/common/cache-control.php');
$page->load(); //loads content and parses into template
//printRequest(0,SHOWSESSION);
$page->display();
//$firephp->info('page displayed');