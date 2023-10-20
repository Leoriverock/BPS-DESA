<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class LPTempCampos_Edit_View extends Vtiger_Edit_View {

	// //////////////////////////////////////////////////////////
	// Padre: Se utilizan todas las private y protected functions
	// //////////////////////////////////////////////////////////

	public function checkPermission(Vtiger_Request $request) {

        if (get_class($this) == 'LPTempCampos_Edit_View') {

			if ($request->get('record')) {
				throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
			}

		}

    }

	public function process(Vtiger_Request $request) {

		global $adb;
		
		$moduleName = $request->getModule();
		$recordId = $request->get('record');
        $recordModel = $this->record;
        
		if (!$recordModel) {
        
			if (!empty($recordId)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            } else {
                $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            }
        
		}

		$viewer = $this->getViewer($request);

		$viewer->assign('POSIBLES_TC_CAMPO', $this->get_posibles_tc_campo());
		$viewer->assign('POSIBLES_TCD_CAMPO', $this->get_posibles_tcd_campo());
		$viewer->assign('POSIBLES_TCD_TEMPLATE', $this->get_posibles_tcd_template());

		parent::process($request);

	}

	/**
	 * Campos UIType 15, 16 o 33 de cada modulo
	 * (excepto los de la configuracion)
	 */
	private function get_posibles_tc_campo() {

		global $adb, $log;

		$retorno = array(); // Lo que se retornaria normalmente
		$elegibles = array(); // Lo que es realmente elegible
		
		// Todos los posibles campos:
		$resultado_todos = $adb->query(
			
			"SELECT 
				f.tabid, name, fieldlabel, fieldname, columnname, fieldid
			FROM
				vtiger_field AS f
					JOIN
				vtiger_tab AS t ON f.tabid = t.tabid
			WHERE
				uitype IN (15 , 16, 33)
					AND name NOT IN ('LPTempFlujos' , 'LPTempFlujoCambios',
					'LPTempCampos',
					'LPTempCamposDetalle',
					'LPTempCamposSeleccion')
			ORDER BY f.tabid"
			
		);

		// Campos usados en los templates:
		$resultado_asignados = $adb->query(
			
			"SELECT 
				f.tabid, name, fieldlabel, fieldname, columnname, fieldid
			FROM
				vtiger_field AS f
					JOIN
				vtiger_tab AS t ON f.tabid = t.tabid
					JOIN
				vtiger_lptempcampos ON fieldid = tc_campo AND name = tc_modulo
					JOIN
				vtiger_crmentity ON lptempcamposid = crmid AND deleted = 0
			WHERE
				uitype IN (15 , 16, 33)
					AND name NOT IN ('LPTempFlujos' , 'LPTempFlujoCambios',
					'LPTempCampos',
					'LPTempCamposDetalle',
					'LPTempCamposSeleccion')
			GROUP BY name"
			
		);

		// Obtener toda la informacion:
		foreach ($resultado_todos as $r) {

			$traduccion = vtranslate($r['fieldlabel'], $r['name']);
			$traduccion = "$r[fieldid]: $traduccion";

			$retorno[$r['name']][] = array( // NOMBRE DEL MODULO
				'value' => $r['fieldid'], // NOMBRE DEL CAMPO
				'traduccion' => $traduccion // TRADUCCION DEL CAMPO
			);

		}

		// Quitar lo que no corresponda:
		foreach ($resultado_asignados as $s) {
			
			foreach ($retorno as $name => $data) {
				
				// si es para el mismo modulo...
				if ($s['name'] == $name) {
					
					// ... obtener todos los datos...
					foreach ($data as $i => $d) {

						// ... y si hay otro campo quitarlo:
						
						if ($s['fieldid'] <> $d['value']) {
						
							/*

								Entonces los campos resultantes,
								seran los unicos que se puedan elegir.

								Por lo tanto nunca se podra crear un template,
								para un campo distinto al de los ya creados.

							*/

							unset($retorno[$name][$i]); // QUITAR.

						}
					
					}
					
				}

			}

		}

		// Agregar lo que corresponda
		foreach ($retorno as $name => $data) {

			// (se corrigen los indices)

			foreach ($data as $d) {
				$elegibles[$name][] = $d;
			}

		}

		return $elegibles;

	}

	/**
	 * Todos los pibles campos de los modulos
	 * (excepto los de la configuracion)
	 */
	protected function get_posibles_tcd_campo() {

		global $adb;

		$retorno = array();
		
		$resultado = $adb->query(
			
			"SELECT 
				f.tabid,
				name,
				fieldlabel,
				fieldname,
				columnname,
				fieldid,
				lptempcamposid
			FROM
				vtiger_field AS f
					JOIN
				vtiger_tab AS t ON f.tabid = t.tabid
					JOIN
				vtiger_lptempcampos ON name = tc_modulo
			WHERE
				name NOT IN ('LPTempFlujos' , 'LPTempFlujoCambios',
					'LPTempCampos',
					'LPTempCamposDetalle',
					'LPTempCamposSeleccion')
			ORDER BY f.tabid"
			
		);

		foreach ($resultado as $r) {

			$traduccion = vtranslate($r['fieldlabel'], $r['name']);
			$traduccion = "$r[fieldid]: $traduccion";

			$retorno[$r['lptempcamposid']][] = array( // ID DEL TEMPLATE
				'value' => $r['fieldid'], // NOMBRE DEL CAMPO
				'traduccion' => $traduccion // TRADUCCION DEL CAMPO
			);

		}

		return $retorno;

	}

	/**
	 * Todos los posibles templates
	 * (por si se quiere transoformar a picklist)
	 */
	protected function get_posibles_tcd_template() {

		global $adb;

		$retorno = array();

		$resultado = $adb->query("SELECT 
			lptempcamposid, tc_nombre, tc_modulo, tc_campo
		FROM
			vtiger_lptempcampos
				JOIN
			vtiger_crmentity ON lptempcamposid = crmid AND NOT deleted
		ORDER BY lptempcamposid");
		
		foreach ($resultado as $r) {
			
			$retorno[] = array(
				'lptempcamposid' => $r['lptempcamposid'],
				'tc_nombre' => $r['tc_nombre'],
				'tc_modulo' => $r['tc_modulo'],
				'tc_campo' => $r['tc_campo'],
			);
		
		}

		return $retorno;

	}

}