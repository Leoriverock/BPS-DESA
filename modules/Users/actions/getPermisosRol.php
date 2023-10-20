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
class Users_getPermisosRol_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb,$current_user;
		$log->info("esta entrando a Users_getPermisosRoles_Action");

		$usuario_logueado = $current_user->id;
		$id = $request->get('id');
		$sql = "SELECT roleid FROM vtiger_user2role WHERE userid = ?";
		
		$result = $adb->pquery($sql, array($usuario_logueado));
		$roleid = $adb->query_result($result, 0, 'roleid');	
		$permiso = false;
		$consultasweb_roles = unserialize(LISTVIEW_CONSULTASWEB_ROLES);
		
		$existe = in_array($roleid,$consultasweb_roles);


		if($existe){
			$log->info("entra al if de ");
			$permiso = true;
		}

		
		
		$log->info("mostrame la respuesta");
		$log->info($permiso);

		echo json_encode(strtolower($permiso));

		
	}

}
?>