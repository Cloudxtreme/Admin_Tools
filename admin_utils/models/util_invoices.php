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
	
	private $company;
	
	/**
	 * @var array Company settings
	 */
	private $company_settings;
	
	/**
	 * Initialize UtilInvoices
	 */
	public function __construct() {
		parent::__construct();

		Loader::loadComponents($this, array("SettingsCollection"));
		Loader::loadModels($this, array("Invoices",  "Companies"));		
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
	public function Invoicesadd($invoice_id) {		
		
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
	public function setClosed($invoice_id) {
		// Loader::loadComponents($this, array("SettingsCollection"));
		// $settings = $this->SettingsCollection->fetchSettings($this->Companies, $this->company_id);

		// if ($settings['inv_type'] == "proforma") {
			// $UtilSecuritySettings = $this->Companies->getSetting($this->company_id , "AdminUtilsPluginInvoicing");
			// $vars  = unserialize($UtilSecuritySettings->value);

			// if ($vars['correct_dateinvoice']) {
				// $invoice =   (array) $this->Invoices->get($invoice_id);				
				// $this->Record->where("id", "=", $invoice['id'])->update("invoices", array("date_billed"=> $invoice['date_closed']));
			// }
			
		// }
		
		// should here add condition to save pdf file
		$this->SaveInvoiceCopy($invoice_id , $vars['correct_dateinvoice']);		
		
		
	}
	
	/**
	 * 
	 */
	private function SaveInvoiceCopy($invoice_id , $correct_dateinvoice = null ) {
		
		if ($correct_dateinvoice) {
			Loader::loadComponents($this, array("Upload"));
			
			$invoice =   $this->Invoices->get($invoice_id);
			$document = $this->buildInvoices(array($invoice), true, null);
			
			$temp = $this->SettingsCollection->fetchSetting(null, Configure::get("Blesta.company_id"), "uploads_dir");
			$upload_path = $temp['value'] . Configure::get("Blesta.company_id") . DS . "pdf_invoices" . DS;
			// Create the upload path if it doesn't already exist
			$this->Upload->createUploadPath($upload_path, 0777);
				
			$inv_path = $upload_path . "invoice-". $invoice->id_code  .".pdf" ;			
			file_put_contents($inv_path, $document->fetch());		
		}
		
	}		
	
	private function buildInvoices(array $invoices, $include_address=true, array $options=null) {	
		
		Loader::loadHelpers($this, array("CurrencyFormat", "Date"));		
		Loader::loadComponents($this, array("Input", "InvoiceTemplates"));
		Loader::loadModels($this, array("Clients", "Contacts", "Countries", "Transactions"));
		
		$company_id = Configure::get("Blesta.company_id");
		$this->CurrencyFormat->setCompany(Configure::get("Blesta.company_id"));
		
		$client_id = null;
		$transaction_types = $this->Transactions->transactionTypeNames();
		for ($i=0, $num_invoices=count($invoices); $i<$num_invoices; $i++) {
			
			if ($client_id != $invoices[$i]->client_id) {
				$client_id = $invoices[$i]->client_id;
				
				$client = $this->Clients->get($client_id);
				if (!($billing = $this->Contacts->get((int)$client->settings['inv_address_to'])) || $billing->client_id != $client_id)
					$billing = $this->Contacts->get($client->contact_id);
				$country = $this->Countries->get($billing->country);
				
				$this->language = $client->settings['language'];
			}
			
			$invoices[$i]->billing = $billing;
			$invoices[$i]->billing->country = $country;
			$invoices[$i]->client = $client;
			

			$invoices[$i]->applied_transactions = $this->Transactions->getApplied(null, $invoices[$i]->id);
			foreach ($invoices[$i]->applied_transactions as &$applied_transaction) {
				$applied_transaction->type_real_name = $transaction_types[($applied_transaction->type_name != "" ? $applied_transaction->type_name : $applied_transaction->type)];
			}
		}
		
		$this->company = $this->Companies->get($company_id);
		$this->company_settings = $this->SettingsCollection->fetchSettings($this->Companies, $this->company->id);
		$this->Date->setTimezone("UTC", $this->company_settings['timezone']);

		
		// Set the invoice attachments
		$document = null;
		
		// Set a 'global' language for all invoices in the document
		$language = ($options && isset($options['language']) ? $options['language'] : null);
		
		
		
		try {
			$meta = array(
				'paper_size' => $this->company_settings['inv_paper_size'],
				'background' => $this->company_settings['inv_background'],
				'logo' => $this->company_settings['inv_logo'],
				'company_name' => $this->company->name,
				'company_address' => $this->company->address,
				'tax_id' => $this->company_settings['tax_id'],
				'terms' => $this->company_settings['inv_terms'],
				'display_logo' => $this->company_settings['inv_display_logo'],
				'display_paid_watermark' => $this->company_settings['inv_display_paid_watermark'],
				'display_companyinfo' => $this->company_settings['inv_display_companyinfo'],
				'display_payments' => $this->company_settings['inv_display_payments'],
				'settings' => $this->company_settings,
				'language' => $language
			);
		
			$document = $this->InvoiceTemplates->create($this->company_settings['inv_template']);
			$document->setMeta($meta);
			$document->setCurrency($this->CurrencyFormat);
			$document->setDate($this->Date);
			$document->setMimeType("application/pdf");
			$document->includeAddress($include_address);
			$document->makeDocument($invoices);
		}
		catch (Exception $e) {
			$this->Input->setErrors(array('InvoiceTemplates' => array('create' => $e->getMessage())));
		}
		return $document;
	}		
}
?>