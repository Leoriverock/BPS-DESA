<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
//Esta funcion es para traer el email remitente en caso de responder un correo desde la vista relacionada de helpdesk Defecto #154150 LR 04/09/23
class HelpDesk_getEmailRel_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb,$current_user;

		$log->debug("entre al process de HelpDesk_getEmailRel_Action");
		$log->info($request);
		$usuario_logueado = $current_user->id;
		$id = $request->get('id');
		$id_email = $request->get('id_email');
		$log->info("mostrame lo que tiene id_email");
		$log->info($id_email);
		
		
		$sql_ = 'SELECT from_email email FROM vtiger_emaildetails
					 WHERE emailid = ? ';
		$result = $adb->pquery($sql_, array($id_email));
		

		
		$email = $adb->query_result($result, 0, 'email');
		$email = str_replace(array('[',"]"), "", $email);
		$email = str_replace("&quot", "", $email);
		$email = str_replace(array(';',";"), "", $email);
		
		$log->info("showme how yo");
		$log->info($email);
		$respuesta = $email;

		echo json_encode(strtolower($respuesta));

		
	}

}
?>