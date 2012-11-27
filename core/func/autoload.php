<?php
/**
 * functions to handle dynamic loading of classes
 * @author John Skrzypek
 * @copyright
 * @todo review
 */

defined('DIRACCESS') or die('Cannot access this directly');

function __autoload($className){
		$folder = classFolder($className);

		if(!file_exists($folder.$className.".php")) {
			throw new oeiSampleServerException(oeiSampleServerException::NOCLASS . ' : ' . $className);
		}

		require_once($folder.$className.".php");
	
}

function classFolder($className, $sub = "/"){

	$dir = dir(CLASSROOT.$sub);

	if(file_exists(CLASSROOT.$sub.$className.".php"))
		return CLASSROOT.$sub;
	
	while(false !== ($folder = $dir->read())){
		if($folder != "." && $folder != ".."){
			if(is_dir(CLASSROOT.$sub.$folder)){
				$subFolder = classFolder($className, $sub.$folder."/");
			
				if($subFolder)
					return $subFolder;
			}
		}
	}	
	
	$dir->close();
	return false;
}

?>