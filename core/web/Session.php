<?php

/**
 * Session/Request/Cookie wrapper
 * @author John Skrzypek
 * @copyright
 * @todo 
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Session
{
	public function isValid($key){
		return array_key_exists($key,$_SESSION);
	}
	
	public function __construct(){
		if(!isset($_SESSION)){
			if(headers_sent()){
				Logger::logError('Header block has already been sent.  You need to start the session object before that'); 
			} else {
				session_start();
			}
		}
	}
	
	public function get($key){
		if(isset($_SESSION))
			return (array_key_exists($key, $_SESSION)) ? $_SESSION[$key] : null;
		else
			return null;
	}
	
	public function set($key,$value){
		$_SESSION[$key] = $value;
	}

	public function clear($key){		
		unset($_SESSION[$key]);
	}
	
	public function kill(){
		$_SESSION = array();
		
		if(isset($_COOKIE[session_name()])){
			setcookie(session_name(), '', time()-42000, '/');
		}
		
		session_destroy();
	}
}


?>