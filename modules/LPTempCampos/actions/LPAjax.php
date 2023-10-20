<?php


class LPTempCampos_LPAjax_Action extends Vtiger_Action_Controller {
        
    function __construct(){
        parent::__construct();
        $Methods = array('json_import');        
        foreach ($Methods AS $method){
            $this->exposeMethod($method);
        }
    }

    function checkPermission(Vtiger_Request $request) {
		return true;
    }
    
    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if(!empty($mode)) {
                $this->invokeExposedMethod($mode, $request);
                return;
        }
    }

    /**
     * si retorna no_pasa_validacion en true, la validacion falla
     * en ese caso indica un mensaje y un titulo para el error
     */
    function validaciones_importacion_json($templates) {
        global $adb;
        $no_pasa_validacion = false;
        $message = ""; // mensaje de error
        $title = ""; // mensaje de error
        $modulos_campo = array();
        // buscar losuqe ya esten en el crm
        foreach($adb->pquery(
            "SELECT DISTINCT fieldname, tc_modulo
            FROM vtiger_lptempcampos 
            JOIN vtiger_crmentity ON crmid=lptempcamposid AND NOT deleted
            JOIN vtiger_field f ON f.fieldid = tc_campo
            JOIN vtiger_tab t ON t.name = tc_modulo AND f.tabid = t.tabid
            ", array()) as $encrm) {
            $modulo = $encrm['tc_modulo'];
            $campo = $encrm['fieldname'];
            $modulos_campo[$modulo] = $campo;
        }
        // ver si alguno de los que se quiere ingresar viene para campos distintos
        foreach ($templates as $temp) {
            $modulo = $temp['tc_modulo'];
            $campo = $temp['fieldname'];
            if (!$modulos_campo[$modulo]) $modulos_campo[$modulo] = $campo;
            if ($modulos_campo[$modulo] <> $campo) {                
                $no_pasa_validacion = true;
                $message = "En el modulo " . vtranslate($modulo, $modulo) . 
                            " se estan utilizando distintos campos para seleccion (".
                                vtranslate($campo, $modulo). ", " . vtranslate($modulos_campo[$modulo], $modulo). ")";
                $title = "Campos desiguales";
                break;
            }
        }      
        
        return array(
            "no_pasa_validacion" => $no_pasa_validacion,
            "message" => $message,
            "title" => $title,
        );
    }

    function json_import(Vtiger_Request $request) {
        $campos = $request->get('campos');
        // validaciones
        $response = new Vtiger_Response();
        $resultado_validacion = $this->validaciones_importacion_json($campos);
        if ($resultado_validacion["no_pasa_validacion"]) {
            $response->setError(
                $resultado_validacion["title"], 
                $resultado_validacion["message"], 
                $resultado_validacion["title"]
            );
            $response->emit();
            return;
        }
        // pasa validaciones
        global $adb;
        $fieldsSQL = "SELECT f.fieldname, f.fieldid, t.name 
            FROM vtiger_field f
            JOIN vtiger_tab t ON t.tabid=t.tabid";
        $fields_trans = array();
        foreach ($adb->pquery($fieldsSQL) as $field_translate) 
            $fields_trans["$field_translate[name]_$field_translate[fieldname]"] = $field_translate['fieldid'];
        
        foreach ($campos as $campo) {
            $entity_campo = Vtiger_Record_Model::getCleanInstance('LPTempCampos');
            $entity_campo->set('tc_nombre', $campo['tc_nombre']);
            $entity_campo->set('tc_modulo', $campo['tc_modulo']);
            $entity_campo->set('tc_campo', $fields_trans["$campo[tc_modulo]_$campo[fieldname]"] );
            $entity_campo->set('mode', '');
            $entity_campo->save();
            $crmid = $entity_campo->getId();
            foreach($campo['selections'] as $selections) {
                $entity_selection = Vtiger_Record_Model::getCleanInstance('LPTempCamposSeleccion');
                $entity_selection->set('ts_valor', $selections['ts_valor']);
                $entity_selection->set('ts_modulo', $campo['tc_modulo']);
                $entity_selection->set('ts_campo', $fields_trans["$campo[tc_modulo]_$campo[fieldname]"] );
                $entity_selection->set('ts_template', $crmid);
                $entity_selection->set('mode', '');
                $entity_selection->save();
            }
            foreach($campo['fields'] as $fields) {
                $entity_field = Vtiger_Record_Model::getCleanInstance('LPTempCamposDetalle');
                $entity_field->set('tcd_campo', $fields_trans["$campo[tc_modulo]_$campo[fieldname]"] );
                $entity_field->set('tcd_obligatorio', $fields['tcd_obligatorio']);
                $entity_field->set('tcd_orden', $fields['tcd_orden']);
                $entity_field->set('tcd_template', $crmid);
                $entity_field->set('mode', '');
                $entity_field->save();
            }
        }
        $response->setResult(array(
            'flujos' => $campos,
        ));
        $response->emit();
    }
}