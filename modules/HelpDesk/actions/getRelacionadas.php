<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_getRelacionadas_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb,$current_user;

		$log->debug("entre al process de HelpDesk_getRelacionadas_Action");
		
		$usuario_logueado = $current_user->id;
		$resultados = array();
		//Obtengo la atencion activa
		$sql = "SELECT atencioneswebid id 
				FROM vtiger_atencionesweb a 
				INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid AND e.deleted = 0 
				WHERE a.aw_estado = ? AND e.smownerid = ?";
		$result = $adb->pquery($sql, array('Asignado', $usuario_logueado));
		$id = $adb->query_result($result, 0, 'id');	

		//Obtengo las consultas relacionadas a la atencion activa
		$sql_ = "SELECT relcrmid
				 FROM vtiger_crmentityrel
				 WHERE crmid = ?
				 AND relmodule = ?";	
		$result = $adb->pquery($sql_, array($id,'ConsultasWeb'));
		//Si hay resultados entonces consulto por los datos de las consultasweb
		if ($result){
			while($fila = $adb->fetch_array($result)){
				$cw = "SELECT DATE_FORMAT(createdtime, '%d-%m-%Y') fecha, cw_contenido	
					   FROM vtiger_consultasweb
					   INNER JOIN vtiger_crmentity e ON e.crmid = consultaswebid AND e.deleted = 0 
					   WHERE consultaswebid = ?";
				$rs = $adb->pquery($cw, array($fila['relcrmid']));
				$fecha = $adb->query_result($rs, 0, 'fecha');	
				$cw_contenido = $adb->query_result($rs, 0, 'cw_contenido');	
				$contenido ="Fecha ".$fecha."<br> ".nl2br($cw_contenido);
				$resultado = array(
			        //'fecha' => $fecha,
			        'contenido' => $contenido
			    );
				$resultados[] = $resultado;

			}	
		}
		$log->info("show me how to live: ");
		$log->info(json_encode($resultados));

		echo json_encode($resultados);

		
	}

}
?>