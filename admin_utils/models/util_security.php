<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Admin Utils
 * @copyright Copyright (c) 2014, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
 
class UtilSecurity extends AdminUtilsModel {
	
	/**
	 * Initialize UtilSecurity
	 */
	public function __construct() {
		parent::__construct();

		Loader::loadModels($this, array("ModuleManager", "PluginManager",  "Companies"));
		$UtilSecuritySettings = $this->Companies->getSetting($this->company_id , "AdminUtilsPlugin");
		$this->UtilSecuritySettings = unserialize($UtilSecuritySettings->value);

	}
	
	/**
	 * Block/authorize ip 
	 */
	public function ipRestrictions() {

		$this->UtilSecuritySettings['allowed_ips'] = explode("\n", str_replace("\r", "", $this->UtilSecuritySettings['allowed_ips']));	
		$this->UtilSecuritySettings['blocked_ips'] = explode("\n", str_replace("\r", "", $this->UtilSecuritySettings['blocked_ips']));	
		// echo $this->CheckSection() ;
		
		// ensure we are in admin side section
		if ($this->CheckSection() == "admin" ) {
		
			// Redirect a blocked ip to 404 page
			if ($this->UtilSecuritySettings['block_access'] && in_array($_SERVER['REMOTE_ADDR'], $this->UtilSecuritySettings['blocked_ips']) ){			
				$url =  "http://". $_SERVER['HTTP_HOST'] . WEBDIR  . "404/" ; // redirect to 404 error page instead of index page
				header('Location: '. $url );
				exit();		
			}
			
			// Redirect an unauthorized ip to 404 page
			if ($this->UtilSecuritySettings['ip_restriction'] && !in_array($_SERVER['REMOTE_ADDR'], $this->UtilSecuritySettings['allowed_ips']) ){			
				$url =  "http://". $_SERVER['HTTP_HOST'] . WEBDIR  . "404/" ; // redirect to 404 error page instead of index page
				header('Location: '. $url );
				exit();				
			}
		}
		
		// ensure we are in client side section
		// return 403 error code for blocked ip
		if (!$this->CheckSection() == "admin" ) {
			if ($this->UtilSecuritySettings['block_access'] && in_array($_SERVER['REMOTE_ADDR'], $this->UtilSecuritySettings['blocked_ips']) ){			
				header("HTTP/1.1 403 Unauthorized" );
				exit();	
			}
		}	
				
	}

	/**
	 * Block to uninstalled plugins
	 */	
	public function UninstallPlugins() {
		if (strpos($_SERVER['REQUEST_URI'], "plugin")) {		
			if ($this->UtilSecuritySettings['uninstall_plugins'] && !$this->isPluginInstalled() ) {
				$url =  "http://". $_SERVER['HTTP_HOST'] . WEBDIR  . "404/" ; // redirect to 404 error page instead of index page
				header('Location: '. $url );
				exit();
			}
		}
	}	
	
	/**
	 * Stop Forum Spam
	 */
    public function StopSpam() {
	
		if (!empty($_POST) && $this->UtilSecuritySettings['stopforumspam_check'] && $this->CheckSection() == "order"  ) {
		
			if (isset($_POST['action']) &&  $_POST['action'] === "signup" ) { //be sure it's a new signup request			
			
				$args = array(
					'email' => $_POST['email'], 
					'ip' => $_SERVER['REMOTE_ADDR'] , 
					// 'ip' => "93.182.136.173" ,  // this is just for testing 
					'username' => $_POST['first_name'] . " ". $_POST['last_name'] // really i don't see a benifict from checking this data !!!!
				);
				
				$spamcheck = $this->StopForumSpamSheck( $args );

				if ($spamcheck) {
					// header( "refresh:5;url=".$_SERVER['HTTP_REFERER']."" ); 
					echo '
					<!DOCTYPE html>
					<html lang="en">
						<head>
							<meta charset="utf-8">
							<meta http-equiv="X-UA-Compatible" content="IE=edge">
							<meta name="viewport" content="width=device-width, initial-scale=1">
							<title>'. Language::_("AdminToolsPlugin.security.stopforumspam_check.page_title", true ) .'</title>					
							<!-- Latest compiled and minified CSS -->
							<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
							<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
							<!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
							<!--[if lt IE 9]>
							  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
							  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
							<![endif]-->							
						</head>	
						<body>
							<!-- Modal -->
							<div class="modal center-block">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'. Language::_("AdminToolsPlugin.security.buttons.close", true ) .'</span></button>
											<h4 class="modal-title" id="myModalLabel">'. Language::_("AdminToolsPlugin.security.stopforumspam_check.page_title", true ) .'</h4>
										</div>
										<div class="modal-body">
											<div class="alert alert-danger" role="alert">'.Language::_("AdminToolsPlugin.security.stopforumspam_check.!error", true).'</div>
										</div>
										<div class="modal-footer">
										</div>
									</div>
								</div>
							</div>
							
							<!-- jQuery (necessary for Bootstrap JavaScript plugins) -->
							<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>						
							<!-- Latest compiled and minified JavaScript -->
							<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>						
						</body>
					</html>	
					';
					exit();
				}
			}
		}
    }	

	/**
	 * Block registration with duplicated email
	 */
    public function BlockDuplicate() {
		
		if (!empty($_POST) && $this->UtilSecuritySettings['block_duplicate'] && $this->CheckSection() == "order" ) {
			
			// ip blocked for test pupose 93.182.136.173
			if (isset($_POST['action']) &&  $_POST['action'] === "signup" ) { //be sure it's a new signup request			
				
				$duplicated = $this->GetDuplicates($_POST['email']);
				
				// print_r($duplicated) ;
				if ($duplicated) {
				
					if (!isset($this->DataStructure))
						Loader::loadhelpers($this, array("DataStructure"));
						
					$this->ArrayHelper = $this->DataStructure->create("Array");				
					
					$posted_data = null ;					
					$_POST['numbers'] = $this->ArrayHelper->keyToNumeric($_POST['numbers']);
					
					foreach ($_POST as $param_name => $param_val) {					
						if ($param_name == 'numbers' ) {
							for ($i = 0 ; $i < count($_POST['numbers']) ; $i++) {
								$posted_data .= "<input type='hidden' name='numbers[type][$i]' value='".$param_val[$i]['type']."' />\n";
								$posted_data .= "<input type='hidden' name='numbers[location][$i]' value='".$param_val[$i]['location']."' />\n";
								$posted_data .= "<input type='hidden' name='numbers[number][$i]' value='".$param_val[$i]['number']."' />\n";
							}
						} else if ($param_name == 'email' )
							$posted_data .= "<input type='text' name='$param_name' value='$param_val' class='form-control' id='$param_name'  />\n";
						
						else 
							$posted_data .= "<input type='hidden' name='$param_name' value='$param_val' />\n";
					}				
					// header("refresh:5;url=". $_SERVER['HTTP_REFERER'] ."" ); 
					echo '
					<!DOCTYPE html>
					<html lang="en">
						<head>
							<meta charset="utf-8">
							<meta http-equiv="X-UA-Compatible" content="IE=edge">
							<meta name="viewport" content="width=device-width, initial-scale=1">
							<title>'. Language::_("AdminToolsPlugin.security.block_duplicate.page_title", true ) .'</title>					
							<!-- Latest compiled and minified CSS -->
							<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
							<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
							<!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
							<!--[if lt IE 9]>
							  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
							  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
							<![endif]-->							
						</head>	
						<body>
							<!-- Modal -->
							<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'. Language::_("AdminToolsPlugin.security.buttons.close", true ) .'</span></button>
											<h4 class="modal-title" id="myModalLabel">'. Language::_("AdminToolsPlugin.security.block_duplicate.page_title", true ) .'</h4>
										</div>
										<form method="post" role="form">
										<div class="modal-body">
										
											<div class="alert alert-danger" role="alert">'.Language::_("AdminToolsPlugin.security.block_duplicate.!error", true , $_POST['email'] ).'</div>
											
											<div class="form-group has-error has-feedback">
												<label for="exampleInputEmail1">'. Language::_("AdminToolsPlugin.security.block_duplicate.newemail", true ) .'</label>
												'.$posted_data.'
												<span class="glyphicon glyphicon-remove form-control-feedback"></span>
											</div>
											
												
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary">'. Language::_("AdminToolsPlugin.security.buttons.submit", true ) .'</button>
											</form>	
											<form method="post" action="'. WEBDIR . Configure::get("Route.client") . '/login/reset/" style="display: inline-block;" >
												<input type="hidden" name="_csrf_token" value="'. $_POST['_csrf_token'] .'" />
												<input type="hidden" name="login_username" value="'. $_POST['email'] .'" />
												<button type="submit" class="btn btn-warning">'. Language::_("AdminToolsPlugin.security.buttons.resetpass", true ) .'</button>
											</form>												
										</div>
										
									</div>
								</div>
							</div>
							
							<!-- jQuery (necessary for Bootstrap JavaScript plugins) -->
							<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>						
							<!-- Latest compiled and minified JavaScript -->
							<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>						
							<script type="text/javascript">
								$(window).load(function(){
									$(\'#myModal\').modal(\'show\');
								});
							</script>						
						</body>
					</html>						
					';
					exit();
				}
			}
		}
    }	
	
	/**
	 * Check a given IP and email against the stopforumspam Database
	 */	
	public function StopForumSpamSheck($args)	{

		$response = @simplexml_load_file('http://www.stopforumspam.com/api?'.http_build_query($args));
		
		if ($response === false)
			return false;

		foreach ($response->appears as $appears)
			if ($appears == 'yes')
				return true;

		return false;
	}	


	/*** PRIVATE FUNCTIONS ***/
	
	public function GetDuplicates($email) {	
			
		$fields = array("clients.*");
		
		$result = $this->Record->select($fields)->from("clients")->
			innerJoin("client_groups", "clients.client_group_id", "=", "client_groups.id", false)->
			innerJoin("contacts", "contacts.client_id", "=", "clients.id", false)->
			
			where("contacts.email", "=", $email)->
			where("contacts.contact_type", "=", "primary")->
			where("client_groups.company_id", "=", Configure::get("Blesta.company_id"))->	
			fetch();	
		
		return $result ;
	}	

	/**
	 * Determine wich area we are
	 */		
	private function CheckSection() {
		if (strpos($_SERVER['REQUEST_URI'], Configure::get("Route.admin"))) {
			return "admin";
		} else if (strpos($_SERVER['REQUEST_URI'], "order" )) {
			return "order";
		} else 	{
			return "client";
		} 
	}
		
	/**
	 * Get the plugin name from the requested url
	 */		
	private function GetPluginName() {
		$url = $_SERVER['REQUEST_URI'] ;
		$pattern = "@plugin/([^/\?]+)@";		
		preg_match($pattern, $url, $matches);
		return $matches[1] ;
	}
	
	/**
	 * Check If a plugin is installed or not 
	 */	
    private function isPluginInstalled(){
	
        if (!$this->PluginManager->isInstalled($this->GetPluginName() , $this->company_id )) {
            return false;
        }
        return true;
    }		
		
}
?>