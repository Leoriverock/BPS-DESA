<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class LPTempFlujos_QuickCreateAjax_View extends Vtiger_QuickCreateAjax_View {

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
		$query_campos = $adb->query("SELECT f.tabid, name, fieldlabel, fieldname,columnname, fieldid
			FROM vtiger_field f
			JOIN vtiger_tab t ON f.tabid=t.tabid
			WHERE uitype IN (15,16,33) AND name NOT IN ('LPTempFlujos', 'LPTempFlujoCambios')
			ORDER BY f.tabid");
		$modulos_campos=array();
		$campos_valores=array();
		foreach($query_campos as $c) {
			if (!$modulos_campos[$c['name']]) 
				$modulos_campos[$c['name']] = array();
			$modulos_campos[$c['name']][]=array(
				'fieldlabel' => $c['fieldlabel'],
				'fieldname' => $c['fieldname'],
				'columnname' => $c['columnname'],
				'fieldid' => $c['fieldid'],
				'traduccion' => vtranslate($c['fieldlabel'], $c['name'])
			);
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
		$viewer->assign('MODULOS_CAMPOS', $modulos_campos);
		$viewer->assign('CAMPOS_VALORES', $campos_valores);

		parent::process($request);
	}
	
}