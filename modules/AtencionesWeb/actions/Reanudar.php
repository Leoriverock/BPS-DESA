<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtencionesWeb_Reanudar_Action extends Vtiger_Action_Controller {

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
        global $current_user, $adb, $log;
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        //Si hay atencion activa no deja reanudar

        $aw_activa = 'SELECT 1 FROM vtiger_atencionesweb a INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0 WHERE a.aw_estado = ? AND e.smownerid = ?' ;
        $result = $adb->pquery($aw_activa,array('Asignado',$current_user->id));
        $asignado = $adb->num_rows($result);

        if ($asignado == 0){
            $actualizar = true;
            if ($request->get('detailajax')) {
                $actualizar = false;
            }
            if($actualizar){
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
                $recordModel->set('aw_estado', 'Asignado');
                $recordModel->set('mode', 'edit');
                $recordModel->save();
            }
            
            //$fecha = new DateTimeField($recordModel->get('aw_fechafin'));
            //$fechalabel = $fecha->getDisplayDateTimeValue();
        }

        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        
        $response = new Vtiger_Response();
        $response->setResult(array('success' => true, 'status' => $recordModel->get('aw_estado')));
        $response->emit();      
    }
}
?>