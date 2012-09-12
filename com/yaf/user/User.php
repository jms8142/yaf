<?php
/**
 * database error catching
 */


require_once(CLASSROOT . '/core/Overloader.php');

defined('DIRACCESS') or die('Cannot access this directly');

class User extends Overloader
{
	private $errorMessage;
	const LOGON = "logon";
	const LOGOFF = "logoff";
	const UPDATESTATUS = "update status";
	const LOG_HISTORY_MAX = 2;
	const STATUS_HISTORY_MAX = 5;
	
	protected $firephp; //debug

	public function __construct(){
		$this->firephp = FirePHP::getInstance(true);
	}
	/**
	 * sets off email and increments password access amount
	 * @return 
	 */
	public function forgotPassword($email){
		if($email){
			//get new user accessor
			//retrieve password information
			//fire off email
		}
	}
	
	public function updatePassword($password){
		$sql = "select count(password) total from password_history where password = '$password' and userId = " . $this->getId();

		$wrapper = ObjFactory::getObject(Constants::dbtype);
		
		$wrapper->query($sql,DBConn::getInstance());
		$row = $wrapper->fetch_assoc_row();
		
		//print $sql . '<br>';
		
		if($row['total']>0){
			$this->errorMessage = '<span class=oei_bad>You may have used this password recently.  Please choose another.</span>';
			return false;
		} else { //not in the 10
			$sql = "select count(password) total from password_history where userId = " . $this->getId();
			
			$wrapper->query($sql,DBConn::getInstance());
			$row = $wrapper->fetch_assoc_row();
			
			if($row['total']>9){ //too many - keep history to 10 (after new insert)
				//get oldest id and delete rest
				$sql = "select id from password_history where userId = " . $this->getId() . " Order by timeStamp DESC LIMIT 10";
				//print 'finding over 9: ' . $sql . '<br/>';
				$wrapper->query($sql,DBConn::getInstance());

				
				//move to end of group
				$wrapper->data_seek($wrapper->getNumrows()-1);
				$row = $wrapper->fetch_assoc_row();	
				
				//oldest entry
				$oldest = $row['id'];
				//print 'found oldest ' . $oldest;
				
				$sql = "delete from password_history where userId = " . $this->getId() . " and id <= " . $oldest;
				//print 'deleting: ' . $sql . '<br>';
				
				$wrapper->query($sql,DBConn::getInstance());
				//should now have 9
			}
			
			$sql = "update users set password = '" . $password . "', modifyDate = Now() where id = " . $this->getId();
			$wrapper->query($sql,DBConn::getInstance());
			
			//print 'updating users: ' . $sql  . '<br>';
			
			$sql = "insert into password_history (userId,password,timeStamp) values (".$this->getId().",'".$password."',Now())";
			$wrapper->query($sql,DBConn::getInstance());
			
			//print 'inserting ' . $sql . '<br>';
		}
		
		return true;
	}
	
	public function updateLog($action,$status=''){
		
		
		$maxcount = ($action == User::LOGON || $action == User::LOGOFF) ? User::LOG_HISTORY_MAX : User::STATUS_HISTORY_MAX;
		
		$wrapper = ObjFactory::getObject(Constants::dbtype);
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		//check for preexisting entry
	
		$sql = "insert into users_history (userid,ip,action,status) values (".$this->getId().", '$ip','$action','$status')";
		$wrapper->query($sql,DBConn::getInstance());
		//$this->firephp->info($sql);
		
		//maintain history count size
		$sql = "SELECT * from users_history where userid = " . $this->getId() . " and action = '$action'";
		$wrapper->query($sql,DBConn::getInstance());
		//$this->firephp->info($sql);
		$numRows = $wrapper->getNumrows();
		if($numRows > $maxcount){	
			$sql = "DELETE from users_history where action = '$action' and userid = " .  $this->getId() . " order by timestamp ASC limit " . ($numRows - $maxcount);	
			//$this->firephp->info($sql);
			$wrapper->query($sql,DBConn::getInstance());
		}
		

	}
	
	public function getAddresses(){
			$wrapper = ObjFactory::getObject(Constants::dbtype);
			$query_str = "select SLN,DEA from users where id = " . $this->getId();
			//$this->firephp->info($query_str);
			
			$result = $wrapper->query($query_str,DBConn::getInstance());
			if($wrapper->getNumrows() > 0) {
				$row = $wrapper->fetch_assoc_row();
				$registration['DEA'] = $row['DEA'];
				$registration['SLN'] = $row['SLN'];
			}

			foreach($registration as $key => $val){
				$query_str = "SELECT * FROM address WHERE " . $key . " = '" . $val . "'";
				$result = $wrapper->query($query_str,DBConn::getInstance());
				if($wrapper->getNumrows() > 0) {
					$addresses[] = $wrapper->fetch_assoc_row();
				}
				
			}
				
			
			//$this->firephp->info($addresses);
			
		return $addresses;
	}
	
	public function getErrorMessage(){
		return $this->errorMessage;
	}
	
	public function setTotalFavorites(){
		$wrapper = ObjFactory::getObject(Constants::dbtype);
		$sql = "select count(id) as totalFavorites from favorites where userid = " . $this->getId();
		$result = $wrapper->query($sql,DBConn::getInstance());
		$row = $wrapper->fetch_assoc_row();
		//$this->firephp->info('session totalFavorites to ' . $row['totalFavorites']);
		$this->setFavorites($row['totalFavorites']);
	}

}

?>