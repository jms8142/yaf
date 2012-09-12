<?php

/**
 * returns associative array from xml
 * @author John Skrzypek
 * @copyright
 */

defined('DIRACCESS') or die('Cannot access this directly');

class xmlToArray
{
    /**
     * Optimization Enabled / Disabled
     *
     * @var bool
     */
		
    protected $bOptimize = false;
    protected $firephp; //debug
    
	public function __construct(){
		$this->firephp = FirePHP::getInstance(true);
	}
	
	public function getArray($xmlFile,$root,$topLevel,$child){
		
		$ret = Array();
		
		try {
			$xmlMap = new xmlToArray;
			$mapArray = $xmlMap->parseFile($xmlFile);
			
			if(is_array($mapArray)){
				if($topLevel && $child){
					$ret = $mapArray[$root][$topLevel][$child];
				} else {
					$ret = $mapArray;
				}
			}	
		} catch (Exception $e) {
            echo $e->getMessage(). ' | Try open file: '. $xmlFile;
		}
		
		self::lastArraytoString($ret); //bump up bottom level arrays to strings
		
		return $ret;
			
	}
	
	
	private function lastArraytoString(&$arr,&$parent='') {
		if(is_array($arr)){
			foreach($arr as &$arr_item){
				if(self::lastArraytoString($arr_item,$arr)){
					$arr = $arr_item;
					return;
				}
			}
		} else {
			return 1;
		}
	}
	
	/**
	 * returns specific item in multidimensional array returned by this object.
	 * can be replaced soon by valobj
	 * @return 
	 * @param object $Arrayobj
	 * @param object $searchKey
	 * @param object $searchValue
	 * @param object $returnKey
	 */
	
	public function getNodeDetail($Arrayobj,$searchKey,$searchValue,$returnKey){
		if(is_array($Arrayobj)){
			if(count($Arrayobj) > 1 && is_array($Arrayobj[0])) {  //multi dim array
				foreach($Arrayobj as $array){
					if(array_key_exists($searchKey,$array) && $array[$searchKey] == $searchValue){
						return $array[$returnKey];
					}
				}	
			} else {
				if(array_key_exists($searchKey,$Arrayobj) && $Arrayobj[$searchKey] == $searchValue){
					return $Arrayobj[$returnKey];
				}	
			}
			
		}
		return '';
	}
	
    /**
     * Method for loading XML Data from String
     *
     * @param string $sXml
     * @param bool $bOptimize
     */	

    public function parseString( $sXml , $bOptimize = false) {
        //$oXml = new XMLReader();
		$oXml = new XMLReader;
        $this -> bOptimize = (bool) $bOptimize;
        try {

            // Set String Containing XML data
            $oXml->XML($sXml);

            // Parse Xml and return result
            return $this->parseXml($oXml);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Method for loading Xml Data from file
     *
     * @param string $sXmlFilePath     
     * @param bool $bOptimize
     */
    public function parseFile( $sXmlFilePath, $bOptimize = false ) {
		
        $oXml = new XMLReader();
        $this -> bOptimize = (bool) $bOptimize;
        
        if(!$oXml->open($sXmlFilePath))
        	throw new oeiSampleServerException("XML File not found", 101);
        else
        	return  $this->parseXml($oXml);
        
     
    }
	
   /**
     * XML Parser
     *
     * @param XMLReader $oXml
     * @return array
     */
    protected function parseXml( XMLReader $oXml ) {

        $aAssocXML = null;
        $iDc = -1;

        while($oXml->read()){
            switch ($oXml->nodeType) {

                case XMLReader::END_ELEMENT:

                    if ($this->bOptimize) {
                        $this->optXml($aAssocXML);
                    }
                    return $aAssocXML;

                case XMLReader::ELEMENT:

                    if(!isset($aAssocXML[$oXml->name])) {
                        if($oXml->hasAttributes) {
                            $aAssocXML[$oXml->name][] = $oXml->isEmptyElement ? '' : $this->parseXML($oXml);
                        } else {
                            if($oXml->isEmptyElement) {
                                $aAssocXML[$oXml->name] = '';
                            } else {
                                $aAssocXML[$oXml->name] = $this->parseXML($oXml);
                            }
                        }
                    } elseif (is_array($aAssocXML[$oXml->name])) {
                        if (!isset($aAssocXML[$oXml->name][0]))
                        {
                            $temp = $aAssocXML[$oXml->name];
                            foreach ($temp as $sKey=>$sValue)
                            unset($aAssocXML[$oXml->name][$sKey]);
                            $aAssocXML[$oXml->name][] = $temp;
                        }

                        if($oXml->hasAttributes) {
                            $aAssocXML[$oXml->name][] = $oXml->isEmptyElement ? '' : $this->parseXML($oXml);
                        } else {
                            if($oXml->isEmptyElement) {
                                $aAssocXML[$oXml->name][] = '';
                            } else {
                                $aAssocXML[$oXml->name][] = $this->parseXML($oXml);
                            }
                        }
                    } else {
                        $mOldVar = $aAssocXML[$oXml->name];
                        $aAssocXML[$oXml->name] = array($mOldVar);
                        if($oXml->hasAttributes) {
                            $aAssocXML[$oXml->name][] = $oXml->isEmptyElement ? '' : $this->parseXML($oXml);
                        } else {
                            if($oXml->isEmptyElement) {
                                $aAssocXML[$oXml->name][] = '';
                            } else {
                                $aAssocXML[$oXml->name][] = $this->parseXML($oXml);
                            }
                        }
                    }

                    if($oXml->hasAttributes) {
                        $mElement =& $aAssocXML[$oXml->name][count($aAssocXML[$oXml->name]) - 1];
                        while($oXml->moveToNextAttribute()) {
                            $mElement[$oXml->name] = $oXml->value;
                        }
                    }
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:

                    $aAssocXML[++$iDc] = $oXml->value;

            }
        }

        return $aAssocXML;
    }

	
   /**
     * Method to optimize assoc tree.
     * ( Deleting 0 index when element
     *  have one attribute / value )
     *
     * @param array $mData
     */
    
	
	public function optXml(&$mData) {
        if (is_array($mData)) {
            if (isset($mData[0]) && count($mData) == 1 ) {
                $mData = $mData[0];
                if (is_array($mData)) {
                    foreach ($mData as &$aSub) {
                        $this->optXml($aSub);
                    }
                }
            } else {
                foreach ($mData as &$aSub) {
                    $this->optXml($aSub);
                }
            }
        }
    }	
    
}