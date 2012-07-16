<?php

/**
 * handler for GET, POST, SERVER, COOKIE
 * @todo check for lagging GETs.  maybe clear out autofill before running - test on production
 * 
 */
defined('DIRACCESS') or die('Cannot access this directly');

class Request
{
	private $request;
	
	public function get($key, $type, $pref=0){
		if(array_key_exists($key, $this->request[$type]))
			return $this->request[$type][$key];
	}
	
	/**
	 * checks for keys in GET then POST.  send 1 for $pref to reverse search order;
	 * @return 
	 * @param object $key
	 * @param object $pref[optional]
	 */
	
	public function getRequest($key,$pref=0){
		$types = array('POST','GET');
		if($pref)
			rsort($types);

		foreach($types as $type){
			if(array_key_exists($type,$this->request)){  //look through GETS and POSTS
				if(array_key_exists($key,$this->request[$type])){
					return $this->request[$type][$key];
				}
			}

		}
	}
	
	public function set($type,$key,$val){
		$this->request[$type][$key] = $val;
	}
	
	public function autoFill(){
		$ret = new Request;
		$types = array('POST' => $_POST, 'GET' => $_GET, 'SERVER' => $_SERVER, 'COOKIE' => $_COOKIE);
		
		foreach($types as $type => $items){
			foreach($items as $key => $val) {
				$ret->set($type,$key,$val);
			}
		}

		return $ret;
	}
	

}

?>