<?php
/**
 * Resource locations
 * @author John Skrzypek
 * @copyright
 */

defined('DIRACCESS') or die('Cannot access this directly');

if(!defined(ROOT))
	define('ROOT', dirname(__FILE__));

define('CLASSROOT', ROOT . '/core');
define('CONFIG', ROOT . '/core/config');
define('FUNC', ROOT . '/core/func');
define('UTILITIES', ROOT . '/lib');
define('TEMPLATEROOT', ROOT . '/html');
define('MODULES', ROOT . '/modules');
define('RULES', ROOT . '/rules');


//Debugging
ini_set('display_errors','on');
error_reporting(E_ALL ^ E_NOTICE);
//error_reporting(E_ALL);
?>