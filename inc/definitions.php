<?php
/**
 * Resource locations
 * @author One Eleven Interactive
 * @copyright
 */

defined('DIRACCESS') or die('Cannot access this directly');

if(!defined(ROOT))
	define('ROOT', dirname(__FILE__));
/*
these should be in db or xml file

define('DOMAIN','http://qa.samplestogether.com');
define('WEBROOT', ''); */
define('CLASSROOT', ROOT . '/com/classes');
define('INCLUDES', ROOT . '/inc');
define('UTILITIES', ROOT . '/util');
define('TEMPLATEROOT', ROOT . '/html');
define('MODULES', ROOT . '/modules');
define('RULES', ROOT . '/rules');


//Debugging
ini_set('display_errors','On');
error_reporting(E_ALL ^ E_NOTICE);
//error_reporting(E_ALL);
?>