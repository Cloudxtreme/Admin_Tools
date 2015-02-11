<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminLanguages extends AdminUtilsController {

	/**
	 * Pre Action
	 */
	public function preAction() {
		parent::preAction();
		
        // Require login
        $this->requireLogin(); 
		Language::loadLang("languages", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
		
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);
		
		// $this->uses(array("admin_utils.UtilServices")); // Call Notes Model Inside admin_utils 
		$this->uses(array("AdminUtils.UtilLanguages")); // Call navigation Model Inside admin_utils 
		$this->Tabs = $this->getTabs($current = "languages") ;
		
		$this->NavigationLinks = '
				<div class="links_row">
					<a class="btn_right "	href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_languages/addlang") .'"><span>'. Language::_("AdminToolsPlugin.languages.addlanguage" , true ) .'</span></a>				
				</div>';
				
		$language = Language::_("AdminToolsPlugin.languages." . Loader::fromCamelCase($this->action ? "page_title.".  $this->action : "page_title") , true);
		$this->structure->set("page_title", $language);
		
	}     
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {
		$this->uses(array("Languages"));
		
		$all_languages = $this->Languages->getAvailable();
		$installed_languages = $this->Languages->getAll($this->company_id);
		$uninstallable_languages = $this->Languages->getAllUninstallable($this->company_id);
		
		// Format the languages for the view
		$languages = array();
		$i=0;
		foreach ($all_languages as $code => $name) {
			$languages[$i] = new stdClass();
			$languages[$i]->code = $code;
			$languages[$i]->name = $name;
			$languages[$i]->installed = false;
			$languages[$i]->uninstallable = in_array($code, $uninstallable_languages);
			
				if ($languages[$i]->uninstallable)
					unset($languages[$i]);
					
			$i++;
		}
		unset($i);
		
		// Set whether or not a language has been installed
		$num_installed = count($installed_languages);
		$num_languages = count($languages);
		for ($i=0; $i<$num_installed; $i++) {
			for ($j=0; $j<$num_languages; $j++) {			
				if ($installed_languages[$i]->code == $languages[$j]->code)
					$languages[$j]->installed = true;				
			}
		}
		
		$this->set("languages", $languages);
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
    }
	
	/**
	 * Check Language
	 */
    public function check() {
		
		$languages = $this->UtilLanguages->CheckLang($this->get[0]);
		
		$this->set("languages", $languages);
		$this->set("tabs", $this->Tabs);
    }	
	
	/**
	 * Add Language
	 */
    public function addlang() {
		
		$this->set("tabs", $this->Tabs);
    }
	
	/**
	 * Edit File Language
	 */
    public function edit() {		

		$this->set("tabs", $this->Tabs);
    }	
	
	/**
	 * Edit File Language
	 */
    public function create() {
			
		$this->set("tabs", $this->Tabs);
    }		
	
}

?>