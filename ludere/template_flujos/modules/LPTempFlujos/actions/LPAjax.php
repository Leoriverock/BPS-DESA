<?php


class LPTempFlujos_LPAjax_Action extends Vtiger_Action_Controller {
        
    function __construct(){
        parent::__construct();
        $Methods = array('verificarFlujos', 'ejecutarFlujo', 'editar', 'json_import', 'getFieldLabels');        
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
    function validaciones_importacion_json($flujos) {
        global $adb;
        $no_pasa_validacion = false;
        $message = ""; // mensaje de error
        $title = ""; // mensaje de error
        $modulos_campo = array();
        $modulos_campo_mod = array();
        $modulos_valor = array();
        $traducciones = $this->getAllTranslations();
        foreach ($flujos as $flujo) {
            $modulo = $flujo['tf_modulo'];
            $valor_actual = $flujo['tf_valor'];
            // verificar entre los flujos que se estan insertando
            $previo_modulo_campo = $modulos_campo[$modulo];
            $previo_modulo_campo_mod = $modulos_campo_mod[$modulo];
            $previo_modulo_valores = $modulos_valor[$modulo];
            $flujos_query = $adb->pquery(
                "SELECT tf_campo, tf_campo_mod, tf_valor, tf_nombre
                FROM vtiger_lptempflujos 
                JOIN vtiger_crmentity ON crmid=lptempflujosid AND NOT deleted
                WHERE tf_modulo = ? ", array($modulo));
            // verificar entre lo que se inserta que el campo no se repita, para el mismo modulo
            if (!!$previo_modulo_campo &&  $previo_modulo_campo <> $flujo['tf_campo']) {
                $no_pasa_validacion = true;
                $message = "Para el modulo " . vtranslate($modulo, $modulo) . 
                            " se estan utilizando distintos campos a evaluar (".
                                vtranslate($previo_modulo_campo, $modulo). ", " . vtranslate($flujo['tf_campo'], $modulo). ")";
                $title = "Campos desiguales";
                break;
            }
            $modulos_campo[$modulo] = $flujo['tf_campo'];
            // verificar entre lo que se inserta que el campo_mod no se repita, para el mismo modulo
            if (!!$previo_modulo_campo_mod &&  $previo_modulo_campo_mod <> $flujo['tf_campo_mod']) {
                $no_pasa_validacion = true;
                $message = "Para el modulo " . vtranslate($modulo, $modulo) . 
                            " se estan utilizando distintos campos a modificar (".
                                vtranslate($previo_modulo_campo_mod, $modulo). ", " . vtranslate($flujo['tf_campo'], $modulo). ")";
                $title = "Campos a modificar desiguales";
                break;
            }
            $modulos_campo_mod[$modulo] = $flujo['tf_campo_mod'];
            if (!$previo_modulo_valores || !is_array($previo_modulo_valores)){
                // no se compararon otros valores aun para este modulo
                $modulos_valor[$modulo] = array($valor_actual);
            } else {
                // ya hay otros valores que se quieren insertar
                if (in_array($valor_actual, $previo_modulo_valores)) {
                    $no_pasa_validacion = true;
                    $message = "Se esta duplicando el flujo para el modulo " .  vtranslate($modulo, $modulo) . 
                                "(".vtranslate($flujo['tf_campo_mod'], $modulo). ", " .
                                    vtranslate($flujo['tf_campo'], $modulo). "): ". vtranslate($valor_actual, $modulo);
                    $title = "Flujos duplicados";
                    break;
                }
                // agregar para proximas comparaciones
                $modulos_valor[$modulo][] = $valor_actual;
            }
            // ahora comparar con los flujos ya existentes en el crm para este modulo
            foreach ($flujos_query as $flujos_crm) {
                if ($flujos_crm['tf_campo'] <> $flujo['tf_campo']) {
                    $no_pasa_validacion = true;
                    $message = "Para el modulo " . vtranslate($modulo, $modulo) . 
                                " se estan utilizando distintos campos a evaluar, con los que ya estan en el crm (".
                                    vtranslate($flujos_crm['tf_campo'], $modulo). ", " . vtranslate($flujo['tf_campo'], $modulo). ")";
                    $title = "Campos desiguales";
                    break;
                }
                if ($flujos_crm['tf_campo_mod'] <> $flujo['tf_campo_mod']) {
                    $no_pasa_validacion = true;
                    $message = "Para el modulo " . vtranslate($modulo, $modulo) . 
                                " se estan utilizando distintos campos a modificar, con los que ya estan en el crm (".
                                    vtranslate($flujos_crm['tf_campo_mod'], $modulo). ", " . vtranslate($flujo['tf_campo_mod'], $modulo). ")";
                    $title = "Campos desiguales";
                    break;
                }
                if ($flujos_crm['tf_valor'] == $flujo['tf_valor']) {
                    $no_pasa_validacion = true;
                    $message = "Se esta duplicando el flujo para el modulo " .  vtranslate($modulo, $modulo) . 
                                "(".vtranslate($flujo['tf_campo_mod'], $modulo). ", " .
                                    vtranslate($flujo['tf_campo'], $modulo). "): ". vtranslate($valor_actual, $modulo);
                    $title = "Flujos duplicados";
                    break;
                }
            }
        }
        return array(
            "no_pasa_validacion" => $no_pasa_validacion,
            "message" => $message,
            "title" => $title,
        );
    }
    function json_import(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $flujos = $request->get('flujos');
        // validaciones
        $resultado_validacion = $this->validaciones_importacion_json($flujos);
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
        foreach ($flujos as $flujo) {
            $entity_flujo = Vtiger_Record_Model::getCleanInstance('LPTempFlujos');
            $entity_flujo->set('tf_campo', $flujo['tf_campo']);
            $entity_flujo->set('tf_campo_mod', $flujo['tf_campo_mod']);
            $entity_flujo->set('tf_modulo', $flujo['tf_modulo']);
            $entity_flujo->set('tf_nombre', $flujo['tf_nombre']);
            $entity_flujo->set('tf_valor', $flujo['tf_valor']);
            $entity_flujo->set('mode', '');
            $entity_flujo->save();
            $crmid = $entity_flujo->getId();
            foreach($flujo['changes'] as $cambios) {
                $entity_cambio = Vtiger_Record_Model::getCleanInstance('LPTempFlujoCambios');
                $entity_cambio->set('tfc_color', $cambios['tfc_color']);
                $entity_cambio->set('tfc_comentario', $cambios['tfc_comentario']);
                $entity_cambio->set('tfc_destino', $cambios['tfc_destino']);
                $entity_cambio->set('tfc_etiqueta', $cambios['tfc_etiqueta']);
                $entity_cambio->set('tfc_origen', $cambios['tfc_origen']);
                $entity_cambio->set('tfc_paracrm', $cambios['tfc_paracrm']);
                $entity_cambio->set('tfc_paraportal', $cambios['tfc_paraportal']);
                $entity_cambio->set('tfc_template', $crmid);
                $entity_cambio->set('mode', '');
                $entity_cambio->save();
            }
        }
        $response->setResult(array(
            'flujos' => $flujos,
        ));
        $response->emit();
    }
    function editar(Vtiger_Request $request) {
        $recordid = $request->get('recordid');
        $editar = $request->get('editar');
        $borrar = $request->get('borrar');
        if (!!$borrar && is_array($borrar)) {
            foreach($borrar as $id){
                $entity = Vtiger_Record_Model::getInstanceById($id, "LPTempFlujoCambios");
                $entity->delete();
            }
        }
        if (!!$editar && is_array($editar)) {
            foreach($editar as $flujo) {
                if (!empty($flujo['id'])) {
                    $entity = Vtiger_Record_Model::getInstanceById($flujo['id'], "LPTempFlujoCambios");
                    $entity->set("mode", "edit");
                } else {
                    $entity = Vtiger_Record_Model::getCleanInstance("LPTempFlujoCambios");
                    $entity->set("mode", "");
                }
                $entity->set("tfc_template", $recordid);
                foreach($flujo as $k => $v){
                    if ($k <> "id") $entity->set($k, $v);
                }
                $entity->save();
            }
        }
        $response = new Vtiger_Response();
        $response->setResult(array(
            'editar' => $editar,
            'borrar' => $borrar,
        ));
        $response->emit();

    }
    function ejecutarFlujo(Vtiger_Request $request) {
        global $adb;
        $source_module = $request->get('source_module');
        $recordid = $request->get('recordid');
        $flujoid = $request->get('flujo');
        $record = Vtiger_Record_Model::getInstanceById($recordid, $source_module);
        $flujo = $adb->pquery("SELECT tf_campo_mod, tfc_destino
        FROM vtiger_lptempflujocambios
        JOIN vtiger_lptempflujos ON tfc_template=lptempflujosid
        WHERE lptempflujocambiosid = ? LIMIT 1", array($flujoid));
        if($adb->num_rows($flujo)) {
            $tf_campo_mod = $adb->query_result($flujo, 0, 'tf_campo_mod');
            $tfc_destino = $adb->query_result($flujo, 0, 'tfc_destino');
            $record->set("mode", "edit");
            $record->set($tf_campo_mod, $tfc_destino);
            $record->save();
        }
        $response = new Vtiger_Response();
        $response->setResult(array("OK"));
        $response->emit();
    }
    function verificarFlujos(Vtiger_Request $request) {
        global $adb;
        $source_module = $request->get('source_module');
        $recordid = $request->get('recordid');
        $record = Vtiger_Record_Model::getInstanceById($recordid, $source_module);
        $templatesq = $adb->pquery(
            "SELECT lptempflujosid, tf_campo, tf_valor, tf_campo_mod
            FROM vtiger_lptempflujos
            JOIN vtiger_crmentity ON crmid=lptempflujosid AND NOT deleted");
        $opciones = null;
        if($adb->num_rows($templatesq) > 0) {
            $opciones = array(
                "tf_campo" => null,
            );
            foreach($templatesq as $t) {
                $value = $record->get($t['tf_campo']);
                if ($value == $t['tf_valor']) {
                    $lptempflujosid = $t['lptempflujosid'];
                    $cambiosq = $adb->pquery(
                        "SELECT * 
                        FROM vtiger_lptempflujocambios
                        JOIN vtiger_crmentity ON crmid=lptempflujocambiosid AND NOT deleted
                        WHERE tfc_template = ?", array($lptempflujosid));
                    $opciones['tf_campo'] = $t['tf_campo'];
                    $opciones['tf_valor'] = $t['tf_valor'];
                    $opciones['tf_campo_mod'] = $t['tf_campo_mod'];
                    $opciones['flujos'] = array();
                    foreach($cambiosq as $flujo) {
                        $opciones['flujos'][]=array(
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

        $response = new Vtiger_Response();
        $response->setResult($opciones);
        $response->emit();
    }
    private function getAllTranslations() {        
        global $adb;
        $fields = $adb->pquery(
            "SELECT f.fieldname, f.fieldlabel, t.name
            FROM vtiger_field f
            JOIN vtiger_tab t ON f.tabid=t.tabid",
            array()
        );
        $translations = array();
        foreach($fields as $f) {
            if (!$translations[$f['name']]) 
                $translations[$f['name']] = array(
                    "__VTIGER_TRANSLATE" => vtranslate($f['name'], $f['name']),
                );
            $translations[$f['name']][$f['fieldname']] = array(
                "label" => $f['fieldlabel'],
                "traduccion" => vtranslate($f['fieldlabel'], $f['name']),
            );
        }
        return $translations;
    }
    function getFieldLabels(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        $response->setResult($this->getAllTranslations());
        $response->emit();
    }
}