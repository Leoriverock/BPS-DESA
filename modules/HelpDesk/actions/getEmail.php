<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_getEmail_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb,$current_user;

		$log->debug("entre al process de HelpDesk_getEmail_Action");
		$log->info($request);
		$usuario_logueado = $current_user->id;
		$id = $request->get('id');
		$id_email = $request->get('id_email');
		$log->info("mostrame lo que tiene id_email");
		$log->info($id_email);
		
		$sql = "SELECT aw_de email FROM vtiger_atencionesweb a INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0 WHERE a.aw_estado = ? AND e.smownerid = ?";
		$result = $adb->pquery($sql, array('Asignado', $usuario_logueado));
		$log->info("Hace la consulta");
		$log->info($usuario_logueado);

		$cant = $adb->num_rows($result);
		$cant_ = $adb->num_rows($result);

		if($cant == 0){

			$sql = "SELECT aw_de email FROM vtiger_atencionesweb a INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0 WHERE a.aw_estado = ? AND e.smownerid = ? Limit 1";
			$result = $adb->pquery($sql, array('Pausado', $usuario_logueado));
			
		}
		if($id_email != '' || $id_email != null){
			$sql_ = 'SELECT to_email email FROM vtiger_emaildetails
					 WHERE emailid = ? ';
					$result = $adb->pquery($sql_, array($id_email));
		}

		
		$email = $adb->query_result($result, 0, 'email');
		$email = str_replace(array('[',"]"), "", $email);
		$email = str_replace("&quot", "", $email);
		$email = str_replace(array(';',";"), "", $email);
		
		$log->info("Abuelaaa la la la ala");
		$log->info($email);
		$respuesta = $email;

		echo json_encode(strtolower($respuesta));

		
	}

}
?>