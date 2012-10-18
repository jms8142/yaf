<?php

/**
 * reads/writes to /inc/config.xml
 * @author John Skrzypek
 * @copyright
 * @todo file checking, data validation
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Configuration
{
	private $file;
	private $parentTag;
	private $items = array();
	
	public function __construct($file,$parentTag){
		//check if file exists
		if($file){
			$this->file = $file;
			$this->parentTag = $parentTag;
			$this->parse();
		}
	}
	
	public function __get($id){
		//validation
		return $this->items[$id];
	}
	/**
	 * overload method to update config properties.  only allows changes to preexisting items
	 */
	public function __set($id,$value){
		if(isset($this->items[$id])){
			$this->items[$id] = $value;
		}
	}
	
	private function parse(){
		$doc = new DOMDocument();
		$doc->load($this->file);

		$cn = $doc->getElementsByTagName($this->parentTag);
		$nodes = $cn->item(0)->getElementsByTagName("*");

		foreach($nodes as $node){
			$this->items[$node->nodeName] = $node->nodeValue;
		}	
	}
	
	public function save(){
		
		//compare attributes (to db?) and show warning if some are missing
		
		$doc = new DOMDocument();
		$doc->formatOutput = true;
		
		$r = $doc->createElement("config");
		$doc->appendChild($r);
		
		foreach($this->items as $k => $v){
			$kn = $doc->createElement($k);
			$kn->appendChild($doc->createTextNode($v));
			$r->appendChild($kn);
		}
		
		copy($this->file,$this->file.'.bak');
		
		$doc->save($this->file);
	}

}
?>