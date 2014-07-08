<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminCountries extends AppController {

    /**
     * Performs necessary initialization
     */
    private function init() {
        // Require login
        $this->requireLogin();

        Language::loadLang("admin_tools", null, PLUGINDIR . "admin_tools" . DS . "language" . DS);
		$this->uses(array("Countries", "States"));
		
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
		$this->set("countries", $this->Countries->getList());
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.countries.page_title", true));
    }

	/**
	 * Add Country
	 */
    public function add() {
		$this->init();

		$vars = null;
		
		if (!empty($this->post)) {
			$vars = (object)$this->post;

			$this->Countries->add($this->post) ;
			
			if (!($errors =  $this->Countries->errors())) {		
				$this->flashMessage("message", Language::_("AdminToolsPlugin.countries.add.!success", true), null, false);
				$this->redirect($this->base_uri . "plugin/admin_tools/admin_countries/edit/" . $this->post['alpha2']);			
			}			
			$this->setMessage("error", $errors, false, null, false);
			$this->set("countries", $vars);
		}
		
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.countries.add.page_title", true));
		//return $this->partial("admin_countries_add", $vars);

    }
	
	/**
	 * Edit Country
	 */
    public function edit() {
		$this->init();
		$vars = $this->Countries->get($this->get[0]) ;
		// $vars = (object)$this->post;
		if (!empty($this->post)) {
			
			//print_r( $this->post );
			$this->Countries->edit($this->post['alpha2'] , $this->post) ;
			
			if (!($errors =  $this->Countries->errors())) {		
				$this->flashMessage("message", Language::_("AdminToolsPlugin.countries.edit.!success", true), null, false);
				$this->redirect($this->base_uri . "plugin/admin_tools/admin_countries/edit/" . $this->get[0] );			
			}
			$vars = (object)$this->post;
			// $vars->alpha2 = $this->get[0] ; 
			$this->setMessage("error", $errors, false, null, false);
			$this->set("admin_countries_add", $vars);
		}
		
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		$this->set("countries", $vars);
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.countries.edit.page_title", true));
		//return $this->partial("countries", $vars);

    }	
	
	/**
	 * Edit Country
	 */
    public function delete() {
		$this->init();
		// print_r( $this->post ) ; 
		if (isset($this->post['alpha2'])) {
	
			$this->Countries->delete($this->post['alpha2']) ;
			
			if (($errors = $this->Countries->errors()))
				$this->setMessage("error", $errors, false, null, false);
			else
				$this->flashMessage("message", Language::_("AdminToolsPlugin.countries.delete.!success", true), null, false);
		}
		
		$this->redirect($this->base_uri . "plugin/admin_tools/admin_countries/"  );	
    }		
}

?>