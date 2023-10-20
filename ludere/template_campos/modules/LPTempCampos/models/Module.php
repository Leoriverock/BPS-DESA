<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class LPTempCampos_Module_Model extends Vtiger_Module_Model {

	// Permitir solo agregar registros
	public function getModuleBasicLinks() {
		
		$createPermission = Users_Privileges_Model::isPermitted($this->getName(), 'CreateView');

		$basicLinks = array();

		if ($createPermission) {
			
			$basicLinks[] = array(
				'linktype' => 'BASIC',
				'linklabel' => 'LBL_ADD_RECORD',
				'linkurl' => $this->getCreateRecordUrl(),
				'linkicon' => 'fa-plus'
			);
			
		}
		$importPermission = Users_Privileges_Model::isPermitted($this->getName(), 'Import');
		if($importPermission && $createPermission) {
			$basicLinks[] = array(
				'linktype' => 'BASIC',
				'linklabel' => 'LBL_IMPORT',
				'linkurl' => 'index.php?module=LPTempFlujos&view=Import',
				'linkicon' => 'fa-download'
			);
		}
		
		return $basicLinks;
	
	}

	// No permitir la vista de creacion rapida
	public function isQuickCreateSupported() {
		return false;
	}
	
}
