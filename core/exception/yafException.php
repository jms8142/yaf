<?php

/**
 * @author One Elven Interactive
 * @copyright
 */

defined('DIRACCESS') or die('Cannot access this directly');

class yafException extends Exception
{
	/*
	 * Database
	 */
	const DBCONN = 'Unable to connect to database';
	const QUERY = 'Unable to execute query';
	const SELECTDB = 'Unable to select database';
	const SAVERECORD = 'Unable to save record';
	const NOTFOUND = 'Record Not found';	
	const PRODUCTNOTFOUND = 'Product Not found';
	const USERNOTFOUND = 'User Not found';	
	const NORECORD = 'Cannot save empty record';
	const VALIDATE = 'Could not validate';	
	const PAGENOTFOUND = 'Page Not found';	
	const ORDERNOTFOUND = 'Order Not found';		
	
	/*
	 * Internal
	 */
	const NOCLASS = 'Class does not exist';
	
	/*
	 * Config FILES
	 */
	const CONFIG_XML = 'Unable to find configuration file';
	
	/*
	 * Error levels
	 */
	
	const FATAL = 1;
	const WARNING = 2;
	
	public function __toString(){
		if($this->code === FATAL) {
			//warning
			print "<b><font color='#FF0000'>Fatal Error</font></b> - {$this->message}<br/>";		
			//halt program
			die();	
		} elseif($this->code === WARNING) {
			//fatal
			return "<b>Warning</b> - {$this->message}<br/>";
		}
		return __CLASS__ . ": [Code: {$this->code} '{$this->message}']\r\n";
	}
}



?>