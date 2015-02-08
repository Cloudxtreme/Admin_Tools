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
		$this->uses(array("admin_utils.UtilClients")); // Call Clients Model Inside admin_utils 
		
		$this->Tabs = $this->getTabs($current = "clients") ;
		
		$this->NavigationLinks = '
				<div class="links_row">
					<a class="btn_right email" href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_clients/emails/").'"><span>'. Language::_("AdminToolsPlugin.clients.duplicate_emails", true) .'</span></a>
					<a class="btn_right username" href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_clients/usernames/") .'"><span>'. Language::_("AdminToolsPlugin.clients.duplicate_usernames", true) .'</span></a>
				</div>';
				
		$language = Language::_("AdminToolsPlugin.clients." . Loader::fromCamelCase($this->action ? "page_title.".  $this->action : "page_title") , true);
		$this->structure->set("page_title", $language);
		
	}    
	

	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {
			
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
    }

	
	/**
	 * Returns Duplicated emails
	 */
    public function emails() {
	
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
		$this->set("duplicates", $this->UtilClients->GetDuplicatesEmails());
    }
		

	/**
	 * Returns Duplicated usernames
	 */
    public function usernames() {
		
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);			
		$this->set("duplicates", $this->UtilClients->GetDuplicatesUsernames());		
    }
	
	/**
	 * Returns Duplicated emails
	 */
    public function emailsinfo() {

		$this->view->setView(null, "AdminUtils.default");

		echo $this->outputAsJson($this->view->fetch("admin_clients_emailsinfo"));
		return false;		
    }		
	
}

?>