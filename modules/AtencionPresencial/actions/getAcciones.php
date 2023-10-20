<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once('integracion/ws_at_curl_2.php');
include_once('integracion/ws.php');
include_once(___DIR__.'/../../../config.ludere.php');
class AtencionPresencial_getAcciones_Action extends Vtiger_Action_Controller {

	function checkPermission(Vtiger_Request $request) {
	}

	public function process(Vtiger_Request $request) {
		global $log, $adb, $current_user, $VTIGER_BULK_SAVE_MODE;

		$log->debug("entre al process de AtencionPresencial_getAcciones_Action");

		
		
		$usuarioLogueado = $current_user->id; //Obtengo id del usuario logueado 
		$modo = $request->get('modo');
		$control = $request->get('control');
		$at_id = $request->get('id');
		$tipoConsulta = $request->get('tipoconsulta');
		$idtramite = $request->get('tipotramite');
		$seleccionid = $request->get('seleccionid');
		$sectorid = $request->get('sectorid');
		$numerofecha = $request->get('fecha');
		$numeroHora = $request->get('hora');
		$sectorid = $request->get('sectorid');
		$numerocod = $request->get('numerocod');


		


		
		if($modo == 'abrirPuesto'){
			$log->debug("entre al abrirPuesto ");
			$host = gethostbyaddr($_SERVER['REMOTE_ADDR']); //Obtengo el nombre de usuario y el dominio
			//$host = 'vir20e0402w106.bps.net';
			$data = explode(".", $host); 

			$equipo = $data[0];
			$user_query = "SELECT user_name usuario FROM vtiger_users WHERE id = ? AND deleted = 0 ";
			$result = $adb->pquery($user_query,array($usuarioLogueado) );

			$usuario = $adb->query_result($result, 0, 'usuario');
			$log->info("usuario ".$usuario);
			$log->info("equipo ".$equipo);

			$rs = ObtIdUsu($usuario);
			$log->info("respuesta: ");
			$log->info($rs);
 			$userID = $rs['resultado'];
			$log->info($userID);
			$rs = ObtPerfilesUsuOf($userID);
			$perfil_habilitado = $rs['resultado'];
			$log->info("respuesta2: ");
			$log->info($perfil_habilitado);
			if(trim($perfil_habilitado) == trim('GAP_CONSULTOR')){
				$log->info("entra al if si es GAP_CONSULTOR");

				$datos = Ws_AP::abrirPuesto($equipo, $usuario);
				$result = array('success' =>  true);
				$lugcod = json_decode($datos)->lugcod;
				$puestoip = json_decode($datos)->puestoip;
				$log->info("lugcod ".$lugcod);
			}
			
			


			$error = json_decode($datos)->error;
			$log->info("Result ".$datos);
			$json = json_decode($datos);
			
				if(!$json){
					$datos = "error";
					$result = array('success' =>  false, 'error' => 'No se pudo abrir el puesto, o el perfil no esta habilitado');
				}else{
					if($json->error){
						
							$result = array('success' =>  false, 'error' => $json->descripcion, 'data' => $json);
					
						
					}else{
						$log->info("adentro del if");	
						$result['respuesta'] = $json;
						$update_equipo = "UPDATE vtiger_users SET equipo = ?, lugcod = ? where id = ?";
						$res = $adb->pquery($update_equipo,array($puestoip,$lugcod,$usuarioLogueado));
						//Consumir traer tramites y setearlo en bd	
						$datos = Ws_AP::obtenerTramitesPuesto($equipo);
						$log->info("adentro del else");	
						$log->info($datos);	
						
						//Borrar registros anteriores de la tabla
						$delete = "DELETE FROM lp_tramites WHERE usuario = ?";
						$rs = $adb->pquery($delete, array($usuarioLogueado));
						//Inserto nuevos registros
						// Convertir la cadena JSON en un array PHP
						$data = json_decode($datos, true);
						$log->info($data);
						// Acceder a los valores
						foreach ($data as $item) {
						    $id = $item['id'];
						    $nombre = $item['nombre'];
						    $log->info($id . " ". $nombre);
						    $insert = 'INSERT INTO lp_tramites (id, nombre, usuario) 
									   VALUES (?,?,?)';
							$rs = $adb->pquery($insert,array($id,$nombre,$usuarioLogueado));
						    
						}
						

					}		
				}
			


			
			$response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit();
			
			
		}

		if($modo == 'cerrarPuesto'){
			$log->debug("entre al cerrarPuesto ");
			$host = gethostbyaddr($_SERVER['REMOTE_ADDR']); //Obtengo el nombre de usuario y el dominio
			//$host = 'vir20e0402w106.bps.net';
			$data = explode(".", $host); 

			$equipo = $data[0];
			
			//$log->info("equipo ".$equipo);

			$datos = Ws_AP::cerrarPuesto($equipo);
			$result = array('success' =>  true);
			$error = json_decode($datos)->error;
			$log->info("Result ".$datos);		
			$json = json_decode($datos);
			if(!$json){
				$datos = "error";
				$result = array('success' =>  false, 'error' => 'No se pudo cerrar el puesto');
			}else{
				if($json->error){
					$result = array('success' =>  false, 'error' => $json->codigo, 'data' => $json);
				}else{
					$log->info("adentro del if");	
					$result['respuesta'] = $json;
					$update_equipo = "UPDATE vtiger_users SET equipo = NULL, lugcod = NULL where id = ?";
					$res = $adb->pquery($update_equipo,array($usuarioLogueado));
				}		
			}

				
			
			//$result = array('success' =>  true, 'result' => $datos);
			$response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit();
			
		}

		if($modo == 'obtenerEstadoPuesto'){
			$log->debug("entre al obtenerEstadoPuesto ");
			$host = gethostbyaddr($_SERVER['REMOTE_ADDR']); //Obtengo el nombre de usuario y el dominio
			//$host = 'vir20e0402w106.bps.net';
			$data = explode(".", $host); 

			$equipo = $data[0];

			$result = array('success' =>  true);
			$datos = Ws_AP::obtenerEstadoPuesto($equipo);
			if($datos){
				$estado = json_decode($datos)->puestoestado;
				if($estado == 'D'){//D es abierto
					
					$result['respuesta'] = $estado;

				}else{
					$log->info("hace eso");
					$update_equipo = "UPDATE vtiger_users SET equipo = NULL, lugcod = NULL where id = ?";
					$res = $adb->pquery($update_equipo,array($usuarioLogueado));
					$result['respuesta'] = $estado;
					}
				}
			$response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit(); 
		}


		/*
		if($modo == 'controlarPuesto'){
			$log->debug("entre al controlarPuesto ");
			$log->debug("entre al cerrarPuesto ");
			$host = gethostbyaddr($_SERVER['REMOTE_ADDR']); //Obtengo el nombre de usuario y el dominio
			//$host = 'vir20e0402w106.bps.net';
			$data = explode(".", $host); 

			$equipo = $data[0];

			$sql = "SELECT fechalog, DATE_SUB(NOW(), INTERVAL 1 HOUR),  NOW()
					FROM lp_status_ap 
					WHERE userid = ?
					AND fechalog  BETWEEN DATE_SUB(NOW(), INTERVAL 1 HOUR) AND NOW()
					ORDER  BY fechalog DESC
					LIMIT 1";
			$res = $adb->pquery($sql,array($usuarioLogueado));
			$actividad = $adb->num_rows($res);
			$log->info($actividad);
			//Si devuelve  0 no hay actividad por mas de 1 hora - se cierra el puesto
			if($actividad==0){
				$update_equipo = "UPDATE vtiger_users SET equipo = NULL, lugcod = NULL where id = ?";
				$res = $adb->pquery($update_equipo,array($usuarioLogueado));
				$datos = Ws_AP::cerrarPuesto($equipo);
				$result = array('success' =>  true, 'result' => 'Se ha cerrado el puesto por inactividad');
			}else{
				$result = array('success' =>  false, 'error' => $json->codigo, 'data' => $json);
			}

			//$result = array('success' =>  true, 'result' => 'Ok');
			$response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit();  


		}*/

		
		
		if($modo == 'llamarNumero'){
			$log->debug("entre al llamarNumero ");
			$datos = '';


			$user_query = "SELECT user_name usuario, equipo, lugcod FROM vtiger_users WHERE id = ? AND deleted = 0 ";
			$result = $adb->pquery($user_query,array($usuarioLogueado) );


			$equipo = $adb->query_result($result, 0, 'equipo');
			$lugarcod = $adb->query_result($result, 0, 'lugcod');
			$usuario = $adb->query_result($result, 0, 'usuario');

			/*$ap = "SELECT ap_sectorid sectorid, ap_numerows numerocod, DATE_FORMAT(ap_fechacomienzo, '%Y-%m-%d') numerofecha 
				   FROM vtiger_atencionpresencial 
				   WHERE atencionpresencialid = ?";
			$result = $adb->pquery($ap, array($at_id));	   

			
			$sectorid = $adb->query_result($result, 0, 'sectorid');
			$numerocod = $adb->query_result($result, 0, 'numerocod');
			$numerofecha = $adb->query_result($result, 0, 'numerofecha');
			*/
			//$ws = new Ws_AP('https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/');
			$log->info("lugar ".$lugarcod." sectorid ".$sectorid." numerocod ".$numerocod." fecha ".$fecha." usuario ".$usuario." equipo ".$equipo);
			
			$datos = Ws_AP::llamarNumero($lugarcod,$sectorid,$numerocod,$numerofecha,$usuario,$equipo,$numeroHora);
			$archivo = fopen('logsWS.txt', 'a');
			fwrite($archivo, 'llamarNumero => '.var_export(array('requestparam' => array( 'lugar' => $lugarcod, 'sector' => $sectorid, 'numero' => $numerocod, 'fecha' => $numerofecha, 'user' => $usuario, 'equipo' => $equipo, 'hora' => $numeroHora),'response' => $datos), true).PHP_EOL);
			$json = json_decode($datos);
			fwrite($archivo, 'llamarNumero => '.var_export(array('json' => $json, 'errorjson' =>json_last_error()), true).PHP_EOL);
			
			$result = array('success' =>  true);
			$error_codigo = $json->codigo;
			$log->info("muestro el error: ");
			$log->info($error_codigo);

			//$estado = json_decode($datos)->estado;
			$log->info("estado ".$datos);
			if(!$json){
				$datos = "error";
				$result = array('success' =>  false, 'error' => 'El número que intenta llamar fue atendido por otro funcionario');
			}else{
				if($json->error){
					if(trim($json->descripcion) == trim("El numero ya fue llamado")){
						$result = array('success' =>  false, 'error' => 'El número que intenta llamar fue atendido por otro funcionario', 'data' => $json);
					}else{
						$result = array('success' =>  false, 'error' => $json->descripcion, 'data' => $json);	
					}
					
				}else{
					$result['respuesta'] = $json;
					AtencionPresencial_Record_Model::statusLog($numerocod, $sectorid, $numerofecha, $lugarcod, null, 'Llamado');	
				}		
			}

			//echo json_encode($datos);
	        $response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit();  
			
		}

		if($modo == 'liberarNumero'){
			$log->debug("entre al liberarNumero ");
			$datos = '';


			$user_query = "SELECT user_name usuario, equipo, lugcod FROM vtiger_users WHERE id = ? AND deleted = 0 ";
			$result = $adb->pquery($user_query,array($usuarioLogueado) );


			$equipo = $adb->query_result($result, 0, 'equipo');
			$lugarcod = $adb->query_result($result, 0, 'lugcod');
			$usuario = $adb->query_result($result, 0, 'usuario');

			/*$ap = "SELECT ap_sectorid sectorid, ap_numerows numerocod, DATE_FORMAT(ap_fechacomienzo, '%Y-%m-%d') numerofecha 
				   FROM vtiger_atencionpresencial 
				   WHERE atencionpresencialid = ?";
			$result = $adb->pquery($ap, array($at_id));	   

			$sectorid = $adb->query_result($result, 0, 'sectorid');
			$numerocod = $adb->query_result($result, 0, 'numerocod');
			$numerofecha = $adb->query_result($result, 0, 'numerofecha');*/

			$log->info("lugar ".$lugarcod." sectorid ".$sectorid." numerocod ".$numerocod." fecha ".$numerofecha." usuario ".$usuario." equipo ".$equipo);

			//$ws = new Ws_AP('https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/');
			$datos = Ws_AP::liberarNumero($lugarcod,$sectorid,$numerocod,$numerofecha,$usuario,$equipo,$numeroHora);
			$archivo = fopen('logsWS.txt', 'a');
			fwrite($archivo, 'liberarNumero => '.var_export(array('requestparam' => array( 'lugar' => $lugarcod, 'sector' => $sectorid, 'numero' => $numerocod, 'fecha' => $numerofecha, 'user' => $usuario, 'equipo' => $equipo, 'hora' => $numeroHora),'response' => $datos), true).PHP_EOL);
			$json = json_decode($datos);
			fwrite($archivo, 'liberarNumero => '.var_export(array('json' => $json, 'errorjson' =>json_last_error()), true).PHP_EOL);
			AtencionPresencial_Record_Model::statusLog($numerocod, $sectorid, $numerofecha, $lugarcod, null, 'Liberado');
			$log->info("estado ".$datos);
			//echo json_encode($datos);
			$result = array('success' =>  true, 'result' => $datos);
			$response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit();
		}



		if($modo == 'atenderNumero'){
			$log->debug("entre al atenderNumero ");
			$datos = '';
			$result = array();
			


			$user_query = "SELECT user_name usuario, equipo, lugcod FROM vtiger_users WHERE id = ? AND deleted = 0 ";
			$result = $adb->pquery($user_query,array($usuarioLogueado) );


			$equipo = $adb->query_result($result, 0, 'equipo');
			$lugarcod = $adb->query_result($result, 0, 'lugcod');
			$usuario = $adb->query_result($result, 0, 'usuario');


			/*$ap = "SELECT ap_sectorid sectorid, ap_numerows numerocod, DATE_FORMAT(ap_fechacomienzo, '%Y-%m-%d') numerofecha 
				   FROM vtiger_atencionpresencial 
				   WHERE atencionpresencialid = ?";
			$red = $adb->pquery($ap, array($at_id));	   

			
			$sectorid = $adb->query_result($result, 0, 'sectorid');
			$numerocod = $adb->query_result($result, 0, 'numerocod');
			$numerofecha = $adb->query_result($result, 0, 'numerofecha');
			*/

			//$ws = new Ws_AP('https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/');

			$datos = Ws_AP::atenderNumero($lugarcod,$sectorid,$numerocod,$numerofecha,$usuario,$equipo, $numeroHora);
			$log->info("estado ".$datos);
			$log->info("lugar ".$lugarcod." sectorid ".$sectorid." numerocod ".$numerocod." fecha ".$numerofecha." usuario ".$usuario." equipo ".$equipo);
			//$estado = json_decode($datos)->estado;
			//$log->info("estado ".$datos);
			$archivo = fopen('logsWS.txt', 'a');
			fwrite($archivo, 'atenderNumero => '.var_export(array('requestparam' => array( 'lugar' => $lugarcod, 'sector' => $sectorid, 'numero' => $numerocod, 'fecha' => $numerofecha, 'user' => $usuario, 'equipo' => $equipo, 'hora' => $numeroHora),'response' => $datos), true).PHP_EOL);
			$json = json_decode($datos);
			fwrite($archivo, 'atenderNumero => '.var_export(array('json' => $json, 'errorjson' =>json_last_error()), true).PHP_EOL);
			if(!is_object($json) || json_last_error() != 0){
				$datos = "error";
				$result = array('success' =>  false, 'error' => 'No se pudo atender el número');
			}else{
				if($json->error){
					$result = array('success' =>  false, 'error' => $json->codigo, 'data' => $json);
				}else{
					/*$recordModel = Vtiger_Record_Model::getInstanceById($at_id, 'AtencionPresencial');
	            	$recordModel->set('ap_estado', 'en Proceso');
	            	$recordModel->set('mode', 'edit');
	            	$recordModel->save();*/

	            	AtencionPresencial_Record_Model::statusLog($numerocod, $sectorid, $numerofecha, $lugarcod, null, 'Atendido');
					$recordModel = Vtiger_Record_Model::getCleanInstance('AtencionPresencial');
		            $recordModel->set('ap_estado', 'en Proceso');
		            $recordModel->set('assigned_user_id', $current_user->id);
		            $documento = $json->documento;
		            $persona = null;
		            $log->info("mostrando el documento $documento");
		            if(!empty(trim($documento))){
		                $personaModel = Accounts_Record_Model::getInstanceBySearch(array('acccountry' => 1, 'accdocumenttype' => 'DO', 'accdocumentnumber' => trim($documento)));
		                if($personaModel) $persona = $personaModel->getId();
		            }else{
		            	$personaModel = Accounts_Record_Model::getInstanceBySearch(array('acccountry' => 1, 'accdocumenttype' => 'PA', 'accdocumentnumber' => trim($documento)));
		                if($personaModel) $persona = $personaModel->getId();
		            }
		            if(!$persona) $persona = USER_DEFAULT;
		            $recordModel->set('ap_persona', $persona);
		            $recordModel->set('mode', 'create');
		            $recordModel->save();
  					$presencialid = $recordModel->getId();
		            $numerofecha_formateada = explode(" ", $json->numerofecha);
		            $numerofecha =  $numerofecha_formateada[0];
		            
		            $sql = "UPDATE vtiger_atencionpresencial SET ap_fechacomienzo = ?, ap_numerows =?, ap_sectorid = ?, ap_sector = ?, ap_lugcod = ?, ap_numerohora = ?, ap_numerofecha = ?, ap_tramite = ? WHERE atencionpresencialid = ?";
		            $adb->pquery($sql, array($numerofecha.' '.$json->hora.':00', $numerocod, $sectorid, $json->sector,$lugarcod,$json->hora,$numerofecha,$json->tramite,$recordModel->getId()));
		            $sql = "UPDATE vtiger_atencionpresencial SET ap_datosextras = ? WHERE atencionpresencialid = ?";
		            $adb->pquery($sql, array(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), $recordModel->getId()));
		            if($persona == USER_DEFAULT){
		            	$result = array('success' =>  true, 'result' => $datos, 'url' => 'index.php?module=AtencionPresencial&view=Edit&record='.$presencialid.'&app=SUPPORT');
		            		//$recordModel->getDetailViewUrl());
		            	$log->info("Estoy en USER_DEFAULT");
		            	$log->info(USER_DEFAULT);
		            	$log->info($recordModel->getDetailViewUrl());
		            }
		            else{
		            	$recordModel = Vtiger_Record_Model::getInstanceById($persona, 'Accounts');
		            	$result = array('success' =>  true, 'result' => $datos, 'url' => $recordModel->getDetailViewUrl());
		            }	
				}
			}

			//echo json_encode($datos);
			$response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit();
		}

		if($modo == 'finalizarNumero'){
			$log->debug("entre al finalizarNumero ");
			$datos = '';


			$user_query = "SELECT user_name usuario, equipo, lugcod FROM vtiger_users WHERE id = ? AND deleted = 0 ";
			$rs = $adb->pquery($user_query,array($usuarioLogueado) );


			$equipo = $adb->query_result($rs, 0, 'equipo');
			$lugarcod = $adb->query_result($rs, 0, 'lugcod');
			$usuario = $adb->query_result($rs, 0, 'usuario');

			$ap = "SELECT ap_sectorid sectorid, ap_numerows numerocod, DATE_FORMAT(ap_fechacomienzo, '%Y-%m-%d') numerofecha, DATE_FORMAT(ap_fechacomienzo, '%H:%i') numerohora
				   FROM vtiger_atencionpresencial 
				   WHERE atencionpresencialid = ?";
			$rs = $adb->pquery($ap, array($at_id));	   

			$sectorid = $adb->query_result($rs, 0, 'sectorid');
			$numerocod = $adb->query_result($rs, 0, 'numerocod');
			$numerofecha = $adb->query_result($rs, 0, 'numerofecha');
			$numerohora = $adb->query_result($rs, 0, 'numerohora');
			$tipoconsulta = '';
			if (trim($seleccionid) == trim('Atendido con éxito')){$tipoconsulta = 2;}
			if (trim($seleccionid) == trim('Falta documentación')){$tipoconsulta = 3;}
			if (trim($seleccionid) == trim('No corresponde el trámite')){$tipoconsulta = 10;}
			if (trim($seleccionid) == trim('Desiste del trámite')){$tipoconsulta = 20;}
			
			$log->info("lugar ".$lugarcod." sectorid ".$sectorid." numerocod ".$numerocod." fecha ".$numerofecha." usuario ".$usuario." equipo ".$equipo." tipoConsulta ".$tipoconsulta." numerohora ".$numerohora." idtramite ".$idtramite);

			$archivo = fopen('logsWS.txt', 'a');
			
			$datos = Ws_AP::finalizarNumero($lugarcod,$sectorid,$numerocod,$numerofecha,$usuario,$equipo,$tipoconsulta,$numerohora,$idtramite);
			$log->info($datos);
			fwrite($archivo, 'finalizarNumero => '.var_export(array('params' => array( 'lugar' => $lugarcod, 'sector' => $sectorid, 'numero' => $numerocod, 'fecha' => $numerofecha, 'usuario' => $usuario, 'equipo' => $equipo, 'tipoConsulta' => $tipoconsulta, 'hora' => $numerohora),'response' => $datos, 'json' => $json, 'errorjson' =>json_last_error()), true).PHP_EOL);
			//$json = json_decode($datos);
			//$log->info($json);
			/*if(!is_object($json) || json_last_error() != 0){
				$log->info("entra al if");
				$datos = "error";
				$result = array('success' =>  false, 'error' => 'No se pudo finalizar el número');	
			}else{
				$log->info("no entra al if");
				if($json->error){
					$result = array('success' =>  false, 'error' => $json->codigo, 'data' => $json);
				}else{
					
		        }
			}*/
			$sql_ = 'SELECT nombre FROM lp_tramites WHERE id = ?';
			$res = $adb->pquery($sql_,array($idtramite));
			$nombre = $adb->query_result($res, 0, 'nombre');
		

			AtencionPresencial_Record_Model::statusLog($numerocod, $sectorid, $numerofecha, $lugarcod, null, 'Finalizado');
			$recordModel = Vtiger_Record_Model::getInstanceById($at_id, 'AtencionPresencial');
            $recordModel->set('ap_estado', 'Finalizado');
            $recordModel->set('mode', 'edit');
            $recordModel->save();
            $log->info("muestro seleccionid $seleccionid");
            $update = 'UPDATE vtiger_atencionpresencial 
            		   SET ap_tipoconsulta = ?, ap_tramite = ?
            		   WHERE atencionpresencialid = ? ';
           	$rs = $adb->pquery($update,array($seleccionid,$nombre, $at_id));
           	$result = array('success' =>  true, 'result' => $datos);

			$response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit();
		}

		if($modo == 'liberarNumeroLlamado'){
			$log->debug("entre al liberarNumero ");
			$datos = AtencionPresencial_Record_Model::getStatusLogByStatus('Llamado');

			$user_query = "SELECT user_name usuario, equipo, lugcod FROM vtiger_users WHERE id = ? AND deleted = 0 ";
			$result = $adb->pquery($user_query,array($usuarioLogueado) );


			$equipo = $adb->query_result($result, 0, 'equipo');
			$lugarcod = $adb->query_result($result, 0, 'lugcod');
			$usuario = $adb->query_result($result, 0, 'usuario');
			if($datos){
				//$ws = new Ws_AP('https://wsgap-backend-dev.apps.ocpp.bps.net/wsgap-backend/api/rest/');
				$datos = Ws_AP::liberarNumero($lugarcod,$datos->sectorId,$datos->numerocod,$datos->numerofecha,$usuario,$equipo,$datos->hora);
				AtencionPresencial_Record_Model::statusLog($numerocod, $sectorid, $numerofecha, $lugarcod, null, 'Liberado');
				$log->info("estado ".$datos);
			}
			//echo json_encode($datos);
			$result = array('success' =>  true, 'result' => $datos);
			$response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit();
		}
		//Si el puesto cerro por Gap quito los datos del crm
		if($modo == 'liberarPuesto'){
			$update_equipo = "UPDATE vtiger_users SET equipo = NULL, lugcod = NULL where id = ?";
			$res = $adb->pquery($update_equipo,array($usuarioLogueado));
			$result = array('success' =>  true, 'result' => 'OK');
			$response = new Vtiger_Response();
	        $response->setResult($result);
	        $response->emit();
		}

	}
}