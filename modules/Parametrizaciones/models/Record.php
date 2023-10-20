<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Parametrizaciones_Record_Model extends Vtiger_Record_Model {

	public static function getFromTema($tema, $motivo, $origen = false){
		global $adb;
		$params = array($motivo);
		$sql = "SELECT crmid FROM vtiger_parametrizaciones p INNER JOIN vtiger_crmentity e ON e.crmid = p.parametrizacionesid AND e.deleted = 0 WHERE pt_motivo = ? ";
		if($origen){
			$params[] = $origen;
			$sql .= "AND pt_origen = ? " ;
		}
		if($tema){
			$params[] = $tema;
			$sql .= "AND pt_tema = ? " ;
		}
		$rs = $adb->pquery($sql, $params);
		$archivo = fopen('testBuzon.txt', 'a');
		fwrite($archivo, 'rs => '.var_export($rs->fields,true) . PHP_EOL);
		if($adb->num_rows($rs) > 0){
			$id = $rs->fields[0];
			return parent::getInstanceById($id, 'Parametrizaciones');
		}
		return null;
	}
}
