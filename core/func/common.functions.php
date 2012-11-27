<?php


function tokenize($val = '1'){
	$hashkey = Constants::hashkey;
	
	$h_str = md5($hashkey);
	$h_str = md5($h_str . $val);
	$h_str = md5($h_str);
	
	return $val . '-' . $h_str;
}

function untokenize($val = '1'){

    if (!preg_match("/^(.+)-(.+)$/", $val, $separatedData))
		return false;
    
    $data = $separatedData[1];
	$check = tokenize($data);

    if (strcmp($check, $val) != 0)
      return false;

    return $data;
	
}

function dateConvert($date_str,$in,$out){
	
	if($date_convert = strtotime($date_str)){
		return date($out,$date_convert);
	}

	return date($out,time());
} 

function init(){
	
	global $session,$firephp,$g_mobile;
	$session = new Session;
	
	if(isset($session) && $session->get('loggedin'))
		$session->set('loggedin', false);

	if($session->get('esig'))
		$session->clear('esig');
	
	
	
	//smarty cache id - for mobile/desktop differentiation
	$templateID = ($g_mobile) ? 'mobile' : 'desktop';
	//$firephp->info('in init() and templateID is ' . $templateID);
	$session->set('templateID',$templateID);
	
	$session->set('init',true);
	
}

/*
 * Delete any user specific info that should not get carried if another user logs in
 */

function killCustomSessions(){
	global $session;
	
	$sessionItems = Array('preloaded',
					  	  'preloadedID',
					  	  'activeuser');
					  
	foreach($sessionItems as $item){
		$session->clear($item);
	}				  
}

function backslashtounderscore($val){
	return str_replace('/','_',$val);
}


?>