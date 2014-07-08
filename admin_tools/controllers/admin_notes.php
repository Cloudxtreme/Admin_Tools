<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminNotes extends AppController {

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
		$this->view->setView(null, "AdminTools.default");
		
		$this->staff_id = $this->Session->read("blesta_staff_id");	
		
		$this->uses(array("admin_tools.Notes")); // need to check this one !!!!
    }
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
    public function index() {	
		$this->init();

	
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		$this->set("notes", $this->Notes->GetNotes());
		$this->set("total_notes", $this->Notes->getNoteListCount());
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.notes.page_title", true));
    }

	/**
	 * Delete Note
	 */
    public function delete() {	
		$this->init();
	
			$this->Notes->deleteNote($this->get[0]) ;
			
			if (($errors = $this->Notes->errors()))
				$this->setMessage("error", $errors, false, null, false);
			else
				$this->flashMessage("message", Language::_("AdminToolsPlugin.notes.delete.!success", true), null, false);
		
		$this->redirect($this->base_uri . "plugin/admin_tools/admin_notes/"  );	
    }
	
	/**
	 * Returns Sticky Notes
	 */
    public function sticky() {	
		$this->init();

	
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		$this->set("notes", $this->Notes->getAllStickyNotes());
		$this->set("total_notes", $this->Notes->getNoteListCount());
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.notes.sticky.page_title", true));
    }	


	/**
	 * Returns UnSticky Notes
	 */
    public function unsticky() {
		$this->init();

	
		// Set the view to render for all actions under this controller		
		$this->view->setView(null, "AdminTools.default");
		$this->set("notes", $this->Notes->getAllUnStickyNotes());
		$this->set("total_notes", $this->Notes->getNoteListCount());
		$this->structure->set("page_title", Language::_("AdminToolsPlugin.notes.unsticky.page_title", true));
    }	

	/**
	 * Sets the given note as unstickied
	 *
	 * @param int $note_id The note ID
	 */
    public function unsticknote() {
		$this->init();

			$this->Notes->unstickNote($this->get[0]) ;
			
			if (($errors = $this->Notes->errors()))
				$this->setMessage("error", $errors, false, null, false);
			else
				$this->flashMessage("message", Language::_("AdminToolsPlugin.notes.unstick.!success", true), null, false);
		
		$this->redirect( $_SERVER['HTTP_REFERER'] );	
    }	

	/**
	 * Sets the given note as unstickied
	 *
	 * @param int $note_id The note ID
	 */
    public function sticknote() {
		$this->init();

			$this->Notes->stickNote($this->get[0]) ;
			
			if (($errors = $this->Notes->errors()))
				$this->setMessage("error", $errors, false, null, false);
			else
				$this->flashMessage("message", Language::_("AdminToolsPlugin.notes.stick.!success", true), null, false);
		
		$this->redirect( $_SERVER['HTTP_REFERER'] );	
    }		
}

?>