<?php 
error_reporting(E_ALL ^ E_NOTICE);	

define('DIRACCESS',1);
if(!defined(ROOT))
	define('ROOT', dirname(dirname(__FILE__)));

require_once(ROOT . '/lib/FirePHPCore/FirePHP.class.php');
require_once(ROOT . '/core/config/definitions.php');
require_once(ROOT . '/core/func/autoload.php');
require_once(FUNC . '/common.functions.php');
require_once('lib/simpletest/autorun.php');