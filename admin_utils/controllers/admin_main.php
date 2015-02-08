<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Admin Utils
 * @copyright Copyright (c) 2014, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminMain extends AdminUtilsController {

	/**
	 * Pre Action
	 */
	public function preAction() {
		parent::preAction();
		
        // Require login
        $this->requireLogin(); 		
		Language::loadLang("emptycache", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
		
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);		
		
		$this->Tabs = $this->getTabs($current = "emptycache") ; 
	}
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */ 
    public function index() {
	
		$cache = Cache::fetchCache( "nav_staff_group_" . $this->staff_id, $this->company_id . DS . "nav" . DS ) ; 
			
		if (!empty($this->post)) {
		
			/*
			$path = $this->company_id . DS . "nav" . DS ;
			if (!($dir = @opendir(CACHEDIR . $path)))
				return;

			while ($item = @readdir($dir)) {
				if (is_file(CACHEDIR . $path . $item))
					@unlink(CACHEDIR . $path . $item);
			}
			
			$this->flashmessage("message", Language::_("AdminToolsPlugin.emptycache.!success", true), null, false);
			$this->redirect($this->base_uri . "plugin/admin_utils/admin_main/");
			*/
			
			if (!Cache::clearCache("nav_staff_group_" . $this->staff_id, $this->company_id . DS . "nav" . DS)) {
				// Error
				$this->setMessage("error", Language::_("AdminToolsPlugin.emptycache.!error",  true) , false, null, false);		
				
			}
			else {
				// Success
				$this->flashmessage("message", Language::_("AdminToolsPlugin.emptycache.!success",  true) ,  null, false);					
				$this->redirect($this->base_uri . "plugin/admin_utils/admin_main/");
			}
			
			
		}			

		$this->view->setView(null, "AdminUtils.default");
		$this->set("cache", $cache);
		$this->set("tabs", $this->Tabs);
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.emptycache.page_title", true));
    }
}
?>