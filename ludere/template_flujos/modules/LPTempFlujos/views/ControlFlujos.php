<?php

/* * *******************************************************************************
 * The content of this file is subject to the PDF Maker Free license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class LPTempFlujos_ControlFlujos_View extends Vtiger_BasicAjax_View {

    public function process(Vtiger_Request $request) {
        global $adb, $log;
        $source_module = $request->get('source_module');
        $actual_view = $request->get('actual_view');
        $recordid = $request->get('recordid');
        $log->debug("LPTempFlujos_ControlFlujos_View $source_module - $actual_view - $recordid");
        if (!$recordid ) {
            $response = new Vtiger_Response();
            $response->setError(array("se necesita un id de record"));
            $response->emit();            
        }
        $record = Vtiger_Record_Model::getInstanceById($recordid, $source_module);
        $templatesq = $adb->pquery(
            "SELECT lptempflujosid, tf_campo, tf_valor, tf_campo_mod
            FROM vtiger_lptempflujos
            JOIN vtiger_crmentity ON crmid=lptempflujosid AND NOT deleted
            WHERE tf_modulo = ?", array($source_module));
        $opciones = null;
        $viewer = $this->getViewer($request);
        if($adb->num_rows($templatesq) > 0) {
            $opciones = array(
                "tf_campo" => null,
            );
            foreach($templatesq as $t) {
                $value = $record->get($t['tf_campo']);
                $value_mod = $record->get($t['tf_campo_mod']);
                if ($value == $t['tf_valor']) {
                    $lptempflujosid = $t['lptempflujosid'];
                    $cambiosq = $adb->pquery(
                        "SELECT * 
                        FROM vtiger_lptempflujocambios
                        JOIN vtiger_crmentity ON crmid=lptempflujocambiosid AND NOT deleted
                        WHERE tfc_template = ? AND tfc_origen=?", array($lptempflujosid, $value_mod));
                    $opciones['tf_campo'] = $t['tf_campo'];
                    $opciones['tf_valor'] = $t['tf_valor'];
                    $opciones['tf_campo_mod'] = $t['tf_campo_mod'];
                    $opciones['source_module'] = $source_module;
                    $opciones['flujos'] = array();
                    $viewer->assign('TF_CAMPO_MOD', $t['tf_campo_mod']);
                    foreach($cambiosq as $flujo) {
                        if (intVal($flujo['tfc_paracrm']) == 1) // solo mostrar las opciones del crm
                            $opciones['flujos'][]=array(
                                "id" => $flujo['lptempflujocambiosid'],
                                "tfc_origen" => $flujo['tfc_origen'],
                                "tfc_destino" => $flujo['tfc_destino'],
                                "tfc_etiqueta" => $flujo['tfc_etiqueta'],
                                "tfc_color" => $flujo['tfc_color'],
                                "tfc_comentario" => $flujo['tfc_comentario'],
                            );
                    }
                }
            }
        }        
		$viewer->assign('OPCIONES', $opciones);
        if ($actual_view <> 'Detail') {
            $response = new Vtiger_Response();
            $response->setResult(array(
                "tf_campo_mod" => $t['tf_campo_mod']
            ));
            $response->emit();
        } else $viewer->view("ControlFlujos.tpl", 'LPTempFlujos');
    }
}

