<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'config.ludere.php';
class Users_getPermisos_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb,$current_user;
		$log->info("esta entrando a Users_getPermisos_Action");

		$usuario_logueado = $current_user->id;
		$id = $request->get('id');
		$sql = "SELECT is_admin FROM vtiger_users WHERE id = ?";
		
		$result = $adb->pquery($sql, array($usuario_logueado));
		$is_admin = $adb->query_result($result, 0, 'is_admin');	
		
        if($is_admin == 'on'){
            $permiso = true;
        }else{
            $permiso = false;
        }
		
		
		$log->info("mostrame la respuesta");
		$log->info($permiso);

		echo json_encode($permiso);

		
	}

}
?>