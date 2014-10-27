<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminClients extends AdminUtilsController {

	/**
	 * Pre Action
	 */
	public function preAction() {
		parent::preAction();
		
        // Require login
        $this->requireLogin(); 
		Language::loadLang("clients", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
		
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);
		
		$this->uses(array("users","Contacts"));
		$this->uses(array("admin_utils.Clients")); // Call Clients Model Inside admin_utils 
		
		$this->Tabs = $this->getTabs($current = "clients") ;
		
		$this->NavigationLinks = '
				<div class="links_row">
					<a class="btn_right email" href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_clients/emails/").'"><span>'. Language::_("AdminToolsPlugin.clients.duplicate_emails", true) .'</span></a>
					<a class="btn_right username" href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_clients/usernames/") .'"><span>'. Language::_("AdminToolsPlugin.clients.duplicate_usernames", true) .'</span></a>
				</div>';
	}    
	

	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {	

		// $vars = array();
			
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.clients.page_title", true));		
    }

	
	/**
	 * Returns Duplicated emails
	 */
    public function emails() {
	
		// $this->uses(array("Services"));
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
		$this->set("duplicates", $this->Clients->GetDuplicatesEmails());
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.clients.page_title.emails", true));		
    }
		

	/**
	 * Returns Duplicated usernames
	 */
    public function usernames() {
		
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);			
		$this->set("duplicates", $this->Clients->GetDuplicatesUsernames());
		
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.clients.page_title.usernames", true));		
    }
	
	/**
	 * Returns Duplicated emails
	 */
    public function emailsinfo() {
	
		// $this->uses(array("Services"));

		$this->view->setView(null, "AdminUtils.default");

		echo $this->outputAsJson($this->view->fetch("admin_clients_emailsinfo"));
		return false;		
    }		
	
}

?>