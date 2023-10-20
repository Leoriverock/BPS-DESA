<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtencionesWeb_getEstado_Action extends Vtiger_Action_Controller {

   
    public function process(Vtiger_Request $request) {
        global $current_user,$log,$adb;
        $log->info("entra a AtencionesWeb_getEstado_Action ");
        $moduleName = $request->getModule();
        $recordId = $request->get('id');
       
        $sql = "SELECT aw_estado FROM vtiger_atencionesweb WHERE atencioneswebid = ?";
        $result = $adb->pquery($sql, array($recordId));
        $aw_estado = $adb->query_result($result, 0, 'aw_estado');
        
        $response = new Vtiger_Response();
        $response->setResult(array('success' => true, 'estado' => $aw_estado));
        $response->emit();      
    }
}
?>