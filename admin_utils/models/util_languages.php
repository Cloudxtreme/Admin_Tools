<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */

class UtilLanguages extends AdminUtilsModel {
	
	/**
	 * Initialize
	 */
	public function __construct() {
		parent::__construct();
		
		Language::loadLang("cms", null, PLUGINDIR . "admin_utils" . DS . "language" . DS);
		
		$diffArray = array();
		$diffResult = array();
		$langArray = array();
		$defaultTmp = array();
		$customTmp = array();
	
	}
	
	public function CheckLang($custom_Lang){
	
		$defaultLangFiles 	= scandir( LANGDIR . '/en_us/' );
		$lang = array();
		
		foreach( $defaultLangFiles as $defaultLang )
		{
			if( substr( $defaultLang, -3, 3 ) == 'php')
			{
				include( LANGDIR . '/en_us/' . $defaultLang );
				$this->diffResult[$defaultLang] = new stdClass();
				//$this->diffResult[$defaultLang]->name = 'uncomplete';
				$defaultTmp = $lang;
				unset( $lang );
				$lang = array();
				// Include Translated Language File
				if (file_exists(  LANGDIR . '/' . $custom_Lang .  '/' . $defaultLang)) {
					include( LANGDIR . '/' . $custom_Lang .  '/' . $defaultLang );
					$customTmp = $lang;
					unset( $lang );
					$lang = array();	
					
					if( count( array_diff_key( $defaultTmp, $customTmp ) ) > 0 )
					{
						// $this->diffArray[ $defaultLang ]->mised = array_diff_key( $defaultTmp, $customTmp );
						$this->diffResult[$defaultLang]->missed = array_diff_key( $defaultTmp, $customTmp );
						$this->diffResult[$defaultLang]->status = 'pending';
					}					
					else
					{
						$this->diffResult[$defaultLang]->status = 'active';
						continue;
					}
					
				} else
					{
						$this->diffResult[$defaultLang]->status = 'inactive';
						continue;
					}
		
			}
		}
		return $this->diffResult;

	}	
	
}
?>