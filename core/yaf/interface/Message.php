<?php

/**
 * Message handler for all transaction
 */

class Message
{
	private $componentName;
	private $tag;
	private $message;
	
	const GOOD = 'SUCCESS';
	const BAD = 'ERROR';
	const STATUS = 'STATUS';
	
	public function __construct($componentName='',$tag=''){
		$this->componentName = $componentName;
		$this->tag = $tag;
	}
	
	public function addMessage($mes,$type = ''){
		$this->message = $mes;
		
		if($type)
			$this->setType($type);
	}
	
	public function setType($statictype = self::GOOD){
		$this->message = "<div class='message-delivery'><span class='$statictype'>" . $this->message . "</span></div>";
	}
	
	public function getComponentName(){
		return $this->componentName;
	}
	
	public function getTag(){
		return $this->tag;
	}
	
	public function getMessage(){
		return $this->message;
	}
	
	public function __toString() {
		return $this->message;
	}
	
}

?>