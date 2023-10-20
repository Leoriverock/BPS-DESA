<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_getTituloHelpDeskRel_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb, $current_user;

		$usuario_logueado = $current_user->id;

		$log->debug("entre al process de HelpDesk_getTituloHelpDeskRel_Action");

		$id = $request->get('id_email');
		$log->info("muestra el id del mail");
		$log->info($id);

		$sql = "SELECT subject titulo FROM vtiger_activity
				WHERE activityid = ?";
		$result = $adb->pquery($sql, array($id));
		$titulo = $adb->query_result($result, 0, 'titulo');

		$log->info("titulo: ".$titulo);
		$log->info("id: ".$id);

		echo json_encode(strtoupper($titulo));

		
	}

}
?>