<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Admin Utils
 * @copyright Copyright (c) 2014, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminNavigation extends AdminUtilsController {


	/**
	 * Pre Action
	 */
	public function preAction() {
		parent::preAction();
		
        // Require login
        $this->requireLogin(); 
		Language::loadLang("navigation", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
		
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);
		
		$this->uses(array("AdminUtils.UtilNavigations")); // Call navigation Model Inside admin_utils 
		
		$this->Tabs = $this->getTabs($current = "navigation") ;
		
		$this->NavigationLinks = '';
				
		// $this->NavigationLinks = '
				// <div class="links_row">
					// <a class="btn_right add" href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_navigation/addlink/") .'"><span>'. Language::_("AdminToolsPlugin.navigation.addlink", true ) .' </span></a>
				// </div>';
				
		$this->link_action = array(
			'nav_primary_staff' => Language::_("AdminToolsPlugin.navigation.nav_primary_staff", true),
			'nav_secondary_staff' => Language::_("AdminToolsPlugin.navigation.nav_secondary_staff", true),
			'nav_primary_client' => Language::_("AdminToolsPlugin.navigation.nav_primary_client", true),
			'action_staff_client' => Language::_("AdminToolsPlugin.navigation.action_staff_client", true)
			/*
			'widget_staff_home' => Language::_("AdminToolsPlugin.navigation.widget_staff_home", true),
			'widget_staff_client' => Language::_("AdminToolsPlugin.navigation.widget_staff_client", true),
			'widget_staff_billing' => Language::_("AdminToolsPlugin.navigation.widget_staff_billing", true)
			'widget_client_home' => Language::_("AdminToolsPlugin.navigation.widget_client_home", true)
			*/
		);
		
		// $this->plugin_id = $this->UtilNavigations->PluginID("admin_utils" , Configure::get("Blesta.company_id"))->id ;
		
		$language = Language::_("AdminToolsPlugin.navigation." . Loader::fromCamelCase($this->action ? "page_title.".  $this->action : "page_title") , true);
		$this->structure->set("page_title", $language);
		
	} 
	
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {
	
		$this->set("tabs", $this->Tabs);
		$this->set("navigationlinks", $this->NavigationLinks );
		
		$this->set("navstaff", $this->UtilNavigations->getPrimaryStaff($base_uri = null ));
		$this->set("navclient", $this->UtilNavigations->getPrimaryClient($base_uri = null ));
    }


	/**
	 * Edit Navigation
	 */
    public function editlink() {	

		if (!empty($this->post)) {
			
			if(!isset($this->post['save'])) {
				$this->set("nav", $this->UtilNavigations->getAction($this->post['plugin_id'], $this->post['uri'], $this->post['action']));
			}
			else {
			
				$data = array(
					'action' => $this->post['action'],
					'uri' => $this->post['uri'],
					'name' => $this->post['name']
				);			
				
				if (isset($this->post['parent'])) {
					$data['options'] = array('parent' => $this->post['parent']);		
					// unset($this->post['parent']);					
				}
				
				if (isset($this->post['options']['sub'])) {					
					$data['options'] = array('sub' => $this->post['options']['sub'] );					
				}
				
				$this->UtilNavigations->EditAction($this->post['plugin_id'] , $data);
				$this->flashMessage("message",  Language::_("AdminToolsPlugin.navigation.edit.!success", true) , null, false);
				$this->redirect($this->base_uri . "plugin/admin_utils/admin_navigation/");
				
				$this->set("nav", $this->UtilNavigations->getAction($this->post['plugin_id'], $this->post['uri'], $this->post['action']));
			}
			
		}
		
		$this->set("tabs", $this->Tabs);
		
    }
	
	/**
	 * Add Navigation
	 */
    public function addlink() {
	
		$vars = array();
				
		// print_r($this->UtilNavigations->PluginID("admin_utils" , Configure::get("Blesta.company_id"))->id);
		
		if (!empty($this->post)) {
				
			$data = array(
				'action' => $this->post['action'],
				'uri' => $this->post['uri'],
				'name' => $this->post['name'],
				'options'  => null
			);
			
			$this->UtilNavigations->addAction($this->post['plugin_id'], $data);		
			
			if (($errors = $this->UtilNavigations->errors())) {
				// Error, reset vars
				$vars = $this->post;
				$this->setMessage("error", $errors, false, null, false);
			}
			else {
				// Success
				$this->flashMessage("message",  Language::_("AdminToolsPlugin.navigation.add.!success", true) , null, false);
				$this->redirect($this->base_uri . "plugin/admin_utils/admin_navigation/");
			}
			
		}
		
		$this->set("action", $this->link_action);
		$this->set("plugin_id", $this->plugin_id);
		$this->set("tabs", $this->Tabs);
		$this->set("navigationlinks", $this->NavigationLinks );	
	
    }	
	
	/**
	 * Delete Navigation
	 */
    public function delete() {	
	
		if (!empty($this->post)) {

			$this->UtilNavigations->deleteAction($this->post['plugin_id'], $this->post['uri'], $this->post['action']);
			$this->flashMessage("message",  Language::_("AdminToolsPlugin.navigation.delete.!success", true) , null, false);
		}
		
		$this->redirect($this->base_uri . "plugin/admin_utils/admin_navigation/"  );	
    }
	


}

?>