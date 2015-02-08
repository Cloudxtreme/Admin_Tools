<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */ 
class AdminCms extends AdminUtilsController {


	/**
	 * Pre Action
	 */
	public function preAction() {
		parent::preAction();
		
        // Require login
        $this->requireLogin(); 
		Language::loadLang("cms", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
		
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);		

		$this->uses(array("admin_utils.UtilCms"));  // Call CMS Model Inside admin_utils 
		
		$this->pages = $this->UtilCms->getAll($this->company_id);
		
		$this->Tabs = $this->getTabs($current = "cms") ;
		
		$this->NavigationLinks = '
				<div class="links_row">
					<a class="btn_right" href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_cms/add/") .'"><span>'. Language::_("AdminToolsPlugin.cms.add_page", true) .'</span></a>
					<a class="btn_right pages" href="#"><span>'. Language::_("AdminToolsPlugin.cms.total_pages", true , count($this->pages)) .'</span></a>
				</div>				
				';
				
		$language = Language::_("AdminToolsPlugin.cms." . Loader::fromCamelCase($this->action ? "page_title.".  $this->action : "page_title") , true);
		$this->structure->set("page_title", $language);
		
	} 
	
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {
	
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
		$this->set("pages", $this->pages);
    }

	/**
	 * Add Page
	 */
    public function add() {
		
		$vars = null;
		
		if (!empty($this->post)) {
			$vars = (object)$this->post;
			$data = $this->post;
			$data['company_id'] = $this->company_id;			
			
			$this->UtilCms->add($data) ;
			
			if (!($errors =  $this->UtilCms->errors())) {		
				$this->flashMessage("message", Language::_("AdminToolsPlugin.cms.add.!success", true), null, false);
				$this->redirect($this->base_uri . "plugin/admin_utils/admin_cms/" );			
			}			
			$this->setMessage("error", $errors, false, null, false);			
		}
		
		$tags = array("{base_url}", "{blesta_url}", "{admin_url}", "{client_url}", "{plugins}");
		
		$this->set("vars", $vars);
		$this->set("tags", $tags);
		$this->set("tabs", $this->Tabs);
    }
	
	/**
	 * Edit Page
	 */
    public function edit() {
	
		$uri = "/";
		if (isset($this->get[0]))
			$uri = $this->get[0];	
		
		$vars = $this->UtilCms->get($uri, $this->company_id) ;

		if (!empty($this->post)) {	
			$vars = (object)$this->post;		
			$data = $this->post;
			$data['company_id'] = $this->company_id;			
			
			$this->UtilCms->add($data) ;
			
			if (!($errors =  $this->UtilCms->errors())) {		
				$this->flashMessage("message", Language::_("AdminToolsPlugin.cms.edit.!success", true), null, false);
				$this->redirect($this->base_uri . "plugin/admin_utils/admin_cms/");			
			}
			$this->setMessage("error", $errors, false, null, false);
		}
		
		$tags = array("{base_url}", "{blesta_url}", "{admin_url}", "{client_url}", "{plugins}");
		
		$this->set("tags", $tags);		
		$this->set("tabs", $this->Tabs);		
		$this->set("vars", $vars);
    }		
	
	/**
	 * Delete Note
	 */
    public function delete() {
		$vars = array();	
		
		if (isset($this->post['uri'])) {
			$vars['uri'] = $this->post['uri'];
			$vars['company_id'] = $this->company_id;	
			
			if ($vars['uri'] == "/") {			
				$this->flashMessage("error", Language::_("AdminToolsPlugin.cms.!error.index.page", true), null, false);
				$this->redirect($this->base_uri . "plugin/admin_utils/admin_cms/"  );	
			}				
			
			$this->UtilCms->delete($vars) ;
			
			if (!($errors =  $this->UtilCms->errors())) {		
				$this->flashMessage("message", Language::_("AdminToolsPlugin.cms.delete.!success", true), null, false);
				$this->redirect($this->base_uri . "plugin/admin_utils/admin_cms/");			
			}
			$this->setMessage("error", $errors, false, null, false);		
		}
		
		$this->redirect($this->base_uri . "plugin/admin_utils/admin_cms/"  );	
    }
			
}

?>