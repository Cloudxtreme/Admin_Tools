<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
 
class UtilNavigations extends AdminUtilsModel {
	
	/**
	 * Initialize Clients
	 */
	public function __construct() {
		parent::__construct();
		Language::loadLang(array("navigation"));

	}
		
	public function getPrimaryStaff($base_uri) {
	
		$nav = array(
			$base_uri . "/" => array(
				'name' => $this->_("Navigation.getprimary.nav_home"),
				'edit' => false,
				'active' => false
			),
			$base_uri . "clients/" => array(
				'name' => $this->_("Navigation.getprimary.nav_clients"),
				'edit' => false,
				'active' => false
			),
			$base_uri . "billing/" => array(
				'name' => $this->_("Navigation.getprimary.nav_billing"),
				'edit' => false,
				'active' => false
			),
			$base_uri . "packages/" => array(
				'name' => $this->_("Navigation.getprimary.nav_packages"),
				'edit' => false,
				'active' => false
			),
			$base_uri . "tools/" => array(
				'name' => $this->_("Navigation.getprimary.nav_tools"),
				'edit' => false,
				'active' => false
			)

		);
		
		// Set plugin primary nav elements
		$plugin_nav = $this->getPluginNav("nav_primary_staff");
		
		foreach ($plugin_nav as $element) {
			$nav[$base_uri . $element->uri] = array(
				'name' => $element->name,
				// 'options' => $element->options,
				'action' => $element->action,
				'edit' => true,
				'plugin_id' => $element->plugin_id,
				'active' => false
			);
			
			// Set primary nav sub nav items if set
			if (isset($element->options['sub'])) {
				$nav[$base_uri . $element->uri]['sub'] = array();
				foreach ($element->options['sub'] as $sub) {
					$nav[$base_uri . $element->uri]['sub'][$base_uri . $sub['uri']] = array(
						'name' => $sub['name'],
						// 'options' => $sub['options'],
						'edit' => false,
						'active' => false
					);
				}
			}
		}
		
		// Set plugin secondary nav elements
		$plugin_nav = $this->getPluginNav("nav_secondary_staff");
		
		foreach ($plugin_nav as $element) {
			if (!isset($element->options['parent']))
				continue;
			
			if (isset($nav[$base_uri . $element->options['parent']])) {
				$nav[$base_uri . $element->options['parent']]['sub'][$base_uri . $element->uri] = array(
					'name' => $element->name,
					'options' => $element->options['parent'],
					'action' => $element->action,
					'edit' => true,
					'plugin_id' => $element->plugin_id,					
					'active' => false
				);
			}
		}		
		
		return $nav;
		
	}
	
	public function getPrimaryClient($base_uri ) {	
		
		$nav = array(
			$base_uri . "/" => array(
				'name' => $this->_("Navigation.getprimaryclient.nav_dashboard"),
				'edit' => false,
				'active' => false
			),
			$base_uri . "accounts/" => array(
				'name' => $this->_("Navigation.getprimaryclient.nav_paymentaccounts"),
				'edit' => false,
				'active' => false ),
			$base_uri . "contacts/" => array(
				'name' => $this->_("Navigation.getprimaryclient.nav_contacts"),
				'edit' => false,
				'active' => false )
		);
		
		$plugin_nav = $this->getPluginNav("nav_primary_client");
		
		foreach ($plugin_nav as $element) {
			$nav[$base_uri . $element->uri] = array(
				'name' => $element->name,
				'action' => $element->action,
				'edit' => true,
				'plugin_id' => $element->plugin_id,		
				'active' => false,
			);
			
			// Set secondary nav sub nav items if set
			if (isset($element->options['secondary'])) {
				$nav[$base_uri . $element->uri]['secondary'] = array();
				foreach ($element->options['secondary'] as $sub) {
					$nav[$base_uri . $element->uri]['secondary'][$base_uri . $sub['uri']] = array(
						'name' => $sub['name'],
						'active' => false,
						'edit' => true,
					);
				}
			}
			
			// Set primary nav sub nav items if set
			if (isset($element->options['sub'])) {
				$nav[$base_uri . $element->uri]['sub'] = array();
				foreach ($element->options['sub'] as $sub) {
					$nav[$base_uri . $element->uri]['sub'][$base_uri . $sub['uri']] = array(
						'name' => $sub['name'],
						'edit' => true,
						'active' => false,
					);
				}
			}
		}
		
		return $nav;
		
	}	
	
	/**
	 * Retrieves the specified action from the given plugin
	 * This should be called from PluginManager model (v3.3.2)
	 */
	public function getAction($plugin_id, $uri, $action) {		
		/*
		if (!isset($this->PluginManager))
			Loader::loadModels($this, array("PluginManager"));
			
		return $this->PluginManager->getAction($plugin_id, $action);	
		*/ // Disabled this function for bug in PluginManager . 
		
		$fields = $this->Record->select(array("plugin_id", "action", "uri", "name", "options"))->
			from("plugin_actions")->
			where("plugin_id", "=", $plugin_id)->
			where("uri", "=", $uri)->
			where("action", "=", $action)->
			fetch();
			
		if ($fields) {
			if ($fields->options)
				$fields->options = unserialize($fields->options);
		}
		
		return $fields;
	}

	/**
	 * Add navigation Link
	 */	
	public function addAction($plugin_id, $vars) {

		if (!isset($this->PluginManager))
			Loader::loadModels($this, array("PluginManager"));	
		
		$this->PluginManager->addAction($plugin_id, $vars);
		
		if (($errors = $this->PluginManager->errors())) {		
			$this->Input->setErrors($errors);
		}		
		
		$path = $this->company_id . DS . "nav" . DS ; 
		if (!($dir = @opendir(CACHEDIR . $path)))
			return;

		while ($item = @readdir($dir)) {
			if (is_file(CACHEDIR . $path . $item))
				@unlink(CACHEDIR . $path . $item);
		}
		
	}

	/**
	 * Edit The navigation Link
	 */
	public function EditAction($plugin_id , $vars) {
		
		// Delete the actual navigation link
		// $this->Record->from("plugin_actions")->where("plugin_id", "=", $plugin_id)->where("uri", "=", $vars['uri'])->where("action", "=", )->delete();
		$this->Record->from("plugin_actions")->where("plugin_id", "=", $plugin_id)->where("action", "=", $vars['action'])->delete();
		
		// Add the navigation link
		if (!isset($this->PluginManager))
			Loader::loadModels($this, array("PluginManager"));

		$this->PluginManager->addAction($plugin_id , $vars);		

		Cache::clearCache("nav_staff_group_" . $this->staff_id, $this->company_id . DS . "nav" . DS);
	}	

	/**
	 * Removes the action from the plugin
	 */
	public function deleteAction($plugin_id, $uri, $action) {
		$this->Record->from("plugin_actions")->where("plugin_id", "=", $plugin_id)->where("uri", "=", $uri)->where("action", "=", $action)->delete();

		$path = $this->company_id . DS . "nav" . DS ; 
		if (!($dir = @opendir(CACHEDIR . $path)))
			return;

		while ($item = @readdir($dir)) {
			if (is_file(CACHEDIR . $path . $item))
				@unlink(CACHEDIR . $path . $item);
		}
		
	}	

	/**
	 * get the plugin ID for a given plugin name
	 */	
	public function PluginID($dir, $company_id=null) {
		$this->Record->select(array("plugins.id"))->from("plugins")->
			where("dir", "=", $dir);
			
		if ($company_id)
			$this->Record->where("company_id", "=", $company_id);
			
		return $this->Record->fetch();
	}
	
	/***** PRIVATE FUNCTIONS *****/
	
	/**
	 * Returns all plugin navigation for the requested location
	 *
	 * @param string $location The location to fetch plugin navigation for
	 * @return array An array of plugin navigation
	 */
	private function getPluginNav($location) {
		if (!isset($this->PluginManager))
			Loader::loadModels($this, array("PluginManager"));
			
		return $this->PluginManager->getActions(Configure::get("Blesta.company_id"), $location);
	}	

}
?>