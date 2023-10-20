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
class AtencionPresencial_CargarFinAtenciones_View extends Vtiger_IndexAjax_View {

	public function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log, $adb,$current_user;
		
		//$usuarioLogueado = 11; //Id usuario de prueba trae consultas de diego da costa
		$usuarioLogueado = $current_user->id; //usuario que estoy logueado
		
		$moduleName = $request->getModule();

		$id = $request->get('id');
		$sql = "SELECT ap_tipoconsulta as tipoconsulta FROM vtiger_ap_tipoconsulta";
		$rs = $adb->pquery($sql);

		while($fila = $adb->fetch_array($rs)){
			$log->info("dentro del while");
			$comboConsultas[] = array(
										"tconsulta" => $fila["tipoconsulta"]
									);

		}

		$sql = 'SELECT ap_tramite
					             FROM vtiger_atencionpresencial
			                     WHERE atencionpresencialid = ?;';
		$rs = $adb->pquery($sql, array($id));
		$tramite = $adb->query_result($rs, 0, 'ap_tramite');

		$select = 'SELECT id, nombre 
				   FROM lp_tramites
				   WHERE usuario = ? ';
		$rs = $adb->pquery($select,array($usuarioLogueado));
		while($fila = $adb->fetch_array($rs)){
			

			$comboTramites[] = array(
										"id" => $fila["id"],
										"nombre" => $fila["nombre"]
									);

		}

		


		$log->info($comboConsultas);
		$viewer = $this->getViewer($request);

		$viewer->assign('TIPOTRAMITE',$comboTramites);
		$viewer->assign('TRAMITE',$tramite);
		$viewer->assign('TIP0CONSULTA',$comboConsultas);
		$viewer->assign('ID',$id);
		$viewer->assign('MODULE', $moduleName);
		//$viewer->assign('tienetickets', $this->tieneTickets($id));
		//$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
		
			
		
		echo $viewer->view('CargarFinAtenciones.tpl',$moduleName,true);
	}

	/*public function tieneTickets($id){
		global $adb;
		$sql = "SELECT * FROM vtiger_crmentityrel r
				INNER JOIN vtiger_crmentity e1 ON (e1.crmid = r.relcrmid OR e1.crmid = r.crmid) AND setype = 'HelpDesk' AND	e1.deleted = 0
				WHERE ? =  r.crmid or ? = r.relcrmid ";
		$rs = $adb->pquery($sql, array($id,$id));
		if($adb->num_rows($rs) > 0) return true;
		return false;
	}*/
	
	
}