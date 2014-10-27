<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminInvoices extends AdminUtilsController {

	/**
	 * Pre Action
	 */
	public function preAction() {
		parent::preAction();
		
        // Require login
        $this->requireLogin(); 
		Language::loadLang("invoices", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
		
		// Restore structure view location of the admin portal
		$this->structure->setDefaultView(APPDIR);
		$this->structure->setView(null, $this->orig_structure_view);
		
		$this->Tabs = $this->getTabs($current = "invoices") ;
		
		$this->NavigationLinks = '
				<div class="links_row">
					<a class="btn_right service"	href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_invoices/moveinvoice/") .'"><span>'. Language::_("AdminToolsPlugin.invoices.moveinvoice", true ) .'</span></a>
					<a class="btn_right orpholan"	href="'. $this->Html->safe($this->base_uri . "plugin/admin_utils/admin_invoices/deleteinvoice/") .'"><span>'. Language::_("AdminToolsPlugin.invoices.deleteinvoice" , true ) .'</span></a>
				</div>';
				
		$language = Language::_("AdminToolsPlugin.invoices." . Loader::fromCamelCase($this->action ? "page_title." . $this->action : "page_title") , true);
		$this->structure->set("page_title", $language);
		
	}     
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {

		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
    }

	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function moveinvoice() {		
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
    }

	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function deleteinvoice() {	
		$this->set("tabs", $this->Tabs);		
		$this->set("navigationlinks", $this->NavigationLinks);	
    }

}

?>