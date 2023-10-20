<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtencionPresencial_Finalizar_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        global $current_user;
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        if($current_user->id != $recordModel->get('assigned_user_id')){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
        return true;
    }

    public function process(Vtiger_Request $request) {
        global $current_user,$log;
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $actualizar = true;
        if ($request->get('detailajax')) {
            $actualizar = false;
        }
        if($actualizar){
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $recordModel->set('ap_estado', 'Finalizado');
            $recordModel->set('mode', 'edit');
            $recordModel->save();
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $fecha = new DateTimeField($recordModel->get('ap_fechafin'));
        //$log->info($recordModel);
        $fechalabel = $fecha->getDisplayDateTimeValue();
        $response = new Vtiger_Response();
        $log->info("show the hora".$fechalabel);
        $response->setResult(array('success' => true, 'status' => $recordModel->get('ap_estado'), 'hora' => $fechalabel));
        $response->emit();      
    }
}
?>