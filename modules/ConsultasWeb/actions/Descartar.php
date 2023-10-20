<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class ConsultasWeb_Descartar_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
    }

    public function process(Vtiger_Request $request) {
        global $current_user,$log;
        $moduleName = $request->getModule();
        $log->info("modulo :");
        $log->info($moduleName);
        $recordId = $request->get('id');
        $actualizar = true;
        if ($request->get('detailajax')) {
            $actualizar = false;
        }
        if($actualizar){
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $recordModel->set('cw_estado', 'Descartada');
            $recordModel->set('mode', 'edit');
            $recordModel->save();
        }
        
      echo json_encode($contenido);     
    }
}
?>