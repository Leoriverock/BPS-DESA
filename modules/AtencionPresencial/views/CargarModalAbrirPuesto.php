<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once("config.ludere.php");
require_once ('include/utils/LP_utils.php');
class AtencionPresencial_CargarModalAbrirPuesto_View extends Vtiger_IndexAjax_View {

	public function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log, $adb,$current_user;
		
		//$usuarioLogueado = 11; //Id usuario de prueba trae consultas de diego da costa
		$usuarioLogueado = $current_user->id; //usuario que estoy logueado
		$grupoActual = ''; //Grupo que pertenece el usuario - hacer consultas a vtiger_user2goups (puede pertencer a mas de un grupo)

		$sql_group_pref = "SELECT us_grupopref id,user_name usuario, equipo FROM vtiger_users WHERE  id = ?";
		$result = $adb->pquery($sql_group_pref,array($usuarioLogueado));
		$grupo_pref = $adb->query_result($result, 0, 'id');
		$usuario = $adb->query_result($result, 0, 'usuario');
		$conectado = $adb->query_result($result, 0, 'equipo'); //Si este valor esta seteado es porque hay un puesto abierto

		
		$moduleName = $request->getModule();
		$consultaswebid = $request->get("id");

		$sql = "";
		//$result = $adb->pquery($sql,array('Asignado',$usuarioLogueado));

		$host = gethostbyaddr($_SERVER['REMOTE_ADDR']); //Obtengo el nombre de usuario y el dominio
			//$host = 'vir20e0402w106.bps.net';
		$data = explode(".", $host); 
		
		$equipo = $data[0];
				
		 
		$viewer = $this->getViewer($request);
		$viewer->assign('EQUIPO',$equipo);
		$viewer->assign('CONECTADO',$conectado);
		$viewer->assign('USUARIO',$usuario);



	
		

		$viewer->assign('MODULE', $moduleName);
		//$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
		
			
		
		echo $viewer->view('CargarModalAbrirPuesto.tpl',$moduleName,true);
	}
	
	
}