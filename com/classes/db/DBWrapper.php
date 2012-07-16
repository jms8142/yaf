<?php
defined('DIRACCESS') or die('Cannot access this directly');

interface DBWrapper
{
	public function getNumrows();
	public function connect($host,$user,$password);
	public function select_db($database,$link);	
	public function query($query_str,$link);	
	public function fetch_assoc_row();
	public function insert_id();
	public function getResult();
}

?>