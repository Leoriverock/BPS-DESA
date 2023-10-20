<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AtencionPresencial_AtenderAtencion_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        if(AtencionPresencial_Module_Model::getAtencionpPActiva()){
            throw new AppException('Ya tiene una atencion presencial activa');
        }
        return true;
    }

    /*public function process(Vtiger_Request $request) {
        global $current_user, $adb;
        $moduleName = $request->getModule();
        $result = array();
        $recordModel = null;
        $crear = $request->get('mode') == 'nueva';
        if($crear){
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $recordModel->set('ap_estado', 'Asignado');
            $recordModel->set('assigned_user_id', $current_user->id);
            include_once('config.ludere.php');
            $documento = $request->get('documento');
            $persona = null;
            if(!empty(trim($documento))){
                //$personaModel = Accounts_Record_Model::getInstanceBySearch(array('acccountry' => 1, 'accdocumenttype' => 'DO', 'accdocumentnumber' => trim($documento)));
                if($personaModel) $persona = $personaModel->getId();
            }
            if(!$persona) $persona = USER_DEFAULT;
            $recordModel->set('ap_persona', $persona);
            $recordModel->set('mode', 'create');
            $recordModel->save();
            $sql = "UPDATE vtiger_atencionpresencial SET ap_fechacomienzo = ?, ap_numerows =?, ap_sectorid = ?, ap_sector = ? WHERE atencionpresencialid = ?";
            $adb->pquery($sql, array($request->get('numerofecha').' '.$request->get('hora').':00', $request->get('numerocod'), $request->get('sectorId'), $request->get('sector'), $recordModel->getId()));
            $result = array('success' => true, 'url' => $recordModel->getDetailViewUrl());
        }else{
            $numero = $request->get('numerocod');
            $fecha = $request->get('numerofecha');
            $sector = $request->get('sectorId');
            $recordModel = AtencionPresencial_Record_Model::getAtencion($numero, $fecha, $sector);
            if($recordModel){
                $recordModel->set('assigned_user_id', $current_user->id);
                $recordModel->set('mode', 'edit');
                $recordModel->save();
                $result = array('success' => true, 'url' => $recordModel->getDetailViewUrl());
            }else{
                $result = array('success' => false, 'error' => 'No existe la atención');
            }
        }
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();      
    }*/

    public function process(Vtiger_Request $request) {
        global $adb, $current_user,$log;
        $user_query = "SELECT user_name usuario, equipo, lugcod FROM vtiger_users WHERE id = ? AND deleted = 0 ";
        $result = $adb->pquery($user_query,array($current_user->id) );
        $lugarcod = $adb->query_result($result, 0, 'lugcod');
        //$archivo = fopen('archivoRequest.txt', 'a');
        $datos = $request->getAll();
        $log->info("atender atenciones");
        $log->info($datos);
        unset($datos['__vtrftk']);
        unset($datos['module']);
        unset($datos['action']); 
        //fwrite($archivo, var_export($datos,true).PHP_EOL);
        //$lugarcod = $datos['lugarCod'];
        AtencionPresencial_Record_Model::statusLog($datos['numerocod'], $datos['sectorId'], $datos['numerofecha'], $lugarcod, json_encode($datos));
        $result = array('success' =>  true);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();  
    }
}
?>