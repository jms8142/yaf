<?php
/*
 * This needs to be moved elsewhere
 */

class Constants
{

	//database
	const dbtype = 'mysql';

	//error handling

	/**
	 * @var $error_type
	 * 0 to the system logger
	 * 1 to $error_email_notification
	 * 3 to $error_file_destination
	 * 4 to both $error_file destination and $error_email_notification
	 * 5 to both system logger and $error_email_notification
	 */
	const error_logging = true;
	const error_type = 3;
	const error_file_destination = '/log';
	const error_file_name = 'error_log';
	const error_email_notification = 'jms8142@gmail.com';
	
	//included xml file names
	const config = 'config.xml';
	const dataMapper = 'domainMapper.xml';
	const templateInfo = 'templates.xml';
	const pageInfo = 'pages.xml'; //temporary - for DB	
	const rules = 'rules.xml';
	const steps = 'steps.xml';	
	
	//admin settings
	const hashkey = 'Samp111server';
	
	//datetime
	const DATETIMEFORMAT = 'm/d/Y';
}


?>