<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Admin Utils
 * @copyright Copyright (c) 2014, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminSecurity extends AdminUtilsController {

	/**
	 * Pre Action
	 */
	public function preAction() {
		parent::preAction();
		
        // Require login
        $this->requireLogin(); 
		Language::loadLang("security", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
		
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);
				
		$this->uses(array("Companies")); 
		$UtilSecuritySettings = $this->Companies->getSetting($this->company_id , "AdminUtilsPlugin");
		$this->UtilSecuritySettings = unserialize($UtilSecuritySettings->value);

		$this->Tabs = $this->getTabs($current = "security") ;
		
		$this->NavigationLinks = '
				<div class="links_row">
					<a class="btn_right ip"		href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_security/iprestriction/") .'"><span>'. Language::_("AdminToolsPlugin.security.iprestriction" , true ) .'</span></a>				
					<a class="btn_right url"	href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_security/changeadminurl/") .'"><span>'. Language::_("AdminToolsPlugin.security.changeadminurl", true ) .'</span></a>
					<a class="btn_right tweak"	href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_security/globalsettings/") .'"><span>'. Language::_("AdminToolsPlugin.security.globalsettings", true ) .'</span></a>
				</div>';
				
		$language = Language::_("AdminToolsPlugin.security." . Loader::fromCamelCase($this->action ? "page_title.".  $this->action : "page_title") , true);
		$this->structure->set("page_title", $language);
		
	}     
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {
	
		$this->redirect($this->base_uri . "plugin/admin_utils/admin_security/globalsettings/");

    }

	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function iprestriction() {
	
		$vars = $this->UtilSecuritySettings ;		
		
		if (!empty($this->post)) {
			
			if(!isset($this->post['allowed_ips']))
				unset($this->UtilSecuritySettings['allowed_ips']);
			
			if(!isset($this->post['blocked_ips']))
				unset($this->UtilSecuritySettings['blocked_ips']);

			if(!isset($this->post['ip_restriction']))
				unset($this->UtilSecuritySettings['ip_restriction']);

			if(!isset($this->post['block_access']))
				unset($this->UtilSecuritySettings['block_access']);				
			
			$result = array_merge($this->UtilSecuritySettings , $this->post);			
			
			// print_r($this->post);
			// print_r($this->UtilSecuritySettings);			
			// print_r($result);
			
			$this->Companies->setSetting($this->company_id , "AdminUtilsPlugin", serialize($result));
			$this->setMessage("success", Language::_("AdminToolsPlugin.security.!success.allowededip_saved", true) , false, null, false);	
			
			$vars = (array)$this->post ;
			
		}
		
		$this->set("vars", $vars );		
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
    }
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function globalsettings() {

		$vars = $this->UtilSecuritySettings ;
		
		if (!empty($this->post)) {
		
			if(!isset($this->post['stopforumspam_check']))
				unset($this->UtilSecuritySettings['stopforumspam_check']);
			
			if(!isset($this->post['uninstall_plugins']))
				unset($this->UtilSecuritySettings['uninstall_plugins']);

			if(!isset($this->post['block_duplicate']))
				unset($this->UtilSecuritySettings['block_duplicate']);

			if(!isset($this->post['route_admin']))
				unset($this->UtilSecuritySettings['route_admin']);				

			$result = array_merge($this->UtilSecuritySettings , $this->post);
				
			$this->Companies->setSetting($this->company_id , "AdminUtilsPlugin",serialize($result));
			$this->setMessage("success", Language::_("AdminToolsPlugin.security.!success.globalsettings_saved", true) , false, null, false);

			$vars = (array)$this->post ;
		
		}
		
		$this->set("vars", $vars );		
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
    }	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function changeadminurl() {		
	
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
    }	

}

?>