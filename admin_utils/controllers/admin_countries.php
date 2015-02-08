<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminCountries extends AdminUtilsController {

	/**
	 * Pre Action
	 */
	public function preAction() {
		parent::preAction();
		
        // Require login
        $this->requireLogin(); 
		Language::loadLang("countries", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
	
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);	
	
		$this->uses(array("Countries", "States"));
		$this->countrylist = $this->Countries->getList() ;
		$this->Tabs = $this->getTabs($current = "countries") ;
		
		$this->NavigationLinks = '
				<div class="links_row">
					<a class="btn_right" href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_countries/add/") .'"><span>'. Language::_("AdminToolsPlugin.countries.heading_add", true) .'</span></a>
					<a class="btn_right countries" href="#"><span>'. Language::_("AdminToolsPlugin.countries.total_countries", true , count($this->countrylist)) .'</span></a>
				</div>';
				
		$language = Language::_("AdminToolsPlugin.countries." . Loader::fromCamelCase($this->action ? "page_title.".  $this->action : "page_title") , true);
		$this->structure->set("page_title", $language);				
	}
	
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {
		
		$this->set("countries", $this->countrylist );
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);		
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.countries.page_title", true));
    }

	/**
	 * Add Country
	 */
    public function add() {
		
		$vars = null;
		
		if (!empty($this->post)) {
			$vars = (object)$this->post;

			$this->Countries->add($this->post) ;
			
			if (!($errors =  $this->Countries->errors())) {		
				$this->flashMessage("message", Language::_("AdminToolsPlugin.countries.add.!success", true), null, false);
				$this->redirect($this->base_uri . "plugin/admin_utils/admin_countries/edit/" . $this->post['alpha2']);			
			}			
			$this->setMessage("error", $errors, false, null, false);
			$this->set("countries", $vars);
		}

		$this->set("tabs", $this->Tabs);
    }
	
	/**
	 * Edit Country
	 */
    public function edit() {
	
		$vars = $this->Countries->get($this->get[0]) ;

		if (!empty($this->post)) {
			
			$this->Countries->edit($this->post['alpha2'] , $this->post) ;
			
			if (!($errors =  $this->Countries->errors())) {		
				$this->flashMessage("message", Language::_("AdminToolsPlugin.countries.edit.!success", true), null, false);
				$this->redirect($this->base_uri . "plugin/admin_utils/admin_countries/edit/" . $this->get[0] );			
			}
			$vars = (object)$this->post;
			$this->setMessage("error", $errors, false, null, false);
			$this->set("admin_countries_add", $vars);
		}
		
		$this->set("tabs", $this->Tabs);		
		$this->set("countries", $vars);
    }	
	
	/**
	 * Edit Country
	 */
    public function delete() {

		if (isset($this->post['alpha2'])) {
	
			$this->Countries->delete($this->post['alpha2']) ;
			
			if (($errors = $this->Countries->errors()))
				$this->setMessage("error", $errors, false, null, false);
			else
				$this->flashMessage("message", Language::_("AdminToolsPlugin.countries.delete.!success", true), null, false);
		}
		
		$this->redirect($this->base_uri . "plugin/admin_utils/admin_countries/"  );	
    }		
}

?>