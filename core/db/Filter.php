<?php

defined('DIRACCESS') or die('Cannot access this directly');

/**
 * @todo consolidate getWhere() function
 */

class Filter
{
	private $condition = Array();
	private $sortField;
	private $sortDirection;
	private $limit;
	
	public function addNameValuePair($field,$value,$connector='AND', $matchType = '='){
		$this->condition[$field]['value'] = $value; 
		$this->condition[$field]['matchType'] = $matchType; 
		$this->condition[$field]['connector'] = $connector; 		
	}
	
	public function sortby($field,$direction='ASC'){
		$this->sortField = $field;
		$this->sortDirection = $direction;
	}
	
	public function setLimit($limit){
		$this->limit = $limit;
	}
	
	public function getWhere(){
		$ret = '';

		foreach($this->condition as $condition_item => $field){
			if($ret){
				$format = '%s %s %s %s ';
				$ret .= sprintf($format,$field['connector'],
									$condition_item,
									$field['matchType'],
									$this->makeQuotes($field['value'],$condition_item)
							);
			} else {
				$format = '%s %s %s ';
				$ret .= sprintf($format,
								$condition_item,
								$field['matchType'],
								$this->makeQuotes($field['value'],$condition_item)
							);
			}
			
		}
		
		if($this->sortField)
			$ret .= ' ORDER BY ' . $this->sortField . ' ' . $this->sortDirection;
		
		if($this->limit)
			$ret .= ' LIMIT ' . $this->limit;
		
		return $ret;
	}
	
		/**
	 * temp hardcoded to handle non int keys
	 * @return 
	 * @todo change to table properties accessed from db or mapper xml 
	 */
	private function makeQuotes($val,$keycol){
		if($keycol != 'id')
			$val = "'" . $val . "'";
		return $val;
	}
	
}

?>