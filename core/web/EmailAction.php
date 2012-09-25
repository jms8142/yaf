<?php

defined('DIRACCESS') or die('Cannot access this directly');
require_once(UTILITIES . '/phpmailer/class.phpmailer.php');
require_once(UTILITIES . '/phpmailer/phpmailer.lang-en.php');

class EmailAction extends Action
{
	private $senderEmail;
	private $senderName;
	private $message;
	private $subject;
	private $recipient = array();
	
	public function __construct(){
		$this->senderEmail = 'processing@samplestogether.com';
		$this->senderName = 'SamplesTogether';
		$this->firephp = FirePHP::getInstance(true);
	}

	public function setSenderEmail($senderEmail = 'admin@admin.com') {
		$this->senderEmail = $senderEmail;
	}
	
	public function setSenderName($senderName = 'admin') {
		$this->senderName = $senderName;
	}	

	public function setRecipient($recipient) {
		$this->addRecipient($recipient);
	}
	
	public function addRecipient($recipient) {
		array_push($this->recipient,$recipient);
	}	

	public function setSubject($subject_text) {
		$this->subject = $subject_text;
	}
	
	public function setMessage($message_text){
		$this->message = $message_text;
	}
	
	public function processMail() {
		return $this->send();
	}
	
	private function send() {
	
		$mail = new PHPMailer();
		$mail->IsSMTP();
		
		$mail->From = $this->senderEmail;
		$mail->FromName = $this->senderName;
		if(is_array($this->recipient)) { //check for multiple emails
			foreach($this->recipient as $recipient) {
				$mail->AddAddress($recipient);		
			}
		} else {
			$mail->AddAddress($this->recipient);
		}
		$mail->WordWrap = 50; 
		$mail->IsHTML(true);                            // set email format to HTML
		$mail->Subject = $this->subject;
		$mail->Body    = stripslashes($this->message);
		$mail->AltBody = stripslashes(strip_tags($this->message));
		//$this->firephp->info(stripslashes($this->message));
		if(!$mail->Send()){
   			//$this->firephp->info("Mailer Error: " . $mail->ErrorInfo);
			return false;
		}
		
		return true;
	}
	
}

?>