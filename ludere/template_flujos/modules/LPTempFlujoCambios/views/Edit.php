<?php

Class LPTempFlujoCambios_Edit_View extends Vtiger_Edit_View {

	public function process(Vtiger_Request $request) {
		global $adb;
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
        $recordModel = $this->record;
        if(!$recordModel){
            if (!empty($recordId)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            } else {
                $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            }
        }
		// buscar distintos templates disponibles
		$templatesq = $adb->query("SELECT lptempflujosid, tf_nombre, tf_modulo, tf_campo_mod
			FROM vtiger_lptempflujos
			JOIN vtiger_crmentity ON lptempflujosid=crmid AND NOT deleted
			ORDER BY lptempflujosid");
		$templates = array();
		foreach($templatesq as $t) {
			$templates[] = array(
				'lptempflujosid' => $t['lptempflujosid'],
				'tf_nombre' => $t['tf_nombre'],
				'tf_modulo' => $t['tf_modulo'],
				'tf_campo_mod' => $t['tf_campo_mod'],
			);
		}
		$campos_valores = array();
		// buscar los valores para cada campo
		$query_campos = $adb->query("SELECT f.tabid, name, fieldlabel, fieldname,columnname, fieldid
			FROM vtiger_field f
			JOIN vtiger_tab t ON f.tabid=t.tabid
			WHERE uitype IN (15,16,33) AND name NOT IN ('LPTempFlujos', 'LPTempFlujoCambios')
			ORDER BY f.tabid");

		foreach($query_campos as $c) {
			try{
				$query_valores = $adb->query("SELECT $c[fieldname] FROM vtiger_$c[fieldname]");
				if (!$campos_valores[$c['fieldname']]) 
					$campos_valores[$c['fieldname']] = array();
				foreach($query_valores as $v) {
					$campos_valores[$c['fieldname']][]=array(
						'value'=>$v[$c['fieldname']],
						'traduccion' => vtranslate($v[$c['fieldname']], $c['name'])
					);
				}
			} catch(Exception $e){ }
		}

		$viewer = $this->getViewer($request);
		$viewer->assign('TEMPLATES', $templates);
		$viewer->assign('CAMPOS_VALORES', $campos_valores);
		parent::process($request);
	}

}