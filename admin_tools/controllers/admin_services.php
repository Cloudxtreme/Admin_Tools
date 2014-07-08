<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminServices extends AppController {

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
    }

	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function moveservice() {	
		$this->init();

		$vars = array(
			'plugin_id'=>$this->plugin_id
		);
			
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		// $this->set("vars", $vars);
    }
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function moveinvoice() {	
		$this->init();

		$vars = array(
			'plugin_id'=>$this->plugin_id
		);
			
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		// $this->set("vars", $vars);
    }

	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function orpholanservices() {	
		$this->init();

		$vars = array(
			'plugin_id'=>$this->plugin_id
		);
			
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		// $this->set("vars", $vars);
    }
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function changedates() {	
		$this->init();

		$vars = array(
			'plugin_id'=>$this->plugin_id
		);
			
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		// $this->set("vars", $vars);
    }	
}

?>