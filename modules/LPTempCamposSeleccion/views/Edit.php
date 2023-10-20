<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class LPTempCamposSeleccion_Edit_View extends Vtiger_Edit_View {

	public function process(Vtiger_Request $request) {

		global $adb;
		
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
        $recordModel = $this->record;
        
		if (!$recordModel) {
        
			if (!empty($recordId)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            } else {
                $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            }
        
		}

		$viewer = $this->getViewer($request);

		$viewer->assign('POSIBLES_TS_CAMPO', LPTempCamposSeleccion_Views_Helper::get_posibles_ts_campo());
		$viewer->assign('POSIBLES_TS_VALOR', LPTempCamposSeleccion_Views_Helper::get_posibles_ts_valor());

		// Valores actuales que tiene el ts_valor para cuando se abre por primera vez la vista de edicion:
		$viewer->assign('ACTUALES_TS_VALOR', json_encode(explode(" |##| ", $recordModel->get('ts_valor'))));

		parent::process($request);

	}

}