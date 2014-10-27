<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
 
class Clients extends AdminUtilsModel {
	
	/**
	 * Initialize Clients
	 */
	public function __construct() {
		parent::__construct();
		Language::loadLang(array("clients"));
	}
	
	/**
	 * Get the Clients data - Duplicated Primary Emails
	 *
	 * @return Record The partially constructed query Record object
	 */

	public function GetDuplicatesEmails($sort_by="email", $order="asc") {	
		$fields = array(
			"id", "client_id", "contact_type", "first_name","last_name", "email", "address1" , "country" ,  "date_added");		
		
		$arr = $this->Record->select($fields)->from("contacts")->
			where("contact_type", "=", "primary")->
			order(array($sort_by=>$order))->fetchAll();

		
		$out = array();
		$i = 0; 
		foreach ($arr as $key => $value){
			if (array_key_exists($arr[$i]->email , $out) ){
				$out[$value->email]["doubles"] = $out[$value->email]["doubles"] + 1 ; 
				$out[$value->email]["clients"][$out[$value->email]["doubles"] + 1]["id"] = $arr[$i]->id ; 
				$out[$value->email]["clients"][$out[$value->email]["doubles"] + 1]["client_id"] = $arr[$i]->client_id ; 
				$out[$value->email]["clients"][$out[$value->email]["doubles"] + 1]["first_name"] = $arr[$i]->first_name ; 
				$out[$value->email]["clients"][$out[$value->email]["doubles"] + 1]["last_name"] = $arr[$i]->last_name ; 
				$out[$value->email]["clients"][$out[$value->email]["doubles"] + 1]["country"] = $arr[$i]->country ; 
				$out[$value->email]["clients"][$out[$value->email]["doubles"] + 1]["address1"] = $arr[$i]->address1 ; 					
				$out[$value->email]["clients"][$out[$value->email]["doubles"] + 1]["date_added"] = $arr[$i]->date_added ; 					
			} else {
				$out[$value->email] = array ("doubles" => 1 , "clients" => array( 1 => array ("id" => $arr[$i]->id  , "client_id" => $arr[$i]->client_id  , "first_name" => $arr[$i]->first_name , "last_name" => $arr[$i]->last_name , "address1" => $arr[$i]->address1 , "date_added" => $arr[$i]->date_added , "country" => $arr[$i]->country ) ) );
			}			
			$i++ ;
		}		
		arsort($out);
		
		foreach ($out as $emails => $count) {
			// print_r($count);
			if ($count["doubles"] < 2 )
				unset($out[$emails]);
		}	
		
		return $out ;
	}


	/**
	 * Get the Clients data - Duplicated Primary Emails
	 *
	 * @return Record The partially constructed query Record object
	 */

	public function GetDuplicatesUsernames($sort_by="username", $order="asc") {	
		$fields = array(
			"id", "username",  "date_added");
		
		$arr = $this->Record->select($fields)->from("users")->
			//where("contact_type", "=", "primary")->
			order(array($sort_by=>$order))->fetchAll();

		
		$out = array();
		$i = 0; 
		foreach ($arr as $key => $value){
			if (array_key_exists($arr[$i]->username , $out) ){
				$out[$value->username]["doubles"] = $out[$value->username]["doubles"] + 1 ; 
				$out[$value->username]["clients"][$out[$value->username]["doubles"] + 1]["id"] = $arr[$i]->id ; 
				$out[$value->username]["clients"][$out[$value->username]["doubles"] + 1]["client_id"] = $arr[$i]->username ; 			
				$out[$value->username]["clients"][$out[$value->username]["doubles"] + 1]["date_added"] = $arr[$i]->date_added ; 					
			} else {
				$out[$value->username] = array ("doubles" => 1 , "clients" => array( 1 => array ("id" => $arr[$i]->id  , "username" => $arr[$i]->username  , "date_added" => $arr[$i]->date_added ) ) );
			}			
			$i++ ;
		}
		
		arsort($out);
		
		foreach ($out as $username => $count) {
			// print_r($count);
			if ($count["doubles"] < 2 )
				unset($out[$username]);
		}		
		
		
		return $out ;
	}	

	
}
?>