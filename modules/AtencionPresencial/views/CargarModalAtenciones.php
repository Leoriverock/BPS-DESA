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
require_once('integracion/ws_at_curl_2.php');
class AtencionPresencial_CargarModalAtenciones_View extends Vtiger_IndexAjax_View {

	public function checkPermission(Vtiger_Request $request) {
		return true;
	}

	public function process(Vtiger_Request $request) {
		global $log, $adb, $current_user;
		
		//$usuarioLogueado = 11; //Id usuario de prueba trae consultas de diego da costa
		$usuarioLogueado = $current_user->id; //usuario que estoy logueado
		$grupoActual = ''; //Grupo que pertenece el usuario - hacer consultas a vtiger_user2goups (puede pertencer a mas de un grupo)

		$sql_group_pref = "SELECT us_grupopref id FROM vtiger_users WHERE  id = ?";
		$result = $adb->pquery($sql_group_pref,array($usuarioLogueado));
		$grupo_pref = $adb->query_result($result, 0, 'id');
		
		$moduleName = $request->getModule();
		$consultaswebid = $request->get("id");

		$sql = "SELECT equipo, lugcod FROM vtiger_users WHERE id = ?";
		$rs = $adb->pquery($sql, array($current_user->id));
		$equipo = $rs->fields['equipo'];
		$lugar = $rs->fields['lugcod'];

		$atenciones = array();
		//$rs = '[{"apellido":" ","documento":" ","estado":"P","hora":"14:11","lugarCod":1,"nombre":" ","numerocod":1,"numerofecha":"2023-02-03","sector":"ASESORAMIENTO Y RESERVA DE NÚMEROS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:11","lugarCod":1,"nombre":" ","numerocod":2,"numerofecha":"2023-02-03","sector":"ASESORAMIENTO Y RESERVA DE NÚMEROS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:11","lugarCod":1,"nombre":" ","numerocod":3,"numerofecha":"2023-02-03","sector":"ASESORAMIENTO Y RESERVA DE NÚMEROS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:11","lugarCod":1,"nombre":" ","numerocod":4,"numerofecha":"2023-02-03","sector":"ASESORAMIENTO Y RESERVA DE NÚMEROS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:12","lugarCod":1,"nombre":" ","numerocod":5,"numerofecha":"2023-02-03","sector":"ASESORAMIENTO Y RESERVA DE NÚMEROS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:12","lugarCod":1,"nombre":" ","numerocod":6,"numerofecha":"2023-02-03","sector":"ASESORAMIENTO Y RESERVA DE NÚMEROS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"}]';
		$archivo = fopen('testWs.txt', 'a');
		$datos = Ws_AP::obtenerEstadoPuesto($equipo);
		if($datos){
			$estado = json_decode($datos)->puestoestado;
			if($estado == 'D'){//D es abierto
				$rs = Ws_AP::listarNumerosNuevos($equipo, $lugar);
				$json = json_decode($rs);
				//fwrite($archivo, var_export(array('resultado' => $rs, 'equipo' => $equipo, 'lugar' => $lugar), true).PHP_EOL);
				if($json)
				foreach($json as $fila){
					$datosPersonas = null;

					if(!empty(trim($fila->documento))){
						$datosPersonas['doc']=trim($fila->documento);
					}

					if (!empty(trim($fila->nombre)) || !empty(trim($fila->apellido))) {
					    $nombreCompleto = trim(trim($fila->nombre) . ' ' . trim($fila->apellido));
					    //$nombreCompleto = str_replace("'", " ", $nombreCompleto);
					    $fila->nombre = str_replace("'", "&apos;", $fila->nombre);
					    $fila->apellido = str_replace("'", "&apos;", $fila->apellido);
					    /*$nombreCompletoEscapado = htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8');
					    $nombreCompletoDecodificado = html_entity_decode($nombreCompletoEscapado, ENT_QUOTES | ENT_HTML5, 'UTF-8');*/
					    $fila->nombre = str_replace("'", "&apos;", $fila->nombre);
					    $fila->apellido = str_replace("'", "&apos;", $fila->apellido);
					    
					    $datosPersonas['nombre'] = $nombreCompleto;
					}


					$atenciones[] = array(
										  "numero"=> $fila->numerocod,
										  "fecha"=> $fila->numerofecha,
										  "hora"=> $fila->hora,
										  "tipo"=> $fila->tipodoc->nombre,
										  "sector"=> $fila->sector,
										  "datosPersona"=> $datosPersonas,
										  "data" => $fila
					);

				}
			}else{
				$log->info("hace eso");
				$update_equipo = "UPDATE vtiger_users SET equipo = NULL, lugcod = NULL where id = ?";
				$res = $adb->pquery($update_equipo,array($usuarioLogueado));
			}
		}
	 	

		$atenciones_atrasadas = array();
		//$rs = '[{"apellido":" ","documento":" ","estado":"P","hora":"14:11","lugarCod":1,"nombre":" ","numerocod":1,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:11","lugarCod":1,"nombre":" ","numerocod":2,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:11","lugarCod":1,"nombre":" ","numerocod":3,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:11","lugarCod":1,"nombre":" ","numerocod":4,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:12","lugarCod":1,"nombre":" ","numerocod":5,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:12","lugarCod":1,"nombre":" ","numerocod":6,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:12","lugarCod":1,"nombre":" ","numerocod":6,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:12","lugarCod":1,"nombre":" ","numerocod":6,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:12","lugarCod":1,"nombre":" ","numerocod":6,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"},{"apellido":" ","documento":" ","estado":"P","hora":"14:12","lugarCod":1,"nombre":" ","numerocod":6,"numerofecha":"2023-02-03","sector":"CONSULTAS","sectorId":3,"serie":"TC ","tramite":"Trámites Cortos SS1"}]';
		
		$datos = Ws_AP::obtenerEstadoPuesto($equipo);
		if($datos){
			$estado = json_decode($datos)->puestoestado;
			if($estado == 'D'){//D es abierto

				$rs = Ws_AP::listarNumerosAtrasados($equipo, $lugar);
				$json = json_decode($rs);
				if($json)
				foreach($json as $fila){
					$datosPersonas = null;

					if(!empty(trim($fila->documento))){
						$datosPersonas['doc']=trim($fila->documento);
					}

					if (!empty(trim($fila->nombre)) || !empty(trim($fila->apellido))) {
					    $nombreCompleto = trim(trim($fila->nombre) . ' ' . trim($fila->apellido));
					    $nombreCompleto = str_replace("'", " ", $nombreCompleto);
					    /*$nombreCompletoEscapado = htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8');
					    $nombreCompletoDecodificado = html_entity_decode($nombreCompletoEscapado, ENT_QUOTES | ENT_HTML5, 'UTF-8');*/
					    $fila->nombre = str_replace("'", "&apos;", $fila->nombre);
					    $fila->apellido = str_replace("'", "&apos;", $fila->apellido);
					    
					    $datosPersonas['nombre'] = $nombreCompleto;
					}



					$atenciones_atrasadas[] = array(
										  "numero"=> $fila->numerocod,
										  "fecha"=> $fila->numerofecha,
										  "hora"=> $fila->hora,
										  "tipo"=> $fila->tipodoc->nombre,
										  "sector"=> $fila->sector,
										  "datosPersona"=> $datosPersonas,
										  "data" => $fila
					);

				}
			}else{
				$log->info("hace eso");
				$update_equipo = "UPDATE vtiger_users SET equipo = NULL, lugcod = NULL where id = ?";
				$res = $adb->pquery($update_equipo,array($usuarioLogueado));
			}
		}

		$log->info("atrasadsas");
		$log->info($atenciones_atrasadas);
		
		//$estado = "D"; 
		$viewer = $this->getViewer($request);
		$APactiva = AtencionPresencial_Module_Model::getAtencionpPActiva();
		if(!$APactiva){
			$viewer->assign('ESTADO',$estado);
			$viewer->assign('ATENCIONES',$atenciones);
			$viewer->assign('ATENCIONESATR',$atenciones_atrasadas);
		}else{
			$viewer->assign('APACTIVA', true);
			$viewer->assign('APACTIVADETAIL', $APactiva['atencionpresencialurl']);
		}
		



	
		

		$viewer->assign('MODULE', $moduleName);
		//$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
		
			
		
		echo $viewer->view('CargarModalAtenciones.tpl',$moduleName,true);
	}
	
	
}
