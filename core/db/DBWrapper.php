<?php
defined('DIRACCESS') or die('Cannot access this directly');

interface DBWrapper
{
	public function getNumrows();
	public function connect($host,$user,$password,$db);
	public function query($query_str);	
	public function fetch_assoc_row();
	public function insert_id();
	public function getResult();
}

?>