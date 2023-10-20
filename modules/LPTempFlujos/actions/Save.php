<?php
class LPTempFlujos_Save_Action extends Vtiger_Save_Action {
    public function process(Vtiger_Request $request) {
        global $adb, $log;
        // realizar las valicaciones requeridas para los templates de flujos
        $record = $request->get("record");
        $tf_modulo = $request->get("tf_modulo");
        $tf_campo = $request->get("tf_campo");
        $tf_campo_mod = $request->get("tf_campo_mod");
        $tf_valor = $request->get("tf_valor");
        $recordcompare = "";
        $params = array();
        if ($record) { // el recor se compara solo cuando se edita, no al crear(no tiene sentido)
            $recordcompare = "lptempflujosid NOT IN (?) AND";
            $params[] = $record;
        }
        $params[]=$tf_modulo;
        $params[]=$tf_campo;
        $params[]=$tf_campo_mod;
        // no puede haber dos templates para el mismo modulo que trabajen con campos distintos
        $campo_distinto = $adb->pquery("SELECT lptempflujosid FROM vtiger_lptempflujos 
            JOIN vtiger_crmentity ON crmid=lptempflujosid AND NOT deleted
            WHERE $recordcompare tf_modulo = ? AND (tf_campo <> ? OR tf_campo_mod <> ?)", $params);
        if ($adb->num_rows($campo_distinto) > 0) {
            $message = 'EXITE PLANTILLA PARA EL MODULO CON OTROS CAMPOS';
			if ($request->isAjax()) {
                $response = new Vtiger_Response();
				$response->setError($message, $message, $message);
				$response->emit();
                // throw new AppException(vtranslate($message));
			} else {
				$viewer = new Vtiger_Viewer();
				$viewer->assign('MESSAGE', $message);
				$viewer->view('OperationNotPermitted.tpl', 'Vtiger');
			}
            return;
        } 
        $params = array();
        if ($record) { // el recor se compara solo cuando se edita, no al crear(no tiene sentido)
            $recordcompare = "lptempflujosid NOT IN (?) AND";
            $params[] = $record;
        }
        // no puede haber dos templates con los mismos valores
        $params[]=$tf_modulo;
        $params[]=$tf_valor;
        $campo_distinto = $adb->pquery("SELECT lptempflujosid 
            FROM vtiger_lptempflujos 
            JOIN vtiger_crmentity ON crmid=lptempflujosid AND NOT deleted
            WHERE $recordcompare tf_modulo = ? AND tf_valor = ?", $params);
        if ($adb->num_rows($campo_distinto) > 0) {
            $message = 'PLANTILLA DUPLICADA';
			if ($request->isAjax()) {
                $response = new Vtiger_Response();
				$response->setError($message, $message, $message);
				$response->emit();
                // throw new AppException(vtranslate($message));
			} else {
				$viewer = new Vtiger_Viewer();
				$viewer->assign('MESSAGE', $message);
				$viewer->view('OperationNotPermitted.tpl', 'Vtiger');
			}
            return;
        }
        $log->debug("PAAAAASSSSSSSSSSSSSSSSSSSSSSSAAAAA BIEN");
        return parent::process($request); // si pasa las dos validaciones se ejecuta el proceso normal de guardado
	}
}
