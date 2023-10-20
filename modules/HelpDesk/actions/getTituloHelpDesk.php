<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class HelpDesk_getTituloHelpDesk_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log,$adb, $current_user;

		$usuario_logueado = $current_user->id;

		$log->debug("entre al process de HelpDesk_getTituloHelpDesk_Action");

		$id = $request->get('id');

		$sql = "SELECT ticket_no nro,title titulo
				FROM  vtiger_crmentity 
				LEFT JOIN vtiger_troubletickets ON (vtiger_troubletickets.ticketid = vtiger_crmentity.crmid  ) 
				LEFT JOIN vtiger_ticketcf ON (vtiger_ticketcf.ticketid = vtiger_crmentity.crmid  ) 
				LEFT JOIN vtiger_ticketcomments ON (vtiger_ticketcomments.ticketid = vtiger_crmentity.crmid  ) 
				LEFT JOIN vtiger_crmentity_user_field ON (vtiger_crmentity_user_field.recordid = vtiger_crmentity.crmid  
				AND vtiger_crmentity_user_field.userid = ? ) 
				WHERE  vtiger_crmentity.crmid= ?";

		$result = $adb->pquery($sql,array($usuario_logueado,$id));

		$log->debug("controlando que tenga datos");

		$nro = $adb->query_result($result, 0, 'nro');
		$titulo = $adb->query_result($result, 0, 'titulo');
		$log->debug($nro);
		$log->debug("id ".$id);
		if($nro==''){
			$log->debug("si esta vacio debe verse este mensaje");

			//Controlo la relacion con cuentas e incidencias
			$sql_ = "SELECT ticket_no nro, title titulo
					 FROM vtiger_crmentityrel 
					 INNER JOIN vtiger_troubletickets ON relcrmid = ticketid
					 WHERE crmid = ? AND  module = ? and status <> ?
					 
					 ORDER BY relcrmid DESC
					 LIMIT 1 ";
			$result_ = $adb->pquery($sql_,array($id,'Accounts','closed')); ;
			$nro = $adb->query_result($result_, 0, 'nro');	
			$titulo = $adb->query_result($result_, 0, 'titulo');
		}
		$respuesta = $nro . " - " . $titulo;

		//Obtengo la atencion activa tema aportacion nro empresa
		$sql = "SELECT  topicname tema,
						ticketcodigoaportacion aportacion, 
						aw_cont_empresa  empresa,
						accdocumentnumber documento
				FROM vtiger_atencionesweb a 
				INNER JOIN vtiger_crmentity e ON e.crmid = a.atencioneswebid  AND e.deleted = 0 
				LEFT JOIN vtiger_account ON accountid = aw_persona
				LEFT JOIN vtiger_topics ON topicsid = aw_tema
				LEFT JOIN vtiger_ticketcodigoaportacion ON ticketcodigoaportacionid = aw_cont_aportacion
				WHERE a.aw_estado = ?
				AND e.smownerid = ?	";
		$result = $adb->pquery($sql, array('Asignado', $usuario_logueado));

		$tema = $adb->query_result($result, 0, 'tema');
		$aportacion = $adb->query_result($result, 0, 'aportacion');
		$empresa = $adb->query_result($result, 0, 'empresa');
		$documento = $adb->query_result($result, 0, 'documento');

		//Formatear la aportacion
		$pos = strpos($aportacion, "-");
		if ($pos !== false) {
  	   		$result = trim(substr($aportacion, $pos + 1));
    		$aportacion =  $result; 
		}
		
		//Si hay aportacion = Empresa
		if($aportacion){
			$respuesta = $tema . " - " .$aportacion . " - " . $empresa;
		}else{
			//Persona
			$respuesta = $tema . " - " . $documento;
		}

		//$log->info("empresa ");
		
		

		echo json_encode(strtoupper($respuesta));

		
	}

}
?>