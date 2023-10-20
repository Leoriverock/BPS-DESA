<?php

class LPTempCamposSeleccion_Views_Helper {
    
    /**
	 * Posibles campos UIType 15, 16 o 33
	 * de cualquier modulo no configurable
	 */
	public static function get_posibles_ts_campo() {

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
				vtiger_lptempcampos ON f.fieldid = tc_campo
			WHERE
				uitype IN (15 , 16, 33)
					AND name NOT IN ('LPTempFlujos' , 'LPTempFlujoCambios',
					'LPTempCampos',
					'LPTempCamposDetalle',
					'LPTempCamposSeleccion')
			GROUP BY lptempcamposid , f.fieldid
			ORDER BY f.tabid"
			
		);

		foreach ($resultado as $r) {

			$traduccion = vtranslate($r['fieldlabel'], $r['name']);
			$traduccion = "$r[fieldid]: $traduccion";

			$retorno[$r['lptempcamposid']][] = array( // ID DEL TEMPLATE
				
				'value' => $r['fieldid'], // NOMBRE DEL CAMPO
				'traduccion' => $traduccion, // TRADUCCION DEL CAMPO

				'value_modulo' => $r['name'], // NOMBRE Y TRADUCCION DEL MODULO:
				'traduccion_modulo' => vtranslate($r['name'], $r['name']),
			
			);

		}

		return $retorno;

	}

	/**
	 * Posibles ts_valor de los posibles ts_campo
	 */
	public static function get_posibles_ts_valor() {

		global $adb, $log;

		$retorno = array();

		$resultado_campos = $adb->query(

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
				uitype IN (15 , 16, 33)
					AND name NOT IN ('LPTempFlujos' , 'LPTempFlujoCambios',
					'LPTempCampos',
					'LPTempCamposDetalle',
					'LPTempCamposSeleccion')
			GROUP BY f.fieldid
			ORDER BY f.tabid"

		);

		foreach ($resultado_campos as $c) {
			
			try {
				
				$resultado_valores = $adb->query(
					"SELECT $c[fieldname] FROM vtiger_$c[fieldname]
					GROUP BY $c[fieldname]"
				);
				
				if (!$retorno[$c['fieldid']]) {
					$retorno[$c['fieldid']] = array();
				}
				
				foreach ($resultado_valores as $v) {
				
					$retorno[$c['fieldid']][] = array(
						'value' => $v[$c['fieldname']],
						'traduccion' => vtranslate($v[$c['fieldname']], $c['name'])
					);
				
				}
			
			} catch (Exception $e) {

				$log->debug($e->getMessage());

			}
		
		}

		return $retorno;

	}

}