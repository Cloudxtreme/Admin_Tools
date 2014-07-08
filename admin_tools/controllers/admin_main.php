<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminMain extends AppController {

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
		// $this->view->setView(null, "emptycache.default");
		
		$this->staff_id = $this->Session->read("blesta_staff_id");
	
    }
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {	
		$this->init();
		// $vars = array();
		$cache = Cache::fetchCache( "nav_staff_group_" . $this->staff_id, $this->company_id . DS . "nav" . DS ) ; 
		$vars = array(
			'plugin_id'=>$this->plugin_id,
			'fetchCache'=>Cache::fetchCache( "nav_staff_group_" . $this->staff_id, $this->company_id . DS . "nav" . DS )
		);
			
		if (!empty($this->post)) {
			// Cache::clearCache("nav_staff_group_" . $this->staff_id, $this->company_id . DS . "nav" . DS);
			
			if ((!Cache::clearCache("nav_staff_group_" . $this->staff_id, $this->company_id . DS . "nav" . DS))) {
				// Error
				$this->setMessage("error", Language::_("AdminToolsPlugin.emptycache.!error", true));				
			}
			else {
				// Success
				$this->flashmessage("message", Language::_("AdminToolsPlugin.emptycache.!success", true));
				$this->redirect($this->base_uri . "plugin/admin_tools/admin_main/");
			}
		}			
			
		// Set the view to render for all actions under this controller
		$this->view->setView(null, "AdminTools.default");
		$this->set("cache", $cache);
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.emptycache.page_title", true));
		return $this->partial("admin_main", $vars);

    }

}

?>