<?php

/**
 * signature validation class
 * @author John Skrzypek
 * @copyright
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Validator
{
	const MIN_CHAR = 8;
	const MAX_CHAR = 20;
	const MAX_REPEAT_CHAR = 4;
	
	const MIN_REACHED = "Password must contain at least 8 characters.";
	const MAX_REACHED = "Password must not exceed 20 characters.";
	const NO_UPPER = "Password must contain at least 1 uppercase alphabetic character.";
	const NO_LOWER = "Password must contain at least 1 lowercase alphabetic character.";
	const NO_NUMBER = "Password must contain at least 1 number.";
	const NO_ACCOUNT_NAME = "Password cannot contain the user's account name";
	const REPEATING_CHAR = "Password cannot contain a character that is repeated more than 4 times.";
	
	private $err_mes;
	
	
	public function strongPassword($password_str,$email_str){
		
		if(strlen($password_str) < self::MIN_CHAR){
			$this->err_mes = self::MIN_REACHED;
			return false;
		}
		
		if(strlen($password_str) > self::MAX_CHAR){
			$this->err_mes = self::MAX_REACHED;
			return false;
		}
		
		if(!preg_match('/[A-Z]/',$password_str)){
			$this->err_mes = self::NO_UPPER;
			return false;
		}
		
		if(!preg_match('/[a-z]/',$password_str)){
			$this->err_mes = self::NO_LOWER;
			return false;
		}
		
		if(!preg_match('/[0-9]/',$password_str)){
			$this->err_mes = self::NO_NUMBER;
			return false;
		}
		
		if(preg_match("/".$email_str."/i",$password_str)){
			$this->err_mes = self::NO_ACCOUNT_NAME;
			return false;
		}
		
		if(preg_match('/(.)\1\1\1\1/',$password_str)){
			$this->err_mes = self::REPEATING_CHAR;
			return false;
		}
		
		/**
		 * check previous usage in database
		 */
		
		return true;
	}
	
	public function getError(){
		return $this->err_mes;
	}
	
	public static function validate(&$var,$type,$min=0,$max=100,$default){
		switch ($type) {
			case 'int':
				if(!(is_numeric($var) && $var > $min && $var < $max))
					$var = $default;
				break;
			default:
		}
	}
	
}

?>