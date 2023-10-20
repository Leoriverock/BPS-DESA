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
class ConsultasWeb_getGrupos_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb,$current_user;;
		$usuarioLogueado = $current_user->id;
		
		$id = $request->get('id');

		$sql = 'SELECT smownerid grupo FROM  vtiger_crmentity 
						LEFT JOIN vtiger_consultasweb ON (vtiger_consultasweb.consultaswebid = vtiger_crmentity.crmid  ) 
						LEFT JOIN vtiger_consultaswebcf ON (vtiger_consultaswebcf.consultaswebid = vtiger_crmentity.crmid  ) 
						LEFT JOIN vtiger_crmentity_user_field ON (vtiger_crmentity_user_field.recordid = vtiger_crmentity.crmid  
						AND vtiger_crmentity_user_field.userid = ? ) 
						WHERE  vtiger_crmentity.crmid=?  LIMIT 1';
		$log->debug("entre al process de ConsultasWeb_getUsuarios_Action");
		$log->debug($id);			

		$resultado = $adb->pquery($sql,array($usuarioLogueado,$id));
		$grupo = $adb->query_result($resultado, 0, 'grupo');


		
		
		//$json = array("id" => 1, "nombre" => "TeleconsultasNEW");
		$sql = "SELECT groupid id, groupname  nombrecompleto
					FROM vtiger_groups
					WHERE groupid not IN  (
							?
							)
							AND TYPE like '%mail%'
				";
		$result = $adb->pquery($sql,array($grupo)); //EJECUTIVOS_MAILS -- AND vtiger_user2role.roleid = ?
		//$grupo = $adb->query_result($result, 0, 'grupo');

		$i = 0;
		while($fila = $adb->fetch_array($result)){
			//$log->debug("entre al while");

			$array_keys[$i]= $fila['id'];
			$array_values[$i]= $fila['nombrecompleto'];
			/*$log->debug("i ".$i );
			$log->debug($array_keys[$i]);
			$log->debug($array_values[$i]);*/
			$i = $i + 1;
			
		}
		
		
		for($i=0;$i<count($array_keys);$i++){
			$json[$array_keys[$i]] = $array_values[$i];
		}
		
		$log->debug("entre al process de consulta $json");
		echo json_encode($json);

		
	}

}
?>