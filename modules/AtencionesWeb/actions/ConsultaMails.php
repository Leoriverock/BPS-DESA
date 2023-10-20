<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtencionesWeb_ConsultaMails_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        global $current_user;
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $permiso = $recordModel->esSupervisor();
        if($current_user->id != $recordModel->get('assigned_user_id') && !$permiso){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
        return true;
    }

    public function process(Vtiger_Request $request) {
        global $current_user;
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $permiso = $recordModel->esSupervisor();
        $status = $recordModel->get('aw_estado');
        $habilitado = true;
        $message = '';
        if($status == 'Asignado'){
            if(intval($recordModel->get('aw_mails')) == 0 || $current_user->id != $recordModel->get('assigned_user_id')){
                $habilitado = false;
                $message = 'Está inhabilitado para finalizar la atención, no se ha respondido la consulta';
            }
        }
        if($status == 'Pausado'){
            if (!$permiso) {
                $habilitado = false;
                $message = 'Está inhabilitado para finalizar la atención, no es supervisor';
            }
        }
        $response = new Vtiger_Response();
        $response->setResult(array('success' => true, 'habilitado' => $habilitado, 'message' => $message));
        $response->emit();      
    }
}
?>