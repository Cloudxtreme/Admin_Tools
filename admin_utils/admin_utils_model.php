<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Admin Utils
 * @copyright Copyright (c) 2014, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
class AdminUtilsModel extends AppModel {
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		// Auto load language for these models
		Language::loadLang("model", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
		$this->company_id = Configure::get("Blesta.company_id");
		
		if (!isset($this->Session))
			Loader::loadComponents($this, array("Session"));
			
        $this->company_id = Configure::get("Blesta.company_id");
		$this->staff_id = $this->Session->read("blesta_staff_id");
		$this->user_id  = $this->Session->read("blesta_id");
	}
}
?>