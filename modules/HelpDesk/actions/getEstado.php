<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_getEstado_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb,$current_user;

		$usuario_logueado = $current_user->id;
		$id = $request->get('id');
		$sql = "SELECT aw_de email FROM vtiger_atencionesweb a INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0 WHERE a.aw_estado = ? AND e.smownerid = ?";
		$result = $adb->pquery($sql, array('Asignado', $usuario_logueado));

		$respuesta = $adb->num_rows($result);
		
		$log->info("mostrame la respuesta");
		$log->info($respuesta);

		echo json_encode(strtolower($respuesta));

		
	}

}
?>