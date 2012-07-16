<?php
require_once(CLASSROOT . '/dao/Orderdao.php');
require_once(CLASSROOT . '/core/Overloader.php');

/**
 * Orders
 * @author One Eleven Interactive
 * @copyright
 */

defined('DIRACCESS') or die('Cannot access this directly');

class Order extends Overloader
{
	public function orderLimit($id){
		$order = new Order;
		$filter = new Filter;
		$filter->addNameValuePair('user', $id);
		$filter->sortby('orderDate','DESC');
		$filter->setLimit(1);
	
		$orderList = new Collection('Order',$filter);
		
		if($orderList->count() == 1) {
			$order = $orderList->current();
			
			//get limit
			$rules = xmlToArray::getArray(RULES.'/'.Constants::rules, 'rules-config', 'rules', 'drug');
			$limit = xmlToArray::getNodeDetail($rules, 'Name', 'ASTEPRO', 'ReorderLimit');
	
			Validator::validate($limit,'int',1,30,30);
			
			$lastOrderDate = strtotime($order->getOrderDate());
			$diff = time() - $lastOrderDate;
				
			if($diff <= $limit * 86400)
				return true;
		}
		
		return false;
	}
	
	/**
	 * returns filled out lineitems from order - include brand, ndc, etc 
	 */
	public function getDescriptiveItems($uid){
		if($this->entity){
			$this->wrapper = ObjFactory::getObject(Constants::dbtype);
			$this->subwrapper = ObjFactory::getObject(Constants::dbtype);
			$items = json_decode($this->getItems(),true);
			
			//$this->firephp->info($address->getSLN());
			//load address into the response - fine for prototype
			$user = new User;
			$user->loadById($uid);
			
			foreach($items as &$item){
				//$query_str = "SELECT * FROM samples WHERE uniqueID = '" . $item['id'] . "'";	
				$query_str = "SELECT samples.* , products.name, products.scientific
								FROM samples
								LEFT OUTER JOIN products ON samples.productID = products.productID
								WHERE samples.uniqueID = '" . $item['id'] . "'";			
				//$this->firephp->info($query_str);
				
				$result = $this->wrapper->query($query_str,DBConn::getInstance());

				if($this->wrapper->getNumrows() > 0) {
					$row = $this->wrapper->fetch_assoc_row();
					$item['name'] = $row['name'];
					$item['scientific'] = $row['scientific'];
					$item['description'] = $row['description'];
					$item['dosage'] = $row['dosage'];
					$item['ndc'] = $row['ndc'];
					$item['type'] = $row['type'];
					
					//get full address
					if($item['address']) {
						$qry = "SELECT * from address where id = " . $item['address'];
						$subresult = $this->subwrapper->query($qry,DBConn::getInstance());
						if($this->subwrapper->getNumrows() > 0) {
							$row = $this->subwrapper->fetch_assoc_row();
							$item['address1'] = $row['address1'];
							$item['city'] = $row['city'];
							$item['zip'] = $row['zip'];
							$item['state'] = $row['state'];
						}
					}
					//$this->firephp->info($qry);
				}

			}
			//$this->firephp->info($items);
			return $items;
		}
	}
	
	public function updateFavorites($uid){
		//$this->firephp->info($this->getItems());
		if($this->getItems()){
			$fav = 0;
			$items = json_decode($this->getItems(),true);
			foreach($items as &$item){
			
				if($item['id']){
					$this->wrapper = ObjFactory::getObject(Constants::dbtype);
					$sql = "select count(id) as favorite from favorites where userid = $uid and productID = '". $item['name'] ."'";
					$result = $this->wrapper->query($sql,DBConn::getInstance());
					//$this->firephp->info($sql);
					if($this->wrapper->getNumrows() > 0) {
						$row = $this->wrapper->fetch_assoc_row();
						$fav = ($row['favorite']>0) ? 1 : 0;
					}
					
					
					
				}
				$item['favorite'] = $fav;	
			}
				
			$this->setItems(json_encode($items));
		}
		
		//$this->firephp->info($this->getItems());
	}
	
}

?>