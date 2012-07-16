<?php

/**
 * wrapper around PHPs native error_log function
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Logger
{
	public static function logError($message = ''){
		
		//add datetime
		$message = date("Y-m-d H:i:s") . " - " . $message . "\n";

		switch (Constants::error_type) {
			case 0:
				self::errorLog($message,0);
				break;
			case 1:
				self::errorEmail($message);
				break;
			case 3:
				self::errorLog($message,3);
				break;
			case 4:
				self::errorLog($message,1);
				self::errorEmail($message);
				break;
			case 5:
				self::errorLog($message,0);
				self::errorEmail($message);
				
			default:
		}
	}
	
	private function errorLog($message, $type=0){
		error_log($message,$type, ROOT . Constants::error_file_destination);
	}
	
	private function errorEmail($message){
		error_log($message,1,Constants::error_email_notification);
	}
}

?>