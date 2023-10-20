<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtencionPresencial_getPersona_Action extends Vtiger_Action_Controller {

   /* public function checkPermission(Vtiger_Request $request) {
        global $current_user;
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        if($current_user->id != $recordModel->get('assigned_user_id')){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
        return true;
    }*/

    public function process(Vtiger_Request $request) {
        global $current_user,$log,$adb;
        $log->info("erntra a AtencionPresencial_getPersona_Action ");
        $moduleName = $request->getModule();
        $recordId = $request->get('id');
       
        $sql = "SELECT DISTINCT ap_persona,accountname 
                FROM vtiger_atencionpresencial 
                INNER JOIN vtiger_account ON accountid = ap_persona 
                WHERE atencionpresencialid = ?";
        $result = $adb->pquery($sql, array($recordId));
        $ap_persona = $adb->query_result($result, 0, 'ap_persona');
        $nombre = $adb->query_result($result, 0, 'accountname');
        
        $response = new Vtiger_Response();
        $log->info("show the hora".$fechalabel);
        $response->setResult(array('success' => true, 'id' => $ap_persona, 'nombre' => $nombre));
        $response->emit();      
    }
}
?>