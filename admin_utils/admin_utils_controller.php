<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminUtilsController extends AppController {

	/**
	 * Setup
	 */
	public function preAction() {		
		$this->structure->setDefaultView(APPDIR);
		parent::preAction();
		
		// Override default view directory
		$this->view->view = "default";
		$this->orig_structure_view = $this->structure->view;
		$this->structure->view = "default";		
		
		Language::loadLang("admin_utils", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);

		
        // Set the company ID
        $this->company_id = Configure::get("Blesta.company_id");
		$this->staff_id = $this->Session->read("blesta_staff_id");
		$this->user_id  = $this->Session->read("blesta_id");
	
	}
	/**
	 * Set all tabs
	 */
	protected function getTabs($current) {
	
		if (!isset($this->PluginManager))
			Loader::loadModels($this, array("PluginManager"));

			
		$tabs =  array( // 
			array('name' => Language::_("AdminToolsPlugin.index.tab_emptycache", true),	'current' => ($current == "emptycache" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_main/")),
			array('name' => Language::_("AdminToolsPlugin.index.tab_countries", true),	'current' => ($current == "countries" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_countries/")),
			array('name' => Language::_("AdminToolsPlugin.index.tab_clients", true),	'current' => ($current == "clients" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_clients/")),			
			array('name' => Language::_("AdminToolsPlugin.index.tab_notes", true),		'current' => ($current == "notes" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_notes/" )),
			array('name' => Language::_("AdminToolsPlugin.index.tab_security", true),	'current' => ($current == "security" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_security/" )),
			array('name' => Language::_("AdminToolsPlugin.index.tab_navigation", true),	'current' => ($current == "navigation" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_navigation/" )),
			// NOTE YET
			array('name' => Language::_("AdminToolsPlugin.index.tab_groups", true),		'current' => ($current == "groups" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_groups/" )),
			array('name' => Language::_("AdminToolsPlugin.index.tab_services", true),	'current' => ($current == "services" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_services/")),
			array('name' => Language::_("AdminToolsPlugin.index.tab_invoices", true),	'current' => ($current == "invoices" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_invoices/"))
		);

		if ($this->PluginManager->isInstalled("cms", $this->company_id ))		
			$tabs[]	= array('name' => Language::_("AdminToolsPlugin.index.tab_cms", true),	'current' => ($current == "cms" ? true : false ) , 'attributes' => array('href' => $this->base_uri . "plugin/admin_utils/admin_cms/" ));		
		
		return $tabs;
	}	
}
?>