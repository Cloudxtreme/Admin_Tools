<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminClients extends AppController {

    /**
     * Performs necessary initialization
     */
    private function init() {
        // Require login
        $this->requireLogin();

        Language::loadLang("admin_tools", null, PLUGINDIR . "admin_tools" . DS . "language" . DS);
		
        // Set the plugin ID
        $this->plugin_id = (isset($this->get[0]) ? $this->get[0] : null);

        // Set the company ID
        $this->company_id = Configure::get("Blesta.company_id");

		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->structure->view);
		$this->view->setView(null, "AdminTools.default");
		
		$this->staff_id = $this->Session->read("blesta_staff_id");	
		$this->uses(array("users","Contacts"));
		$this->uses(array("admin_tools.Clients")); // need to check this one !!!!
    }
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {	
		$this->init();

		$vars = array(
			'plugin_id'=>$this->plugin_id
		);
			
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		$this->set("vars", $vars);
		//$this->set("total_notes", $this->Notes->getNoteListCount());
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.clients.page_title", true));		
    }

	
	/**
	 * Returns Duplicated emails
	 */
    public function emails() {
		$this->uses(array("Services"));
		$this->init();
		
		// Set the view to render for all actions under this controller		
		
		// echo $this->Services->getStatusCount($client_id, $status);
		$this->view->setView(null, "AdminTools.default");
		$this->set("duplicates", $this->Clients->GetDuplicatesEmails());
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.clients.page_title", true));		
    }
	
	/**
	 * Returns Duplicated emails
	 */
    public function emailsinfo() {
		$this->uses(array("Services"));
		$this->init();
		
		// Set the view to render for all actions under this controller		
		
		// echo $this->Services->getStatusCount($client_id, $status);
		$this->view->setView(null, "AdminTools.default");
		// $this->set("duplicates", $this->Clients->GetDuplicatesEmails());
		// $this->structure->set("page_title", Language::_("AdminToolsPlugin.clients.page_title", true));
		echo $this->outputAsJson($this->view->fetch("admin_clients_emailsinfo"));
		return false;		
    }		

	/**
	 * Returns Duplicated usernames
	 */
    public function usernames() {
		$this->init();
		
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		$this->set("duplicates", $this->Clients->GetDuplicatesUsernames());
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.clients.page_title", true));		
    }
	
}

?>