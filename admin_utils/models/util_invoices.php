<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Admin Utils
 * @copyright Copyright (c) 2014, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
 
class UtilInvoices extends AdminUtilsModel {
	
	/**
	 * Initialize UtilInvoices
	 */
	public function __construct() {
		parent::__construct();

		Loader::loadModels($this, array("Invoices", "Companies"));

	}
	
	
	/**
	 * Get Last Proforma ID 
	 */
	public function GetLastProformaID() {
	
		$fields = array("id_value");
		
		// Fetch last Proforma ID in Database
		$query = $this->Record->select($fields)->
			from("invoices")->
			where("status", "=", "proforma")->
			limit( 1 , 0)->
			order(array('id_value'=>"DESC"))->
			fetchAll();
			
		return $query[0]->id_value + 1  ; 
	}
	
	/**
	 * 
	 */
	public function EuInvoicing($invoice_id) {		
		Loader::loadComponents($this, array("SettingsCollection"));
		$settings = $this->SettingsCollection->fetchSettings($this->Companies, $this->company_id);

		if ($settings['inv_type'] == "proforma") {
			$UtilSecuritySettings = $this->Companies->getSetting($this->company_id , "AdminUtilsPluginInvoicing");
			$vars  = unserialize($UtilSecuritySettings->value);
		
			if ($vars['eu_invoicing']) {			
				$invoice =   (array) $this->Invoices->get($invoice_id);
				if ($invoice['status'] == "proforma" && $invoice['id_value'] != $vars['last_proforma_id']) {
					$this->Record->where("id", "=", $invoice['id'])->update("invoices", array("id_value"=>(int)$vars['last_proforma_id']));
					$vars['last_proforma_id']++ ;
					$this->Companies->setSetting($this->company_id , "AdminUtilsPluginInvoicing", serialize($vars));					
				}
			}
		}				
	}	

	
	/**
	 * 
	 */
	public function Invoicesadd($invoice_id) {
		// TODO			
	}

	/**
	 * 
	 */
	public function setClosed($invoice_id) {
				
	}		
}
?>